<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\User;
use DomainException;
use App\Domain\Badgr;
use App\Form\QuizType;
use App\Entity\Language;
use App\Entity\QuizAnswer;
use App\Entity\QuizResult;
use App\Domain\PageManager;
use App\Domain\QuizManager;
use App\Entity\QuizQuestion;
use App\Domain\LanguageTrait;
use App\Domain\ChapterManager;
use Doctrine\ORM\EntityManager;
use App\Repository\ChapterRepository;
use App\Domain\FinishedModuleException;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LearningModuleRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class QuizController extends AbstractController
{
    use LanguageTrait;
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/partner/quiz/", name="quiz_index", methods={"GET"})
     */
   /* public function index(LearningModuleRepository $learningModuleRepository): Response
    {
        return $this->render('quiz/index.html.twig', [
            'learning_modules' => $learningModuleRepository->findAll(),
        ]);
    }*/

    /**
     * @Route("/partner/quiz/show/{id}", name="quiz_show", methods={"GET"})
     */
    public function show(Request $request, Quiz $quiz, ChapterRepository $chapterRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->query->get('lang') === null){
            $language = $em->getRepository(Language::class)->findOneBy(['code'=>$request->getLocale()]);
            $request->query->set('lang', $language->getCode());
        }else {
            $language = $em->getRepository(Language::class)->findOneBy(['code' => $request->query->get('lang')]);
        }

        return $this->render('quiz/show.html.twig', [
            'chapter' => $chapterRepository->findOneBy(['quiz' => $quiz->getId()]),
            'quiz' => $quiz,
            'language'=>$language,
        ]);
    }

    ///
    ///  FRONTEND QUIZ
    ///

    /**
     * @Route("/portal/quiz/{quiz}", name="quiz_show_user")
     */
    public function showUserQuiz(Request $request, Quiz $quiz)
    {
        /** @var User $user */
        $user = $this->getUser();

        $chapterManager = new ChapterManager($quiz->getChapter());
        $module = $quiz->getChapter()->getLearningModule();

        $test = false;
        if (isset($_GET['test']) && $_GET['test'] === 'true' && $user->isPartner()) {
            $test = true;
        }

        if ($chapterManager->previous() !== null && !isset($user->getProgressByLearningModule($module)[$chapterManager->previous()->getId()])) {
            $this->addFlash('error', 'You did not unlock the chapter to access this quiz.');
            return $this->redirectToRoute('module', ['module' => $module]);
        }

        return $this->render('quiz/show-user-quiz.html.twig', [
            'quiz' => $quiz,
            'language' => $this->getLanguage($request),
            'module' => $module,
            'test' => $test
        ]);
    }

    /**
     * @Route("/portal/quiz/{quiz}/send", name="quiz_send")
     */
    public function sentQuiz(Quiz $quiz): JsonResponse
    {
        if(!isset($_POST['questions'])) {
            throw new BadRequestHttpException('Missing questions in POST');
        }

        $quizManager = new QuizManager($quiz, $_POST['questions']);
        $result = number_format($quizManager->getPercentageResult());
        /* 
            get result of quiz and update it if he his more than the last 
        */
        /* @var User $user */
        $user = $this->getUser();
        $chapter_number = $quiz->getChapter()->getChapterNumber();
         $quizResult = $this->em->getRepository(QuizResult::class)->findOneBy(["id_user" => $user->getId(), "id_chapter" =>  $chapter_number]);
        $oldResultQuiz = $quizResult ? intval($quizResult->getResult()) : 0; 
        if($oldResultQuiz < $result) 
            {
               if($quizResult)
               {
                $quizResult->setResult($result);
                $quizResult->setIdChapter($chapter_number);
                $quizResult->setIdUser($user->getId());
                $this->em->persist($quizResult);
                $this->em->flush();
               }
               else{
                   $quizResult = new QuizResult();
                    $quizResult->setResult($result);
                    $quizResult->setIdChapter($chapter_number);
                    $quizResult->setIdUser($user->getId());
                    $this->em->persist($quizResult);
                    $this->em->flush();
               }

            }

        if ($quizManager->getStatus() === $quizManager::FAIL) {
            return new JsonResponse([
                'status' => $quizManager::FAIL,
                'percentage' => $result,
                'route' => $this->generateUrl('portal'),
                'number-chapter' => $chapter_number
            ]);
        }

        $user->addProgress($quiz->getChapter());
        $this->getDoctrine()->getManager()->flush();

        if ($quizManager->getStatus() === $quizManager::FINISHED_MODULE) {
            $learningModule = $quiz->getChapter()->getLearningModule();

            try {
                $badgrManager = new Badgr;
                $badgrManager->addBadgeToUser(
                    $learningModule,
                    $user
                );
                $image = $badgrManager->getImage(
                    $user,
                    $learningModule->getBadge()
                );
            }
            catch(ClientException $e) {
                $image = '';
                $user->addBadge($learningModule);
            }

            //we need to save because we added the Badgr badge to the user in the line above
            $this->getDoctrine()->getManager()->flush();
            $chapterManager = new ChapterManager($quiz->getChapter());

            /* In case the next chapter does not have any pages,
             * point the user to the portal page.
             * This should normally not happen!
             */
            $route = $this->generateUrl('portal');
            try {
                if (count($chapterManager->next()->getPages())) {
                    $route = $this->generateUrl('module_view_page', [
                        'chapterPage' => $chapterManager->next()->getPages()[0]->getId()//firstPageOfNextChapter
                    ]);
                }
            } catch (FinishedModuleException $exception){
                //route is still Portal
            }

            return new JsonResponse([
                'status' => $quizManager::FINISHED_MODULE,
                'image' => $image,
                'route'  => $route
            ]);
        }

        return new JsonResponse([
            'status' => $quizManager::FINISHED_CHAPTER,
            'route'  => $this->generateUrl('portal')
        ]);
    }
}
