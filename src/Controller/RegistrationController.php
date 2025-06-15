<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        
        // Initialiser les valeurs par défaut
        $user->setPoints(0);
        $user->setActif(true);
        $user->setRoles(['ROLE_USER']);
        
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encoder le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // S'assurer que les valeurs par défaut sont bien définies
            if ($user->getPoints() === null) {
                $user->setPoints(0);
            }
            
            if ($user->isActif() === null) {
                $user->setActif(true);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger vers la page d'accueil avec un message de succès
            $this->addFlash('success', 'Votre compte a été créé avec succès ! Bienvenue !');
            
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}