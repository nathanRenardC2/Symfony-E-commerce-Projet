<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Usager;
use App\Entity\Commande;
use App\Entity\LigneCommande;

class CommandeController extends AbstractController
{

    public function index(): Response
    {
        // On récupère l'utilisateur connecté
        $userConnected =  $this->getUser();
        // On récupère toutes les commandes de l'utilisateur
        $all_commande = $this->getDoctrine()->getRepository(Commande::class)->findBy(['usager' => $userConnected]);
        $res = array();
        // On récupère les lignes de commandes de chaque commande
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

        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
            'commandes' => $res
        ]);
    }
}

?>