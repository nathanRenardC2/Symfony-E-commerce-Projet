<?php

// src/Service/PanierService.php
namespace App\Service;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\BoutiqueService;
// Service pour manipuler le panier et le stocker en session
class PanierService {
    ////////////////////////////////////////////////////////////////////////////
    const PANIER_SESSION = 'panier'; // Le nom de la variable de session du panier
    private $session;  // Le service Session
    private $boutique; // Le service Boutique
    private $panier;   // Tableau associatif idProduit => quantite
	                     //  donc $this->panier[$i] = quantite du produit dont l'id est $i
    // constructeur du service
    public function __construct(SessionInterface $session, BoutiqueService $boutique) {
        // Récupération des services session et BoutiqueService
        $this->boutique = $boutique;
        $this->session  = $session;
        // Récupération du panier en session s'il existe, init. à vide sinon
        $this->panier =  $this->panier = $this->session->has(self::PANIER_SESSION) ? $this->session->get(self::PANIER_SESSION) : [];
    }
    // getContenu renvoie le contenu du panier
    //  tableau d'éléments [ "produit" => un produit, "quantite" => quantite ]
    public function getContenu() { 
        return $this->session->get(self::PANIER_SESSION, array());
    }
    // getTotal renvoie le montant total du panier
    public function getTotal() { 
        $total = 0;
        $contenu = $this->getContenu();
        foreach ($contenu as $element) {
            $idProduit = $element["idProduit"];
            $quantite = $element["quantite"];
            $produit = $this->boutique->findProduitById($idProduit);
            $total += $produit->prix * $quantite;
        }
        return $total;
    }
    // getNbProduits renvoie le nombre de produits dans le panier
    public function getNbProduits() {
        $nbProduits = 0;
        $contenu = $this->getContenu();
        foreach ($contenu as $element) {
            $nbProduits += $element["quantite"];
        }
        return $nbProduits;
    }
    // ajouterProduit ajoute au panier le produit $idProduit en quantite $quantite 
    public function ajouterProduit(int $idProduit, int $quantite = 1) {
        $contenu = $this->getContenu();
        $trouve = false;
        foreach ($contenu as $i => $element) {
            if ($element["idProduit"] == $idProduit) {
                $contenu[$i]["quantite"] += $quantite;
                $trouve = true;
            }
        }
        if (!$trouve) {
            $contenu[] = ["idProduit" => $idProduit, "quantite" => $quantite];
        }
        $this->session->set(self::PANIER_SESSION, $contenu);
    }
    // enleverProduit enlève du panier le produit $idProduit en quantite $quantite 
    public function enleverProduit(int $idProduit, int $quantite = 1) {
        $contenu = $this->getContenu();
        foreach ($contenu as $i => $element) {
            if ($element["idProduit"] == $idProduit) {
                $contenu[$i]["quantite"] -= $quantite;
                if ($contenu[$i]["quantite"] <= 0) {
                    unset($contenu[$i]);
                }
            }
        }
        $this->session->set(self::PANIER_SESSION, $contenu);
    }
    // supprimerProduit supprime complètement le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) { 
        $contenu = $this->getContenu();
        foreach ($contenu as $i => $element) {
            if ($element["idProduit"] == $idProduit) {
                unset($contenu[$i]);
            }
        }
        $this->session->set(self::PANIER_SESSION, $contenu);
    }
    // vider vide complètement le panier
    public function vider() { 
        $this->session->remove(self::PANIER_SESSION);
    }
}
