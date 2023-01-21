<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
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
    public function boutique(CategorieRepository $catRepo): Response
    {
        $categories = $catRepo->findAll();

        if(!$categories)
        {
            throw $this->createNotFoundException("La boutique n'existe pas");
        }

        return $this->render('boutique/boutique.html.twig', 
        [
            'controller_name' => 'BoutiqueController',
            'categories' => $categories,
        ]);
    }

    public function rayon(CategorieRepository $catRepo, int $idCategorie): Response
    {
        $categorie = $catRepo->find($idCategorie);

        if(!$categorie)
        {
            throw $this->createNotFoundException("La catégorie n'existe pas");
        }


        $produits = $categorie->getProduits();
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
