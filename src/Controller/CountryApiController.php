<?php


namespace App\Controller;


use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CountryApiController extends AbstractController
{
    /**
     * @Route("country/show", name="country_show", methods={"GET"})
     */
    public function view(EntityManagerInterface $entityManager) {
        $repository = $entityManager->getRepository(Country::class);
        $countries = $repository->findAll();
        return new JsonResponse(['countries' => $countries]);
    }

    /**
     * @Route("country/add", name="country_add", methods={"POST"})
     */
    public function add(EntityManagerInterface $entityManager) {
        $request = Request::createFromGlobals();
        $countryName = $request->get("name");
        $countryCapital = $request->get("capital");
        $country = new Country();
        $country->setName($countryName);
        $country->setCapital($countryCapital);
        $entityManager->persist($country);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Country added']);
    }

}