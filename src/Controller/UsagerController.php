<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Form\UsagerType;
use App\Repository\UsagerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsagerController extends AbstractController
{

    public function index(UsagerRepository $usagerRepository): Response
    {
        $userAuthentificate = $usagerRepository->findAll()[0];

        return $this->render('usager/index.html.twig', [
            'userAuthentificate' => $userAuthentificate,
        ]);
    }

    public function new(Request $request, UsagerRepository $usagerRepository): Response
    {
        $usager = new Usager();
        $form = $this->createForm(UsagerType::class, $usager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usagerRepository->add($usager, true);

            return $this->redirectToRoute('usager_accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('usager/new.html.twig', [
            'usager' => $usager,
            'form' => $form,
        ]);
    }

}
