<?php


namespace App\Controller\Api;

use App\Entity\Country;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class CountryController extends AbstractFOSRestController
{
    /**
     * List all Countries
     * @Rest\Get("/countries")
     *
     * @return Response
     */
    public function getCountryAction(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Country::class);
        $countries = $repository->findAll();
        return $this->handleView($this->view($countries));
    }

}
