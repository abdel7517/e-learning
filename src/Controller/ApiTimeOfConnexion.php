<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use DateTimeZone;
use App\Entity\User;
use App\Entity\TimeOfConnexion;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


Class ApiTimeOfConnexion extends AbstractController {

   
    /**
     * @Route("co/{user_id}/{time}/{session_id}", name="start_session")
     */
    public function startSession($user_id,  $time,  $session_id){

        $newSession = new TimeOfConnexion();
        $time_good_format = str_replace("_"," ",$time);
        $date = DateTime::createFromFormat('Y-m-d H:i', $time_good_format);

        $newSession->setUserId($user_id);
        $newSession->setTimeCo($date);
        $newSession->setTimeDeco($date);

        $newSession->setSessionId($session_id);
        $newSession->setToDay($date);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($newSession);
        $manager->flush();

        $textResponse = new Response('ok' , 200);


        return $textResponse;
    }

    /**
     * @Route("deco/{user_id}/{time}/{session_id}", name="close_session")
     */
    public function closeSession($user_id,  $time,  $session_id){
        // $date = strtotime($time);
        // $now =  date('d/M/Y h:i:s', $date);
        $sessionRepo = $this->getDoctrine()->getManager()->getRepository(TimeOfConnexion::class)->findOneBy([ 'user_id'=> $user_id, 'session_id'=> $session_id ]);
        $userRepo =  $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy([ 'id'=>$user_id]);

        $actualSessionId = $userRepo->getSessionId();
        $newSessionId = $actualSessionId+1;
        $userRepo->setSessionId($newSessionId);

        $time_good_format = str_replace("_"," ",$time);
        $date = DateTime::createFromFormat('Y-m-d H:i', $time_good_format);
        
        
        $sessionRepo->setTimeDeco($date);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($userRepo);
        $manager->persist($sessionRepo);

        $manager->flush();

        $textResponse = new Response($newSessionId , 200);

        // $this->tokenStorage->setToken();

        return $textResponse;
    }

     /**
     * @Route("presence/{user_id}/{time}/{session_id}", name="presence_session")
     */
    public function presence($user_id,  $time,  $session_id){
        $sessionRepo = $this->getDoctrine()->getManager()->getRepository(TimeOfConnexion::class)->findOneBy([ 'user_id'=> $user_id, 'session_id'=> $session_id ]);
        $manager = $this->getDoctrine()->getManager();
        $message = "présent";

        if($sessionRepo == null){
            $this->startSession($user_id, $time, $session_id);
            $message = "session start";
        }
        else{
            $time_good_format = str_replace("_"," ",$time);
            $now = DateTime::createFromFormat('Y-m-d H:i', $time_good_format);
            $diff = date_diff($sessionRepo->getTimeDeco(), $now);
            $diff_BetweenNowAndLastCo_InMin = $diff->format('%i');

            $lastCoString = date_format($sessionRepo->getTimeDeco(), "Y-m-d");
            $lastCo = DateTime::createFromFormat('Y-m-d', $lastCoString );
            dump($now);
            echo "<br>";
            dump($lastCo);
            $diffForDay = date_diff($lastCo, $now);

            $diff_BetweenNowAndLastCo_InDay = $diffForDay->format('%d');
             echo $diff_BetweenNowAndLastCo_InDay;      
             exit;


          
            

           // check if it the same day because diff for minute give just diff for the same day 
            if( $diff_BetweenNowAndLastCo_InMin <= 20 ){
                $sessionRepo->setTimeDeco($now);
            }else{
                $userRepo =  $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy([ 'id'=>$user_id]);
                $actualSessionId = $userRepo->getSessionId();
                $newSessionId = $actualSessionId+1;
                $userRepo->setSessionId($newSessionId);

                //add 20 min for the last presence 
                // $newTimeDeco = DateTime::createFromFormat('Y-m-d H:i', $date );
                // $newTimeDeco->add(new DateInterval('PT20M'));
                $message = "deco";
        
            }

            $manager->persist($sessionRepo);
            $manager->flush();

        }
        
        $textResponse = new Response($message , 200);


        return $textResponse;
        
    }


     /**
     * @Route("/logerChecker", name="logerChecker")
     */
    // public function logerChecker(){
    //     $numberMore = 0;
    //     $number = 0;
    //     $now = new DateTime();
    //     $manager = $this->getDoctrine()->getRepository(TimeOfConnexion::class);
    //     $sessionRepo = $manager->findByDate($now);
    //     $entityManager = $this->getDoctrine()->getManager();
    //     foreach ($sessionRepo as $session) {
    //         $diff = date_diff($session->getTimeCo(), $session->getTimeDeco());
    //         $min = $diff->format('%i');
    //         $number++;

    //         if($min > 20 ){
    //             $date = DateTime::createFromFormat('Y-m-d H:i', date());
    //             $session->setTimeDeco($date);
    //             $numberMore++;
    //             $entityManager->persist($session);       

    //         }
    //     }
    //     $entityManager->flush();

    //     $textResponse = new Response("Less : ".$number." ------ more : " . $numberMore, 200);

    //    return $textResponse;
    // }

      /**
     * @Route("/test", name="test")
     */
    function test(){
        $to = DateTime::createFromFormat('Y-m-d H:i', '2021-05-07 15:49');
        $to->add(new DateInterval('PT20M'));
        var_dump($to);
        exit;
    }
}



