<?php

namespace App\Controller;

use DateTime;
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

        $time_good_format = str_replace("_"," ",$time);
        $date = DateTime::createFromFormat('Y-m-d H:i', $time_good_format);

        
        
        $sessionRepo->setTimeDeco($date);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($sessionRepo);
        $manager->flush();

        $textResponse = new Response('present' , 200);


        return $textResponse;
    }
}



