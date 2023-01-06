<?php

namespace App\Controller;

use App\Service\BoutiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueController extends AbstractController
{
    /**
     * @Route("/boutique", name="app_boutique")
     */
    public function boutique(BoutiqueService $boutique): Response
    {
        $categories = $boutique->findAllCategories();
        return $this->render('boutique/boutique.html.twig', 
        [
            'controller_name' => 'BoutiqueController',
            'categories' => $categories,
        ]);
    }
}
