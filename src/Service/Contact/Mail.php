<?php

namespace App\Service\Contact;

use App\Form\ContactType;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Mail extends AbstractController
{
    private $request, $mailer;
    public function __construct(\Swift_Mailer $mailer){
        // $this->request = $request;
        $this->mailer = $mailer;
    }
   
    public function notifUser(string $name ,string $mail ,string $mdp, string $formation)
    {
        // $form = $this->createForm(ContactType::class);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
            // $contact = $form->getData();
            $contact = [ 'name' => $name, 'mail'=> $mail, 'mdp'=> $mdp, 'formation' => $formation ];
            // On crée le message
            $message = (new \Swift_Message(' Votre formation commence '))
                // On attribue l'expéditeur
                ->setFrom("contact@ifpro-france.fr")
                // On attribue le destinataire
                ->setTo($mail)
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'contact/mail.html.twig', compact('contact')
                    ),
                    'text/html'
                );
            try {
                $this->mailer->send($message);
            } catch (\Throwable $th) {
                throw $th;
            }

            // $this->addFlash('message', 'Votre message a été transmis, nous vous répondrons dans les meilleurs délais.'); // Permet un message flash de renvoi
        // }
        // return $this->render('contact/index.html.twig',['contactForm' => $form->createView()]);
    }

    public function setPass(string $mail ,string $link)
    {
        // $form = $this->createForm(ContactType::class);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
            // $contact = $form->getData();
            $contact = ['link' => $link];
            // On crée le message
            $message = (new \Swift_Message('Modification de votre MDP '))
                // On attribue l'expéditeur
                ->setFrom("contact@staffdeco.fr")
                // On attribue le destinataire
                ->setTo($mail)
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'contact/mailPass.html.twig', compact('contact')
                    ),
                    'text/html'
                )
            ;
            $this->mailer->send($message);

            // $this->addFlash('message', 'Votre message a été transmis, nous vous répondrons dans les meilleurs délais.'); // Permet un message flash de renvoi
        // }
        // return $this->render('contact/index.html.twig',['contactForm' => $form->createView()]);
    }

    public function notifAdmin(){
        $contact = ['link' => 'https://boutique.staffdeco.fr/admin/'];
        // On crée le message
        $message = (new \Swift_Message('Nouvelle Commande'))
            // On attribue l'expéditeur
            ->setFrom("contact@staffdeco.com")
            // On attribue le destinataire
            ->setTo('contact@staffdeco.com')
            // On crée le texte avec la vue
            ->setBody(
                $this->renderView(
                    'contact/newOrder.html.twig', compact('contact')
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }


}