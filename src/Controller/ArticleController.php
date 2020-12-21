<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ArticleController extends AbstractController
{
    /**
     * @Route("/home", name="homepage")
     */
    public function homepage() {
        return $this->render('article/homepage.html.twig', [
            'title' => ucwords('homepage')
        ]);
    }

}