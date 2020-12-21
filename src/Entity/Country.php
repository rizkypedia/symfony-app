<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Country
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $capital;

    public function getId():?int
    {
        return $this->id;
    }

    public function getName():?string
    {
        return $this->name;
    }

    public function getCapital():?string
    {
        return $this->capital;
    }

    public function setName(string $name):self
    {
        $this->name = $name;
        return $this;
    }

    public function setCapital(string $capital):self
    {
        $this->capital = $capital;
        return $this;
    }

}