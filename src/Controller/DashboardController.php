<?php

namespace App\Controller;

use App\Domain\FlaggingManager;
use App\Domain\LanguageTrait;
use App\Entity\Language;
use App\Entity\LearningModule;
use App\Repository\LearningModuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    use LanguageTrait;

    /**
     * @Route("partner/dashboard/{id_module}/{state}", name="dashboard")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, string $id_module = null, bool $state = null): Response
    {

        // get the current language
        $language = $this->getLanguage($request);

        $languageCount = $this->getDoctrine()->getRepository(Language::class)->getLanguageCount();

        // fetch all LM objects from the DB
        $allModules = $this->getDoctrine()->getRepository(LearningModule::class)->findAll();

        $lmRepo = $this->getDoctrine()->getRepository(LearningModule::class);
        $fm = new FlaggingManager($languageCount);

        if( $id_module !== null && $state !== null  ){
            $manager = $this->getDoctrine()->getManager();
            // get the module with specifique id
            $module = $this->getDoctrine()->getRepository(LearningModule::class)->findOneBy(['id' => $id_module]);
            // active or disable the module
            if ($state == 1){
                $module->setIsPublished($state);
            } else{
                $module->setIsPublished(0);
            }
            $manager->persist($module);
            $manager->flush();
        
        }

        return $this->render('dashboard/index.html.twig', [
            'allModules' => $allModules,
            'language' => $language,
            'lmRepo' => $lmRepo,
            'fm' => $fm,
            'languagecount' => $languageCount,
        ]);
    }
}
