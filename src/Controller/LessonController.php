<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LessonController extends AbstractController
{
    #[Route('/course/{id}/lessons', name: 'app_lesson_list')]
    #[IsGranted('ROLE_STUDENT')]
    public function list(int $id, CourseRepository $courseRepository): Response
    {
        $course = $courseRepository->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Cours introuvable.');
        }

        return $this->render('lesson/list.html.twig', [
            'course' => $course,
            'lessons' => $course->getLessons(),
        ]);
    }

    #[Route('/course/{id}/lesson/new', name: 'app_lesson_new')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function new(int $id, CourseRepository $courseRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = $courseRepository->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Cours introuvable.');
        }

        $lesson = new Lesson();
        $lesson->setCourse($course);

        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lesson);
            $entityManager->flush();

            $this->addFlash('success', 'Leçon ajoutée avec succès !');
            return $this->redirectToRoute('app_instructor_courses');
        }

        return $this->render('lesson/new.html.twig', [
            'form' => $form->createView(),
            'course' => $course,
        ]);
    }

    #[Route('/lesson/{id}', name: 'app_lesson_show')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }
}