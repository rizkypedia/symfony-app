<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController
{
    /**
     * @Route("/show", name="show_result", methods={"GET"})
     */
    public function view() {
        $languages = ['de' => 'Germany', 'nl' => 'Netherlands', 'fr' => 'France', 'id' => 'Indonesia', 'ru' => 'Romania', 'es' => 'Spain'];
        return new JsonResponse(
          json_encode($languages)
        );
    }

    /**
     * @Route("/add", name="add_result", methods={"POST"})
     */
    public function add() {
        $request = Request::createFromGlobals();
        $result = $request->get("result");
        return new JsonResponse(
          ['success' => 'true', 'result' => $result]
        );
    }


}