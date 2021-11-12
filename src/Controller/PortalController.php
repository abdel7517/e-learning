<?php
declare(strict_types=1);

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Language;
use App\Domain\LanguageTrait;
use App\Entity\LearningModule;
use App\Domain\LearningModuleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PortalController extends AbstractController
{
    use LanguageTrait;
    private $security;
    public function  __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @Route("/portal", name="portal")
     */
    public function index(Request $request): Response

    {
        if (isset($_GET['mode'])) {
            $mode = $_GET['mode'];
        } else {
            $mode = 'ALL';
        }

        $user = $this->security->getUser();
       
        $n = DateTime::createFromFormat('Y-m-d', Date('Y-m-d') );
        $start = date_format($user->getStart(), 'Y-m-d');
        $end = date_format($user->getEnd(), 'Y-m-d');
        $now = date_format($n, 'Y-m-d');
        
        if($start > $now)
        {
           return $this->render('portal/info.html.twig', ['message' => "Votre formation débute le " . $user->getStart()->format('d:m:Y') ]);
        }
        if($end < $now)
        {
            return $this->render('portal/info.html.twig', ['message' => "Votre formation est finis depuis le " . $user->getEnd()->format('d:m:Y') ]);
        }

        // get the user formation and publish him sld
        $formation = $user->getFormation();
        $modules = $this->getDoctrine()->getRepository(LearningModule::class)->findBy(['id' =>  $formation ]);

       /* $modules = !isset($_GET['mode'])?
            $this->getDoctrine()->getRepository(LearningModule::class)->findBy(['isPublished' => true])
            : $this->getDoctrine()->getRepository(LearningModule::class)->findBy([
                'isPublished' => true,
                'type' => strtoupper($_GET['mode'])
            ]);*/

        $mode = $_GET['mode'] ?? 'ALL';

        $activeModules = $finishedModules = [];

        /** @var User $user */
        $user = $this->getUser();

        foreach($modules AS $learningModule) {
            if(isset($user->getBadges()[$learningModule->getId()])) {
                $finishedModules[] = $learningModule;
            } else {
                $activeModules[] = $learningModule;
            }
        }
       return $this->render('portal/index.html.twig', [
            'mode' => $mode,
            'language' => $this->getLanguage($request),
            'activeModules' => $activeModules,
            'finishedModules' => $finishedModules,
            'mode' => $mode,
        ]);
    }
}
