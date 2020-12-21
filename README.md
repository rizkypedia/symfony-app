# WZ-Schlüssel-Redaktionssystem

## Aufsetzen des Projekts

- Container bauen: `build/run.sh build`
- Container starten: `build/run.sh start`
- In den Container wechseln: `build/run.sh cli`
- Abhängigkeiten mit Composer installieren (im Container): `composer install`
- Datenbankmigrationen ausführen (im Container): `bin/console doctrine:migrations:migrate` (mit y bestätigen)
- Initialen Benutzer anlegen (im Container): `bin/console app:create-initial`

Die `build/run.sh`-Befehle sind spezifisch auf die Arbeitsweise der Themenwelt
Wirtschaft angepasst. Sie basieren auf `docker-compose`, das Projekt läuft auch nur
mit diesem Tool. Das Anwenden der korrekten Befehle ist dem Leser als einfache
Aufgabe überlassen.

### Datenbank
Voraussetzung sind laufende Projektcontainer.

**Sichern der aktuellen Datenbank** (aus dem Projekt-Root heraus):  
`source .env && docker exec wz_redaktionssystem-mysql bash -c "mysqldump -u$MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE" > dump.sql`

**Einspielen einer fremden Datenbank**:  
```
$ docker cp dump.sql wz_redaktionssystem-mysql:/
$ docker exec -ti wz_redaktionssystem-mysql bash
root@32f128a9a6c8:/# echo $MYSQL_DATABASE
wz
root@32f128a9a6c8:/# mysql -u$MYSQL_USER -p$MYSQL_PASSWORD
mysql: [Warning] Using a password on the command line interface can be insecure.
[...]

mysql> drop database wz;
Query OK, 6 rows affected (0.06 sec)

mysql> create database wz;
Query OK, 1 row affected (0.00 sec)

mysql> use wz
Database changed

mysql> source /dump.sql
Query OK, 0 rows affected (0.00 sec)

Query OK, 0 rows affected (0.00 sec)

[...]
```
## Deployment
Ein Deployment der Anwendung erfordert eine funktionierende `kubectl`-Konfiguration für das Gridscale-NRW-Cluster.

