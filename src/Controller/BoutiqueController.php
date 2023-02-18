<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BoutiqueController extends AbstractController
{
    /**
     * @Route("/boutique", name="app_boutique")
     */
    public function boutique(CategorieRepository $catRepo): Response
    {
        // On récupère les catégories
        $categories = $catRepo->findAll();

        // Si la catégorie n'existe pas, on renvoie une exception
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
        // On récupère la catégorie lié à son id
        $categorie = $catRepo->find($idCategorie);

        if(!$categorie)
        {
            throw $this->createNotFoundException("La catégorie n'existe pas");
        }

        // On récupère les produits de la catégorie
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

    public function rechercheProduit(Request $request, ProduitRepository $prodRepo): Response
    {
        // On récupère la valeur de la recherche dans la requête GET
        $recherche = $request->query->get('recherche');
        
        // On récupère les produits correspondant à la recherche
        $produits = $prodRepo->rechercheProduit($recherche);
        $nbProduits = count($produits);
        
        return $this->render('boutique/recherche.html.twig', [
            'controller_name' => 'BoutiqueController',
            'produits' => $produits,
            'nbProduits' => $nbProduits,
            'recherche' => $recherche,
        ]);
    }

    public function ajouterProduit(PanierService $panier, int $idProduit, int $quantite = 1): Response
    {
        // On ajoute le produit au panier
        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('boutique');
    }
}
