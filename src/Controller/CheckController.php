<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Service\Contact\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;

class CheckController extends AbstractController
{
    private $em, $co; 
    private $mailer;
    public function  __construct(EntityManagerInterface $em, DashboardUserController $co, Mail $mailer)
    
    {    
        $this->mailer = $mailer;
        $this->em = $em;
        $this->co = $co;
    }
    
    /**
     * @Route("/check")
     * @return Response
     */
    public function index()
    {
        $date =  DateTime::createFromFormat('Y-m-d', Date('Y-m-d'));
        $date->modify("-1 day");
        $markets = $this->em->getRepository(User::class)->findBy(["is_partner" => 1]);
        foreach($markets as $market)
        {
            $users = $this->em->getRepository(User::class)->getByDateAndMarket($date, $market->getId());
            echo $date->format("d-m-Y");
            foreach($users as $user)
            {
                $timeOfCo = $this->co->getHistory($user->getId());
                $hourLog = intdiv($timeOfCo, 60);
                $this->mailer->notif_end($user, $timeOfCo, $hourLog, $market->getEmail());
                echo $market->getEmail() ." a ". $user->getEmail(). "<br>";
            }
        }
        $textResponse = new Response('ok' , 200);
        return $textResponse;   
    }

    /**
     * @Route("/oldDropOut")
     * @return Response
     */
    public function oldDropOut()
    {
        $date =  DateTime::createFromFormat('Y-m-d', Date('Y-m-d'));
        $now = DateTime::createFromFormat('Y-m-d', Date('Y-m-d'));
        $date->modify("-3 day");
        $markets = $this->em->getRepository(User::class)->findBy(["market" => 1]);
        foreach($markets as $market)
        {
            $users = $this->em->getRepository(User::class)->getByDateStartAndMarket($date, $market->getId());
            foreach($users as $user)
            {
                $timeOfCo = $this->co->getHistory($user->getId());
                $hourLog = intdiv($timeOfCo, 60);
                if($hourLog < ($user->getDuration()/4) )
                    $this->mailer->notif_dropOut($user, $timeOfCo, $hourLog, $market->getEmail());
                echo $market->getEmail() ." a ". $user->getEmail(). " à faire ". ($user->getDuration()/4) ."<br>";
            }
        }
        $textResponse = new Response('ok' , 200);
        return $textResponse;   
    }
     /**
     * @Route("/dropout")
     * @return Response
     */
    public function dropOut(){
        $markets = $this->em->getRepository(User::class)->findBy(["is_partner" => 1]);
        foreach($markets as $market)
        {
            $users = $this->em->getRepository(User::class)->getUserNotFinishFormation($market->getId());
            foreach($users as $user)
            {
                $diffBetweenStartAndEnd = $this->getNumberOfDay($user->getEnd(), $user->getStart());
                $halfOfHourFormation = floor($diffBetweenStartAndEnd / 2);
                $stringForModify = "+". $halfOfHourFormation . " day";
                $dateHalfOfFormation =   $user->getStart()->modify($stringForModify);
                $today = new DateTime(); 
                $today->setTime( 0, 0, 0 );
                if ($dateHalfOfFormation ==  $today){
                    $timeOfCo = $this->co->getHistory($user->getId());
                    $hourLog = intdiv($timeOfCo, 60);
                    if($hourLog < ($user->getDuration()/4) )
                        $this->mailer->notif_dropOut($user, $timeOfCo, $hourLog, $market->getEmail(), $halfOfHourFormation);
                    echo $market->getEmail() ." a ". $user->getEmail(). " à faire ". ($user->getDuration()/4) . " et fait : ". $hourLog . "<br>";
                }
            }
        }
        $textResponse = new Response('' , 200);
        return $textResponse; 

    }

    // call in dropOut 
    public function getNumberOfDay(DateTime $start, DateTime $end)
    {
        $diff = date_diff($end, $start);
        $diffOnMonths = $diff->m;
        $diffOnDay = $diff->d;
        if($diffOnMonths == 0)
        {
            return $diffOnDay;
        }
        $diffTotalOnDay = ($diffOnMonths  * 30) - $diffOnDay;
        return $diffTotalOnDay;
    }
}