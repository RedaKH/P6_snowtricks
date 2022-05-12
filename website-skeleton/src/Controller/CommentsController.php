<?php

namespace App\Controller;

use App\Form\CommentType;
use App\Entity\Comments;
use App\Entity\Tricks;
use App\Entity\User;
use App\Repository\CommentsRepository;
use App\Repository\TricksRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class CommentsController extends AbstractController
{
    /**
     * @Route("/tricks/{id}", name="show_trick",requirements={"id"="\d+"})
     */
     public function AddComments(Request $request, TricksRepository $tricksRepository, EntityManagerInterface $em, UserRepository $userepo): Response
    {
        $commentData = $request->request->all('comment');

        $tricks = $tricksRepository->findOneBy(['id'=>$commentData['tricks']]);

      
        $comment = new Comments();
        $comment->setComment($commentData['comment']);
        $comment->setUser($userepo->findOneBy(['id'=>1]));
        $comment->setCreatedAt(new \DateTime());

        $em->persist($comment);
        $em->flush();

        return $this->render('comments/comments.html.twig', ['comment'=>$comment]);

    } 



    /**
     * @Route("/tricks/{id}", name="show_trick", methods={"GET"})
     * 
     */
    public function pagination(CommentsRepository $repo, Request $request,Tricks $tricks): Response
    {
        $limit = 5;
        $page = $request->query->getInt('page', 1);

        $tricks = $repo->getPaginatedComments($page,$limit);

        $total = $repo->getTotalComments();



        return $this->render('comments/comments.html.twig', ['page'=>$page,'tricks'=>$tricks,'total'=>$total,'limit'=>$limit]);
    } 


}
