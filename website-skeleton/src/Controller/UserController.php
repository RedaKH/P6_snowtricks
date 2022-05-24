<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\editUserType;
use App\Form\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
   
    /**
     * @Route("dashboard/update_profile", name="update_profile")
     */
    public function updateProfile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(editUserType::class,$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        
            $avatar = $form->get('avatar')->getData();
            if ($avatar) {
                $fichier = md5(uniqid()) . '.' . $avatar->guessExtension();
 
                $avatar->move(
                    $this->getParameter('images_directory'),
                    $fichier, 
                );
                $user->setAvatar($fichier);

            }

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('msg', 'Votre profil a bien été modifié');


            return $this->redirectToRoute('dashboard');
        }

        return $this->render('user/update_profile.html.twig',['form'=>$form->createView()]);
}

/**
 * @Route("/dashboard", name="dashboard")
 */
public function dashboard(): Response
{
    return $this->render('user/dashboard.html.twig');
}
}
