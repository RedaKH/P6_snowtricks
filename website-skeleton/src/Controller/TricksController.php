<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Tricks;
use App\Entity\User;
use App\Entity\Comments;
use App\Entity\Video;
use App\Form\AddTrickType;
use App\Form\CommentType;
use App\Repository\TricksRepository;

use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentsRepository;

class TricksController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="app_home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, TricksRepository $tricksrepo): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit=10;
        $alltricks = count($tricksrepo->findAll());
        $pagination = ceil($alltricks / $limit);
        $pagination = ($pagination == 0) ? 1 : $pagination;
        $page = ($page > $pagination || $page == 0) ? 1 : $page;
        $tricks = $tricksrepo->pagination($limit, $page);





        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'pagination' => $pagination,
            'page' => $page
        ]);
    }



    /**
     * @Route("/dashboard/maketrick", name="make_trick",methods={"GET","POST"})
     */

    public function makeTrick(Request $request): Response
    {
        $user = new User();
        $tricks = new Tricks();
        $form = $this->createForm(AddTrickType::class, $tricks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $mainimage = $form->get('featimg')->getData();
            if ($mainimage) {
                $fichier = md5(uniqid()) . '.' . $mainimage->guessExtension();

                $mainimage->move(
                    $this->getParameter('images_directory'),
                    $fichier,
                );
                $tricks->setFeatImg($fichier);
            }
            $manyimg = $form->get('name')->getData();



            foreach ($manyimg as $image) {

                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Image();
                $img->setName($fichier);


                $tricks->addImage($img);
            }

            $tricks->setCreatedAt(new \DateTime());
            $tricks->setUser($this->getUser());
            $this->em->persist($tricks);
            $this->em->flush();
            $this->addFlash('success', 'Votre tricks a bien été envoyé');
        }
        return $this->render('tricks/maketrick.html.twig', ['form' => $form->createView()]);
    }




    /**
     * @Route("dashboard/update/{id}", name="update_trick")
     */

    public function update(Tricks $tricks,$id,VideoRepository $video, Request $request): Response
    {

        $form = $this->createForm(AddTrickType::class, $tricks);


        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $mainimage = $form->get('featimg')->getData();
            if ($mainimage) {
                $fichier = md5(uniqid()) . '.' . $mainimage->guessExtension();

                $mainimage->move(
                    $this->getParameter('images_directory'),
                    $fichier,
                );
                $tricks->setFeatImg($fichier);
            }
            $manyimg = $form->get('name')->getData();



            foreach ($manyimg as $image) {

                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                $img = new Image();
                $img->setName($fichier);




                $tricks->addImage($img);
            }

           

             
            $tricks->setCreatedAt(new \DateTime());
            $tricks->setUser($this->getUser());
            $this->em->flush();
            $this->addFlash('success', 'Votre tricks a bien été modifié');


            return $this->redirectToRoute('app_home');
        }

        return $this->render('tricks/update.html.twig', ['tricks' => $tricks, 'form' => $form->createView()]);
    } 

  

  


    /**
     * @Route("/supprime/image/{id}", name="tricks_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Image $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            // On récupère le nom de l'image
            $manyimg = $image->getName();
            // On supprime le fichier
            unlink($this->getParameter('images_directory') . '/' . $manyimg);


            $this->em->remove($image);
            $this->em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    /**
     * @Route("/delete/video/{id}", name="remove_video", methods={"DELETE"})
     */
    public function deleteVideo(Video $video,Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $video->getId(), $data['_token'])) {
            
        $video->getUrl();

            $this->em->remove($video);
            $this->em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }


    


    /**
     * @Route("/tricks/{id}", name="show_trick")
     */
    public function showTrick(TricksRepository $tricksrepo, $id, CommentsRepository $repo, Request $request, EntityManagerInterface $em, UserRepository $userepo): Response
    {
        $tricks = $tricksrepo->find($id);
        $category = new Category();
        if (!$tricks) {
            throw new NotFoundHttpException();
        }
        $comments = new Comments;
        $commentForm = $this->createForm(CommentType::class, $comments);
        $commentForm->handleRequest($request);


        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comments->setCreatedAt(new \DateTime());
            $comments->setTricks($tricks);
            $comments->setUser($this->getUser());
            $this->em->persist($comments);
            $this->em->flush();
            $this->addFlash('msg', 'votre commentaire a bien été posté');
        }



        $limit = 5;
        $page = $request->query->getInt('page', 1);

        $comments = $repo->getPaginatedComments($page, $limit, $tricks);

        $total = $repo->getTotalComments($tricks);


        return $this->render('tricks/showtrick.html.twig', [
            'tricks' => $tricks,
            'category' => $category,
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
            'comments' => $comments,
            'commentForm' => $commentForm->createView()
        ]);
    }
}
