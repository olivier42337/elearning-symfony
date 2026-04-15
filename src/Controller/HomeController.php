<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user) {
            if (in_array('ROLE_INSTRUCTOR', $user->getRoles())) {
                return $this->render('home/instructor.html.twig', [
                    'user' => $user,
                ]);
            }

            // Récupérer les inscriptions de l'étudiant
            $enrollments = $user->getEnrollments();

            return $this->render('home/student.html.twig', [
                'user' => $user,
                'enrollments' => $enrollments,
            ]);
        }

        return $this->render('home/index.html.twig');
    }
}