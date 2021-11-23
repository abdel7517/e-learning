<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Chapter;
use App\Entity\Language;
use App\Entity\ChapterPage;
use App\Domain\LanguageTrait;
use App\Domain\FlaggingManager;
use App\Entity\TimeOfConnexion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardUserController extends AbstractController
{
    use LanguageTrait;
    private $security;
    public function  __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @Route("partner/dashboard/user/home/{all}", name="dashboard_user")
     * @return Response
     */
    public function index($all = false, Request $request): Response
    {
        $language = $this->getLanguage($request);
        $languageCount = $this->getDoctrine()->getRepository(Language::class)->getLanguageCount();

        $pr = $this->getDoctrine()->getRepository(ChapterPage::class);
        $users = $this->getDoctrine()->getRepository(User::class);
        $fm = new FlaggingManager($languageCount);
        $user = $this->security->getUser();

        if($request->isMethod('POST')){
           $mail =   $request->get('mail');
           $answer = $this->getDoctrine()->getRepository(User::class)->findOneBy([ "email" => $mail, 'market_id' => $user->getId() ]);
            if ($answer != null)
                {
                    return $this->render('dashboard_user/index.html.twig', [
                        'fm' => $fm,
                        'language' => $language,
                        'pr' => $pr,
                        'user' => $answer,
                        'languagecount' => $languageCount,
                    ]);
                }
        }
        
        if($all == true){

            return $this->render('dashboard_user/index.html.twig', [
                'fm' => $fm,
                'language' => $language,
                'pr' => $pr,
                "users" => $users->findBy(['market_id' => $user->getId() ],["start" => "DESC"]),
                'languagecount' => $languageCount,
            ]);
        }
        return $this->render('dashboard_user/index.html.twig', [
            'fm' => $fm,
            'language' => $language,
            'pr' => $pr,
            "users" => $users->findBy(['market_id' => $user->getId() ],["start" => "DESC"],  5),
            'languagecount' => $languageCount,
        ]);
       
        
    }

    
 

     /**
     * @Route("partner/dashboard/user/history/{id}", name="dashboard_user_history")
     * @return Response
     */
    public function history(Request $request,int  $id): Response
    {
        $language = $this->getLanguage($request);
        $languageCount = $this->getDoctrine()->getRepository(Language::class)->getLanguageCount();

        $pr = $this->getDoctrine()->getRepository(ChapterPage::class);
        $fm = new FlaggingManager($languageCount);

        $history = $this->getDoctrine()->getRepository(TimeOfConnexion::class)->findBy(['user_id' => $id], ['toDay' => 'DESC']);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $id]);
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
         
        return $this->render('dashboard_user/history.html.twig', [
            'fm' => $fm,
            'language' => $language,
            'pr' => $pr,
            'languagecount' => $languageCount,
            'history'=>$sessionAccordingToADay,
            "totalOfConnexionForDay" => $dayWithTotal,
            "user" => $user
        ]);
    }

    /**
     * Resorts an item using it's doctrine sortable property
     * @Route("partner/dashboard/chapter/sort/{id}/{position}", name="dashboard_chapter_sort", requirements={"chapter"= "\d+"})
     * @param integer $id
     * @param integer $position
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sortAction($id, $position)
    {
        $em = $this->getDoctrine()->getManager();

        $chapter = $em->getRepository(Chapter::class)->find($id);
        $chapter->setPosition($position);

        $em->persist($chapter);
        $em->flush();

        return new Response(
            'All ok!',
            Response::HTTP_OK
        );
    }

    /**
     * @Route("delete/{id}", name="dashboard_user_delete")
     */
    public function delete(int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('dashboard_user');
    }


    public function getHistory($id)
    {
       /* $language = $this->getLanguage($request);
        $languageCount = $this->getDoctrine()->getRepository(Language::class)->getLanguageCount();

        $pr = $this->getDoctrine()->getRepository(ChapterPage::class);
        $fm = new FlaggingManager($languageCount);*/

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
        $totalCo = 0;
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
                    $totalCo += $min;
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

        return $totalCo;        
       
    }

}
