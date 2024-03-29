<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Usager;
use App\Repository\ProduitRepository;
use App\Repository\UsagerRepository;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="app_default")
     */
    public function index(PanierService $panier, ProduitRepository $prodRepo): Response
    {
        // On récupère le contenu du panier en session
        $contenuSession = $panier->getContenu();
        $listeProduit = [];
        $total = 0;
        // On récupère les produits correspondants
        foreach ($contenuSession as $element) {
            $idProduit = $element['idProduit'];
            $produit = $prodRepo->find($idProduit);

            if(!$produit)
            {
                throw $this->createNotFoundException("Le produit n'existe pas");
            }

            $quantite = $element['quantite'];
            $listeProduit[$idProduit] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'total' => $quantite * $produit->getPrix(),
            ];
            // On calcule le total
            $total += $quantite * $produit->getPrix();
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
        // On ajoute le produit au panier
        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    public function enlever(PanierService $panier, int $idProduit, int $quantite = 1): Response
    {
        // On enleve le produit au panier
        $panier->enleverProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    public function supprimer(PanierService $panier, int $idProduit): Response
    {
        // On supprime le produit du panier
        $panier->supprimerProduit($idProduit);
        $this->addFlash('success', 'Produit supprimé du panier avec succès');
        return $this->redirectToRoute('panier_index');
    }

    public function vider(PanierService $panier): Response
    {
        // On vide le panier
        $panier->vider();
        return $this->redirectToRoute('panier_index');
    }

    public function validation(PanierService $panier, UsagerRepository $usagerRepository): Response
    {
        $userConnected =  $this->getUser();
        // On envoie le panier en commande
        $panier->panierToCommande($userConnected);
        $all_commande = $this->getDoctrine()->getRepository(Commande::class)->findBy(['usager' => $userConnected]);
        $res = array();
        // On récupère les produits correspondants
        foreach ($all_commande as $commande) {
            $nbproduits = 0;
            $montant = 0;
            $ligneCommandes = $this->getDoctrine()->getRepository(LigneCommande::class)->findBy(['commande' => $commande]);
            foreach($ligneCommandes as $ligneCommande){
                $nbproduits += $ligneCommande->getQuantite();
                $montant += $ligneCommande->getQuantite() * $ligneCommande->getProduit()->getPrix();
            }
            $res[] = array(
                'commande' => $commande,
                'nbProduits' => $nbproduits,
                'montant' => $montant
            );
        }
        
        // On récupère le contenu du panier en session
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'PanierController',
            'commandes' => $res,
        ]);

    }
}
