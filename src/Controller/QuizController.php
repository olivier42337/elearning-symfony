<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Answer;
use App\Repository\CourseRepository;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class QuizController extends AbstractController
{
    #[Route('/course/{id}/quiz/new', name: 'app_quiz_new')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function new(int $id, CourseRepository $courseRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = $courseRepository->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Cours non trouvé');
        }

        if ($request->isMethod('POST')) {
            $quiz = new Quiz();
            $quiz->setTitle($request->request->get('title'));
            $quiz->setDescription($request->request->get('description'));
            $quiz->setCourse($course);

            $entityManager->persist($quiz);

            $questions = $request->request->all('questions');
            foreach ($questions as $questionData) {
                $question = new Question();
                $question->setContent($questionData['content']);
                $question->setQuiz($quiz);
                $entityManager->persist($question);

                foreach ($questionData['answers'] as $index => $answerContent) {
                    $answer = new Answer();
                    $answer->setContent($answerContent);
                    $answer->setIsCorrect($index == $questionData['correct']);
                    $answer->setQuestion($question);
                    $entityManager->persist($answer);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Quiz créé avec succès !');

            return $this->redirectToRoute('app_instructor_courses');
        }

        return $this->render('quiz/new.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/quiz/{id}/show', name: 'app_quiz_show')]
    #[IsGranted('ROLE_STUDENT')]
    public function show(Quiz $quiz): Response
    {
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/quiz/{id}/submit', name: 'app_quiz_submit', methods: ['POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function submit(Quiz $quiz, Request $request): Response
    {
        $score = 0;
        $total = count($quiz->getQuestions());
        $results = [];

        foreach ($quiz->getQuestions() as $question) {
            $selectedAnswerId = $request->request->get('question_' . $question->getId());
            $isCorrect = false;

            foreach ($question->getAnswers() as $answer) {
                if ($answer->getId() == $selectedAnswerId && $answer->isCorrect()) {
                    $isCorrect = true;
                    $score++;
                }
            }

            $results[] = [
                'question' => $question,
                'isCorrect' => $isCorrect,
                'selectedId' => $selectedAnswerId,
            ];
        }

        return $this->render('quiz/result.html.twig', [
            'quiz' => $quiz,
            'score' => $score,
            'total' => $total,
            'results' => $results,
        ]);
    }
}