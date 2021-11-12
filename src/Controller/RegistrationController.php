<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Language;
use App\Domain\ImageManager;
use App\Service\Contact\Mail;
use App\Form\RegistrationFormType;
use App\Security\AppAuthAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $mailer;
    private $security;

    public function __construct( Mail $mailer, Security $security){
        $this->mailer = $mailer;
        $this->security = $security;
    }

    /**
     * @Route("/register", name="en_register")
     * IsGranted("ROLE_PARTNER")
     */
    public function registerInEnglish()
    {
        return $this->redirectToRoute('app_register');
    }
    /**
     * @Route("/newUser", name="app_register")
     * IsGranted("ROLE_PARTNER")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthAuthenticator $authenticator): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute($this->getUser()->isPartner() ? 'partner' : 'portal');
        // }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // compare pwd and pwd-repeat
            $pwd = $_POST['registration_form']['plainPassword'];
            $pwdRepeat = $_POST['registration_form']['passwordRepeat'];

            if (!$pwd || !$pwdRepeat || ($pwd != $pwdRepeat)) {
                $this->addFlash('error', 'Enter your password correctly again!');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
  
            $defaultLang = $this->getDoctrine()->getRepository(Language::class)->findOneBy(['code' => 'en']);
            $user->setLanguage($defaultLang);
            // add formation of user 
            $formation = $form->get('formation')->getData();
            $user->setFormation($formation->getId());
            // TODO pass null to database to get automatic timestamp
            $dateTime = new DateTimeImmutable();
            $user->setCreated($dateTime);
            $user->setSessionId(0);
            
            // add market_id
            $market = $this->security->getUser()->getId();
            $user->setMarketId($market);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            // do anything else you need here, like send an email
            $name = $form->get('name')->getData();
            $mail = $form->get('email')->getData();
            $passWord = $form->get('plainPassword')->getData();
            $this->mailer->notifUser($name, $mail, $passWord, $formation->getTitle($defaultLang));
            $this->addFlash('error', 'Le nouvelle utilisateur à été créé avec succés');  
        }
        
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
