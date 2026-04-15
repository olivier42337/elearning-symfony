<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CourseController extends AbstractController
{
    #[Route('/courses', name: 'app_course_list')]
    public function list(CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findBy(['isPublished' => true]);

        return $this->render('course/list.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/instructor/courses', name: 'app_instructor_courses')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function myCourses(): Response
    {
        $user = $this->getUser();
        $courses = $user->getCourses();

        return $this->render('course/my_courses.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/course/new', name: 'app_course_new')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $course->setInstructor($this->getUser());
            $course->setCreatedAt(new \DateTimeImmutable());
            $course->setIsPublished(true);

            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash('success', 'Cours créé avec succès !');

            return $this->redirectToRoute('app_instructor_courses');
        }

        return $this->render('course/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/course/{id}/edit', name: 'app_course_edit')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function edit(Course $course, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Cours modifié avec succès !');
            return $this->redirectToRoute('app_instructor_courses');
        }

        return $this->render('course/edit.html.twig', [
            'form' => $form,
            'course' => $course,
        ]);
    }

    #[Route('/course/{id}/delete', name: 'app_course_delete')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function delete(Course $course, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($course);
        $entityManager->flush();

        $this->addFlash('success', 'Cours supprimé avec succès !');

        return $this->redirectToRoute('app_instructor_courses');
    }

    #[Route('/course/{id}/enroll', name: 'app_course_enroll')]
    #[IsGranted('ROLE_STUDENT')]
    public function enroll(Course $course, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        foreach ($user->getEnrollments() as $enrollment) {
            if ($enrollment->getCourse() === $course) {
                $this->addFlash('error', 'Tu es déjà inscrit à ce cours !');
                return $this->redirectToRoute('app_course_list');
            }
        }

        $enrollment = new Enrollment();
        $enrollment->setStudent($user);
        $enrollment->setCourse($course);
        $enrollment->setEnrolledAt(new \DateTimeImmutable());
        $enrollment->setProgress(0);

        $entityManager->persist($enrollment);
        $entityManager->flush();

        $this->addFlash('success', 'Inscription au cours réussie !');

        return $this->redirectToRoute('app_course_list');
    }
}