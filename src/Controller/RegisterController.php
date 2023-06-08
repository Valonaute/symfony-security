<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'inscription')]
    public function index(Request $request, UserPasswordHasherInterface $encoder, UserRepository $userRepository)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // encodage du mot de passe
            $password_hashed = $encoder->hashPassword($user, $user->getPassword());

            // Insertion du mot de passe crypté
            $user->setPassword($password_hashed);

            $userRepository->save($user, $flush = true);

            $this->addFlash('success', 'Votre compte a bien été créé');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()]);
    }
}
