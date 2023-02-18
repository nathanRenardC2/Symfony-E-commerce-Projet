<?php

namespace App\Controller;

use App\Entity\LigneCommande;
use App\Extension\DeviseExtension;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/default", name="app_default")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    public function devise(DeviseExtension $deviseExtension, string $devise): Response
    {
        $devise = $deviseExtension->setDevise($devise);

        return $this->render('devise.html.twig', [
            'controller_name' => 'DefaultController',
            'devise' => $devise,
        ]);

    }

    public function navBar(PanierService $panier): Response
    {
        $nbProduit = $panier->getNbProduits();

        return $this->render('navbarproduit.html.twig', [
            'controller_name' => 'DefaultController',
            'nbProduit' => $nbProduit,
        ]);
    }

    public function mostSold()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(LigneCommande::class)->findTopOrdered();
    
        return $this->render('mostSold.html.twig', [
            'products' => $products,
        ]);
    }
}
