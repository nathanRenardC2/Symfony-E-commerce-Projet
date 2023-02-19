<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usager;

class ContactController extends AbstractController
{
    #[Route('/email')]
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
        $email_user = '';

        if($request->request->get('email') !== null){
            $email_user = $request->request->get('email');
        } elseif($this->getUser() !== null){
            $email_user = $this->getUser()->getEmail();
        }

        $email = (new Email())
            ->from($email_user)
            ->to('n.renard38350@gmail.com')
            ->subject($request->request->get('objet'))
            ->html($request->request->get('message'));

        $mailer->send($email);

        $this->addFlash('success', 'Votre message a bien été envoyé !');

        return $this->redirectToRoute('contact');
    }
}
