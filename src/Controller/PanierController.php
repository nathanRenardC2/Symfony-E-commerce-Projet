<?php

namespace App\Controller;

use App\Service\BoutiqueService;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="app_default")
     */
    public function index(PanierService $panier, BoutiqueService $boutique): Response
    {
        $contenuSession = $panier->getContenu();
        $listeProduit = [];
        $total = 0;
        foreach ($contenuSession as $element) {
            $idProduit = $element['idProduit'];
            $produit = $boutique->findProduitById($idProduit);
            $quantite = $element['quantite'];
            $listeProduit[$idProduit] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'total' => $quantite * $produit->prix,
            ];
            $total += $quantite * $produit->prix;
        }

        // On récupère le contenu du panier en session
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'listeProduit' => $listeProduit,
            'total' => $total,
        ]);
    }

    public function ajouter(PanierService $panier, int $idProduit, int $quantite = 1): Response
    {
        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    public function enlever(PanierService $panier, int $idProduit, int $quantite = 1): Response
    {
        $panier->enleverProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    public function supprimer(PanierService $panier, int $idProduit): Response
    {
        $panier->supprimerProduit($idProduit);
        $this->addFlash('success', 'Produit supprimé du panier avec succès');
        return $this->redirectToRoute('panier_index');
    }

    public function vider(PanierService $panier): Response
    {
        $panier->vider();
        return $this->redirectToRoute('panier_index');
    }
}
