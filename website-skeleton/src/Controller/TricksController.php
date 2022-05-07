<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Tricks;
use App\Entity\User;
use App\Form\AddTrickType;
use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TricksController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

   

    /**
     * @Route("/dashboard/maketrick", name="make_trick",methods={"GET","POST"})
     */

    public function makeTrick(Request $request): Response
    {
        $tricks = new Tricks();
        $user = new User();
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

            $tricks->setCreatedAt(new \DateTimeImmutable());
            $user->addTrick($tricks);
            $this->em->persist($tricks);
            $this->em->flush();
            $this->addFlash('success', 'Votre tricks a bien été envoyé');


        }
        return $this->render('tricks/maketrick.html.twig', ['form' => $form->createView()]);

   
    }


   

    /**
     * @Route("update_trick/{id}", name="update_trick")
     */
    public function update(Tricks $tricks, Request $request): Response
    {
        
        $form = $this->createForm(AddTrickType::class,$tricks);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            
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

            $tricks->setCreatedAt(new \DateTimeImmutable());
            $this->em->persist($tricks);
            $this->em->flush();
            $this->addFlash('success', 'Votre tricks a bien été modifié');


            return $this->redirectToRoute('app_home');
        }

        return $this->render('tricks/update.html.twig',['tricks'=>$tricks,'form'=>$form->createView()]);
       
    }

    /**
     * @Route("Route", name="RouteName")
     */
    public function delete($id): Response
    {
        $tricks = $this->tricksRepository->find($id);
        $this->em->remove($tricks);
        $this->em->flush();

        return $this->redirectToRoute('app_home');
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
            $manyimg=$image->getName();
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
     * @Route("/tricks/{id}", name="show_trick",requirements={"id"="\d+"})
     */
    public function showTrick(TricksRepository $tricksrepo,$id): Response
    {
        $tricks = $tricksrepo->find($id);
        $category = new Category();
        if (!$tricks) {
            throw new NotFoundHttpException();

        }


        return $this->render('tricks/showtrick.html.twig', [
            'tricks'=>$tricks,
            'category'=>$category
        ]);
    }
}
