<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TricksRepository;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {

        return $this->render('home/index.html.twig');
    }

     /**
     * @Route("/", name="app_home")
     */
    public function showAllTricks(TricksRepository $tricksrepo): Response
    {
        $tricks = $tricksrepo->findAll();

        return $this->render('home/index.html.twig',['tricks'=>$tricks]);
    }
    /**
     * @Route("/", name="app_home")
     */
    public function paginateTricks(TricksRepository $tricksrepo, Request $request): Response
    {
        $limit = 5;
        $page = $request->query->getInt('page', 1);

        $tricks = $tricksrepo->getPaginatedTricks($page,$limit);

        $total = $tricksrepo->getTotalTricks();



        return $this->render('home/index.html.twig', ['page'=>$page,'tricks'=>$tricks,'total'=>$total,'limit'=>$limit]);
    }
}
