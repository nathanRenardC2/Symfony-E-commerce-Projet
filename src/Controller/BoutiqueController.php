<?php

namespace App\Controller;

use App\Service\BoutiqueService;
use App\Service\PanierService;
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

    public function rayon(BoutiqueService $boutique, int $idCategorie): Response
    {
        $produits = $boutique->findProduitsByCategorie($idCategorie);
        $categorie = $boutique->findCategorieById($idCategorie);
        $nbProduits = sizeOf($produits);
        return $this->render('boutique/rayon.html.twig', 
        [
            'controller_name' => 'BoutiqueController',
            'produits' => $produits,
            'categorie' => $categorie,
            'nbProduits' => $nbProduits,
        ]);
    }

    public function ajouterProduit(PanierService $panier, int $idProduit, int $quantite = 1): Response
    {
        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('boutique');
    }
}