Die Docker-Registry des Projekts ist unter https://hub.docker.com/repository/docker/publicplan/wz-redaktionssystem/
zu finden.   
Jeder Commit in den Branches `develop` sowie `master` löst einen automatischen Build gegen die Docker-Tags
`develop` bzw. `latest` aus. Diese sollten allerdings nicht zum Deployment verwendet werden. Stattdessen
funktioniert ein Deployment wie folgt:

 - Alle vorzunehmenden Änderungen sind erstellt, getestet(!) und zu Bitbucket gepushed.
 - Der zu deployende Stand ist im git-Branch `develop` oder (besser) `master`.
 - Wechsel in den Branch des zu deployenden Standes.
 - Mit `git tag -l` werden die Release-Tags eingesehen, d.h. git-Tags nach dem Schema `release-[0-9.]`.
 - Basierend auf den bestehenden Versionen und [Semantic Versioning](https://semver.org/) wird eine neue
   Versionsnummer bestimmt.
 - Die Version wird unter *k8s-deployment/300-wzredaktion-deployment.yaml* als Docker-Tag für die Images
   eingetragen. Dies muss an **beiden** Stellen geschehen (derzeit Zeile 26 und 61)!
   Die Versionsnummer wird weiterhin unter *project/resources/restartjob.yaml* eingetragen (Zeile 11).
 - Die Änderung wird als neuer Commit festgehalten und gepusht.
 - Mit `git tag release-VERSION` wird ein neuer git-Tag für die Version erstellt
 - `git push origin release-VERSION` pusht den Tag zu Bitbucket. Dockerhub wird basierend darauf ein
   Image bauen, das dauert etwa zehn bis 15 Minuten. Der aktuelle Status kann auf der Dockerhub-Seite eingesehen werden.
 - Erst **nachdem das Image erfolgreich gebaut wurde** wird die neue Version manuell deployt: 
   `kubectl apply -n wz-redaktionssystem -f k8s-deployment/300-wzredaktion-deployment.yaml`
   Eventuell notwendige Datenbankmigrationen werden beim Deployment automatisch ausgeführt. Bei der Entwicklung ist
   zwecks Abwärtskompatibilität daher darauf zu achten, keine Datenbankfelder zu löschen.

### Vollständiges Deployment
Muss das Projekt vollständig neu deployt werden, reicht es aus den kompletten `k8s-deployment`-Ordner zu deployen
(d.h. `kubectl apply -n wz-redaktionssystem -f k8s-deployment`). Für den Zugriff vom Cluster aus auf die (private)
Docker-Registry wird allerdings das `cloud-docker-com`-Deploy-Secret benötigt. Dies kann aus bestehenden anderen
Namespaces extrahiert und im Namespace des Redaktionssystems neu angewendet werden.  
Alternativ muss ein neues Deploy-Secret angelegt und für das Projekt auf Dockerhub berechtigt werden. 

## Bedienungsanleitung (kurz)

### Benutzerverwaltung
Die Benutzerverwaltung ist selbsterklärend, allerdings nur einem Benutzer mit
entsprechenden Rechten zugänglich (z.B. dem initialen Admin-Benutzer).

### Redaktionssystem
Um einen WZ-Schlüssel zu bearbeiten, im Menü unter WZ-Schlüssel auf Liste klicken,
dann im oberen Formular den WZ-Schlüssel eintippen (Autocomplete sollte helfen) und
auf Bearbeiten klicken. Die Felder ausfüllen und speichern. In der Liste sollte der
Schlüssel nun auftauchen. Wird der Schlüssel erneut bearbeitet, kann die
Bearbeitungshistorie des jeweiligen Eintrags angesehen werden. Ebenso ist der
Historieneintrag unter "Letzte Bearbeitungen" ersichtlich.

Wurden genügend Schlüssel bearbeitet, kann ein Chefredakteur (nicht aber ein Redakteur)
unter "API-Versionen" eine neue Version anlegen. Es kann immer nur eine Version in
Bearbeitung geben. Wird eine Version an die Live-API ausgespielt, ist sie fixiert
und kann nicht mehr bearbeitet werden. Das Ausspielen an die Test-API geht aber ohne
Fixierung der Daten. In der Bearbeitungsmaske einer Version werden die bearbeiteten
Schlüssel für die jeweilige Version freigegeben oder gelöscht. Freigeben bedeutet,
dass mit dem Ausspielen der Version die Inhalte des WZ-Schlüssels festgehalten werden
(d.h. selbst wenn der Schlüssel später noch einmal bearbeitet wird, ändern sich die Daten
der Version nicht). Löschen bedeutet, dass der WZ-Schlüssel nicht länger mit ausgespielt
wird (gleiches Verhalten wie vorher: das Löschen eines Schlüssels verändert vorhergehende
Versionen nicht).

Zusätzlich werden in einer Version auch immer alle Inhalte aller vorherigen Versionen
mit aufgenommen, die nicht in einer nachfolgenden Version verändert wurden (d.h. WZ-Schlüssel
A wird in Version 1 bearbeitet, dann taucht er auch in Version 2 und 3 auf - erst wenn in
Version 4 ein neuer Wert für A gesetzt wird, werden dann diese Inhalte ausgespielt).

Das Freigeben bzw. Löschen erzeugt ebenfalls einen Log-Eintrag (siehe "Letzte Bearbeitungen").

Ist man mit den Inhalten einer Version zufrieden, kann man sie an die einzelnen APIs ausspielen.
Dazu muss die Bearbeitungsmaske der jeweiligen Version aufgerufen und der entsprechende
Button betätigt werden.Die Inhalte sind dann unter http://wz\_redaktionssystem.docker.localhost/export
(für die Live-API) bzw. http://wz_redaktionssystem.docker.localhost/export/test (für die Test-API)
abrufbar. Es ist ebenfalls möglich, eine alte Version erneut an die APIs auszuspielen.
Das Vorgehen ist das Gleiche.
