<?php

namespace App\Controller;

use App\Form\CommentType;
use App\Entity\Comments;
use App\Repository\CommentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CommentsController extends AbstractController
{
    /**
     * @Route("/comments", name="app_comments")
     */
    public function AddComments(Request $request): Response
    {
        $comments = new Comments();
        $form = $this->createForm(CommentType::class, $comments);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $comments->setCreatedAt(new \DateTimeImmutable());
            $comments->setUser($this->getUser());
            $comments->setTricks($this->getTricks());


            $this->em->persist($comments);
            $this->em->flush();
            $this->addFlash('success', 'Votre tricks a bien été envoyé');


        }
        return $this->render('comments/comments.html.twig', ['form' => $form->createView()]);

      

   
    
    }

     

     /**
     * @Route("/comments", name="app_comments")
     */
     public function pagination(CommentsRepository $commentsRepo, Request $request): Response
     {
         $limit=5;
         $page = (int)$request->query->get("page", 1);
        $comments= $commentsRepo->getPage($page,$limit);
        $total = $commentsRepo->getTotalComments();


        return $this->render('comments/comments.html.twig', ['comment'=>$comments,'total'=>$total,'limit'=>$limit,'page'=>$page]);
     }

    
}
