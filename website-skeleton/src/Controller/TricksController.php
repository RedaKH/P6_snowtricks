<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TricksController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(): Response
    {
        return $this->render('tricks/dashboard.html.twig', [
            'controller_name' => 'TricksController'
        ]);
    }

    /**
     * @Route("/maketrick", name="make_trick")
     */

     public function makeTrick():Response
     {
         return $this->render('tricks/maketrick.html.twig',['controller_name'=>'TricksController']);

     }
}
