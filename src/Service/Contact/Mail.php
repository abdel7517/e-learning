<?php

namespace App\Service\Contact;

use App\Entity\LearningModule;
use App\Form\ContactType;
use Symfony\Component\BrowserKit\Request;
use App\Repository\LearningModuleRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LearningModuleTranslationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Mail extends AbstractController
{
    private $request, $mailer;
    public function __construct(\Swift_Mailer $mailer){
        // $this->request = $request;
        $this->mailer = $mailer;
    }
   
    public function notifUser(string $name ,string $mail ,string $mdp,string $start ,string $end, string $formation)
    {
        // $form = $this->createForm(ContactType::class);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
            // $contact = $form->getData();
            $contact = [ 'name' => $name, 'mail'=> $mail, 'mdp'=> $mdp, 'formation' => $formation, 'start' => $start, 'end' => $end  ];
            // On crée le message
            $message = (new \Swift_Message(' Votre Formation '))
                // On attribue l'expéditeur
                ->setFrom("contact@abyformation.fr")
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
                ->setFrom("contact@abyformation.fr")
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

    public function notif_end($user, $timeOfCo, $hourLog, $market)
    {
        $hourLog = intdiv($timeOfCo, 60);
        $formationRepo = $this->getDoctrine()->getRepository(LearningModule::class)->findOneBy(["id"=> $user->getFormation() ]);
        $co = ['user' => $user, 'timeOfCo' => $timeOfCo, 'hour'=> $hourLog ,'formation' => $formationRepo->getTitle($user->getLanguage()) ];
        // On crée le message
        $message = (new \Swift_Message('Fin de formation'))
            // On attribue l'expéditeur
            ->setFrom("contact@abyformation.fr")
            ->setSubject('Fin de formation')
            // On attribue le destinataire
            ->setTo([$market, "aby.formation.france@gmail.com"])
            // On crée le texte avec la vue
            ->setBody(
                $this->renderView(
                    'contact/endFormation.html.twig', compact('co')
                ),
                'text/html'
            );
            // On crée le message
        $messageUser = (new \Swift_Message('Fin de formation'))
        // On attribue l'expéditeur
        ->setFrom("contact@abyformation.fr")
        ->setSubject('Fin de formation')
        // On attribue le destinataire
        ->setTo([$user->getEmail()])
        // On crée le texte avec la vue
        ->setBody(
            $this->renderView(
                'contact/endFormationUser.html.twig', compact('co')
            ),
            'text/html'
        );
            $this->mailer->send($message);
            $this->mailer->send($messageUser);
    }

    public function notif_dropOut($user, $timeOfCo, $hourLog, $market, $halfOfHourFormation)
    {
        $formationRepo = $this->getDoctrine()->getRepository(LearningModule::class)->findOneBy(["id"=> $user->getFormation()]);
        $co = ['user' => $user, 'timeOfCo' => $timeOfCo,'hour'=>$hourLog, 'formation' => $formationRepo->getTitle($user->getLanguage()), 'halfOfHourFormation' => $halfOfHourFormation ];
        // On crée le message
        $message = (new \Swift_Message('Vous nous manquez'))
            // On attribue l'expéditeur
            ->setFrom("contact@abyformation.fr")
            ->setSubject('Vous nous manquez')
            // On attribue le destinataire
            ->setTo([$user->getEmail()])
            // On crée le texte avec la vue
            ->setBody(
                $this->renderView(
                    'contact/dropOutUser.html.twig', compact('co')
                ),
                'text/html'
            );
            $messageMarket = (new \Swift_Message('Relance necessaire'))
            // On attribue l'expéditeur
            ->setFrom("contact@abyformation.fr")
            ->setSubject('Relance necessaire')
            // On attribue le destinataire
            ->setTo([$market, "aby.formation.france@gmail.com"])
            // On crée le texte avec la vue
            ->setBody(
                $this->renderView(
                    'contact/dropOutMarket.html.twig', compact('co')
                ),
                'text/html'
            );
            $this->mailer->send($messageMarket);
            $this->mailer->send($message);



    }
    public function linkConnexion(string $mail, string $lien){
       
        // $contact = $form->getData();
        $contact = ['mail' => $mail, 'lien' => $lien];
        // On crée le message
        $message = (new \Swift_Message('Lien de connexion'))
            // On attribue l'expéditeur
            ->setFrom("contact@abyformation.fr", "ABY formation")
            // On attribue le destinataire
            ->setTo($mail)
            // On crée le texte avec la vue
            ->setBody(
                $this->renderView(
                    'contact/co.html.twig', compact('contact')
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
    public function linkFormation(string $mail, string $lien){
       
        // $contact = $form->getData();
        $contact = ['mail' => $mail, 'lien' => $lien];
        // On crée le message
        $message = (new \Swift_Message('Lien d\'inscription'))
            // On attribue l'expéditeur
            ->setFrom("contact@abyformation.fr", "ABY Formation")
            // On attribue le destinataire
            ->setTo($mail)
            // On crée le texte avec la vue
            ->setBody(
                $this->renderView(
                    'contact/info.html.twig', compact('contact')
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
    public function linkReset(string $mail, string $lien){
       
        // $contact = $form->getData();
        $contact = ['mail' => $mail, 'lien' => $lien];
        // On crée le message
        $message = (new \Swift_Message('Lien pour changement de mot de passe'))
            // On attribue l'expéditeur
            ->setFrom("contact@abyformation.fr","ABY Formation")
            // On attribue le destinataire
            ->setTo($mail)
            // On crée le texte avec la vue
            ->setBody(
                $this->renderView(
                    'contact/info.html.twig', compact('contact')
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
    
}