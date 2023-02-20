<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Form\UsagerType;
use App\Repository\UsagerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Gregwar\CaptchaBundle\Type\CaptchaType;

/**
 * @Route("/{_locale}/usager", requirements={"_locale"="%app.supported_locales%"},
*                              defaults={"_locale"="fr"})
 */
class UsagerController extends AbstractController
{

    public function index(): Response
    {
        // On récupère l’usager authentifié
        $userAuthentificate = $this->getUser();

        return $this->render('usager/index.html.twig', [
            'userAuthentificate' => $userAuthentificate,
        ]);
    }

    public function new(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $usager = new Usager();
        // On crée le formulaire
        $form = $this->createFormBuilder($usager)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('captcha', CaptchaType::class, array(
                'width' => 200,
                'height' => 50,
                'length' => 6,
            ))
            ->getForm();
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère l’entité Usager
            $em = $this->getDoctrine()->getManager();
            $email = $usager->getEmail();
            // Vérifier si l’adresse courriel existe déjà
            $existingUser = $this->getDoctrine()->getRepository(Usager::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('danger', 'Email déjà utilisé. Veuillez en choisir un autre.');
                return $this->redirectToRoute('usager_inscription');
            }
            // Hacher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($usager, $usager->getPassword());
            $usager->setPassword($hashedPassword);
            // Définir le rôle de l’usager qui va être créé
            $usager->setRoles(["ROLE_CLIENT"]);
            $em->persist($usager);
            $em->flush();
        
            return $this->redirectToRoute('usager_accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('usager/new.html.twig', [
            'usager' => $usager,
            'form' => $form,
        ]);
    }

}
