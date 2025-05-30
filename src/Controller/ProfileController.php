<?php

namespace App\Controller;

use App\Domain\Badgr;
use App\Domain\ImageManager;
use App\Domain\LanguageTrait;
use App\Entity\Chapter;
use App\Entity\Image;
use App\Entity\LearningModule;
use App\Entity\PwdResetToken;
use App\Entity\User;
use App\Entity\UserChapter;
use App\Entity\Language;
use App\Entity\ChapterPage;
use App\Domain\FlaggingManager;
use App\Entity\TimeOfConnexion;
use App\Form\EditProfileType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    use LanguageTrait;
    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function index(Request $request, Swift_Mailer $mailer): Response
    {

        /** @var User $user */
        $user = $this->getUser();

        //get modules
        $modules = $this->getDoctrine()->getRepository(LearningModule::class)->findBy(['isPublished' => true]);

        // Badge : get all badges from user and put all badge keys in userBadges
        $badges = $user->getBadges()->getValues();
        $badgeKeys = [];
        foreach ($badges as &$badgeData) {
            $badgeKey = $badgeData->getBadge();
            $badgeKeys[] = $badgeKey;
        }
        $badgrHandler = new Badgr;
        // try {
        //     $userBadges = $badgrHandler->getAllBadges($badgeKeys, $user);
        // }
        // catch(ClientException $e) {
        //     //nothing happens, badge was not configured correctly.
        //     $this->addFlash('error', 'You completed a module with a broken badge. Please contact Learning2Gether to fix this.');
        // }

        // Edit-profile
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Avatar : change user's avatar only if user upload a new avatar
            if ($request->files->get('edit_profile')['avatar']) {
                $this->UpdateUserAvatar($user, $request);
            }
            $this->flushUpdatedUser($user);
            $this->addFlash('info', 'Modification enregister');

            return $this->redirectToRoute('profile');
        }

        // Delete-account
        $deleteBtn = $this->createFormBuilder()
            ->add('delete_user', SubmitType::class, [
            'row_attr'=> ['id' => 'delete-account-btn']])
            ->getForm();
        $deleteBtn->handleRequest($request);

        if ($deleteBtn->isSubmitted() && $deleteBtn->isValid()) {

            return $this->deleteUser($mailer);
        }

        return $this->render('profile/index.html.twig', [
            'language' => $this->getLanguage($request),
            'badgeKeys' => $badges,
            // 'userBadges' => $userBadges,
            'user' => $user,
            'profileForm' => $form->createView(),
            'deleteBtn' => $deleteBtn->createView(),
            'modules' => $modules
        ]);
    }



    /**
     * @param User $user
     */
    public function flushUpdatedUser(User $user): void
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * @param User $user
     * @param Request $request
     */
    public function UpdateUserAvatar(User $user, Request $request): void
    {
        $imageManager = new ImageManager();
        $imageManager->fixUploadsFolder($this->getParameter('uploads_directory'), $this->getParameter('public_directory'));
        $avatarImage = $this->getDoctrine()->getRepository(Image::class)->findOneBy(['type' => 'avatar', 'user' => $user->getId()]);
        // check if there was an previous image for that user
        if (!$user->getAvatar()) {
            $newImage = $imageManager->createImage($request->files->get('edit_profile')['avatar'], $user, $this->getParameter('uploads_directory'), 'avatar');
            $user->setAvatar($newImage->getSrc());
            $this->getDoctrine()->getManager()->persist($newImage);
        } else {
            $user = $imageManager->changeUserAvatarImage($request->files->get('edit_profile')['avatar'], $avatarImage, $user, $this->getParameter('uploads_directory'));
        }
    }

    /**
     * @param Swift_Mailer $mailer
     * @return RedirectResponse
     */
    public function deleteUser(Swift_Mailer $mailer): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $imageManager = new ImageManager();
        /* @var User $user */
        $user = $this->getUser();
        $userEmail = $user->getEmail();
        $this->get('security.token_storage')->setToken(null);
        $userProgress = $user->getProgress();
        foreach ($userProgress as $item) {
            $user->removeProgress($item);
        }
        $userImages = $this->getDoctrine()->getRepository(Image::class)->findBy(['user' => $user]);
        foreach ($userImages as $userImage) {
            $imageManager->removeUpload($userImage->getSrc(), $this->getParameter('uploads_directory'));
        }
        $userPwdTokens = $this->getDoctrine()->getRepository(PwdResetToken::class)->findBy(['user' => $user]);
        if (!empty($userPwdTokens)) {
            foreach ($userPwdTokens as $userPwdToken) {
                $em->remove($userPwdToken);
            }
        }
        $this->sendDeleteEmail($userEmail, $mailer);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('portal');
    }

    public function sendDeleteEmail(string $userEmail, Swift_Mailer $mailer): void
    {
        $message = (new Swift_Message('User deleted confirmation'))
            ->setFrom('contact@abyformation.fr')
            ->setTo($userEmail)
            ->setBody($this->renderView('profile/dltmail.html.twig'), 'text/html');
        $mailer->send($message);
    }





    /**
     * @Route("profile/user/history", name="user_history")
     * @return Response
     */
    public function history(Request $request): Response
    {
        $language = $this->getLanguage($request);
        $languageCount = $this->getDoctrine()->getRepository(Language::class)->getLanguageCount();

        $pr = $this->getDoctrine()->getRepository(ChapterPage::class);
        $fm = new FlaggingManager($languageCount);

        $id = $this->getUser()->getId();


        $history = $this->getDoctrine()->getRepository(TimeOfConnexion::class)->findBy(['user_id' => $id], ['toDay' => 'DESC']);
        $dateOfConnexion= [];
        
        // get all date of connexion  
        foreach($history as $item){

            $day = $item->getToday()->format('Y-m-d');
            $positionOfItem = array_search($day, $dateOfConnexion);

            if($positionOfItem !== 0){

                $dateOfConnexion[]=$day;

            }
            
        }

        // all session of co
        $allSession = [];
        $sessionAccordingToADay = [];
        $totalOfConnexionForDay = 0;
        // array with date of connexion and total min of co
        $dayWithTotal = [];

        foreach($dateOfConnexion as $date){

            $detailsSessionForOneDay = [];

            foreach($history as $item){

                $day = $item->getToday()->format('Y-m-d');

                // if the date of item element match with the 
                if( $date == $day ){
                    $detailsForOneSession = [];
                    $detailsForOneSession['time_co'] = $item->getTimeCo();
                    $detailsForOneSession['time_deco'] = $item->getTimeDeco();

                    $dateStart = $item->getTimeCo();
                    $dateFinish = $item->getTimeDeco();
                    $diff = $dateFinish->diff($dateStart);
                    $diff = date_diff($dateStart,$dateFinish);
                    $min = $diff->format('%i');
                    $hour = $diff->format('%H');
                    $min = $min + ($hour * 60);
                    
                    

                    $detailsForOneSession['timeOfCo'] = $min;
                    $totalOfConnexionForDay += $min;
                    
                    // save session info
                    $detailsSessionForOneDay[] = $detailsForOneSession;
                    // $sessionAccordingToADay[$day] = $allSessionForDay;

                }
                if(!empty($detailsSessionForOneDay))
                {
                    $allSession[] = $detailsSessionForOneDay;
                }
    
            }

            $sessionAccordingToADay[$date] = $detailsSessionForOneDay;
            $dayWithTotal[$date] = $totalOfConnexionForDay;
            $totalOfConnexionForDay = 0;

        }

        
        
        //             echo "<pre>";
        //             print_r($sessionAccordingToADay);
        //             print_r($dayWithTotal);
        //             echo "</pre>";

        // exit;
         
        return $this->render('profile/history.html.twig', [
            'fm' => $fm,
            'language' => $language,
            'pr' => $pr,
            'languagecount' => $languageCount,
            'history'=>$sessionAccordingToADay,
            "totalOfConnexionForDay" => $dayWithTotal
        ]);
    }
}
