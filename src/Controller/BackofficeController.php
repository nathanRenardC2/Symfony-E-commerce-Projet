<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\PanierService;

class BackofficeController extends AbstractController
{

    public function index()
    {
        // On récupère toutes les commandes en BDD
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findAll();
        return $this->render('backoffice/index.html.twig', [
            'controller_name' => 'BackofficeController',
            'commandes' => $commandes
        ]);
    }

    public function valide(Request $request)
    {
        $id = $request->attributes->get('idCommande');
        $commande = $this->getDoctrine()->getRepository(Commande::class)->findOneBy(['id' => $id]);

        if (!$commande) {
            throw $this->createNotFoundException('La commande n\'existe pas');
        }
    
        if ($request->isMethod('POST')) {
            $commande->setStatut("Validée");
            $em = $this->getDoctrine()->getManager();
            $em->persist($commande);
            $em->flush();
        }
    
        return $this->redirectToRoute('backoffice_index');
    }

    public function refuse(Request $request)
    {
        $id = $request->attributes->get('idCommande');
        $commande = $this->getDoctrine()->getRepository(Commande::class)->findOneBy(['id' => $id]);
        if (!$commande) {
            throw $this->createNotFoundException('La commande n\'existe pas');
        }
    
        if ($request->isMethod('POST')) {
            $commande->setStatut("Refusée");
            $em = $this->getDoctrine()->getManager();
            $em->persist($commande);
            $em->flush();
        }
    
        return $this->redirectToRoute('backoffice_index');
    }

}
