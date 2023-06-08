<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AdresseType;
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdresseController extends AbstractController
{
    public $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager =  $entityManager;
    }
    #[Route('/adresse', name: 'adresse')]
    public function index(Request $request)
    {
        $adresse = new Adresse();
        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isvalid())
        {
            // insertion dans la bdd
            $this->entityManager->persist($adresse);
            $this->entityManager->flush();

            $this->addFlash('success',"l'adresse a bien été sauvegardé");
            return $this->redirectToRoute('adresses');
        }


        return $this->render('adresse/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/adresses', name: 'adresses')]
    public function showAdresses(AdresseRepository $adresseRepository)
    {
        $adresses = $adresseRepository->findAll();

        return $this->render('adresse/adresses.html.twig',['adresses' => $adresses]);

    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteAction(AdresseRepository $adresseRepository,$id)
    {
        $adresse = $adresseRepository->find($id);

        if($adresse)
        {
            $adresseRepository->remove($adresse, $flush = true);
            $this->addFlash('success',"L'adresse a bien été supprimé"); 
            return $this->redirectToRoute('adresses');
        }

        return $this->redirectToRoute('adresses');

    }

    #[Route('/modify/{id}', name: 'modify')]
    public function modifyAction(AdresseRepository $adresseRepository, Request $request,$id)
    {
        $adresse = $adresseRepository->find($id);
        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $adresseRepository->save($adresse, $flush = true);

            $this->addFlash('success',"l'adresse a bien été modifié");

            return $this->redirectToRoute('adresses');
        }

        return $this->render('adresse/modify.html.twig',['form' => $form->createView()]);
    }
}
