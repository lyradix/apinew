<?php

namespace App\Controller;

use App\Entity\Days;
use App\Form\DayformType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DaysRepository;

class IndexDaysController extends AbstractController
{
    #[Route('/days', name: 'days_form')]
    public function handleDaysForm(
        Request $request,
        EntityManagerInterface $entityManager,
        DaysRepository $daysRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return new Response('User is not logged in', Response::HTTP_UNAUTHORIZED);
        }

        // Fetch the days associated with the logged-in user
        $userDays = $daysRepository->findBy(['userFK' => $user]);

        // Create a new Days entity and form
        $days = new Days();
        $form = $this->createForm(DayformType::class, $days);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the checkboxes are selected and set the corresponding day
            if ($form->get('vendredi')->getData()) {
                $days->setJour('vendredi');
            }
            if ($form->get('samedi')->getData()) {
                $days->setJour('samedi');
            }
            if ($form->get('dimanche')->getData()) {
                $days->setJour('dimanche');
            }

            // Link the day to the logged-in user using setUserFK
            $days->setUserFK($user);
            $entityManager->persist($days);
            $entityManager->flush();

            $this->addFlash('success', 'Days saved successfully!');

            return $this->redirectToRoute('days_form');
        }

        return $this->render('index_days/index.html.twig', [
            'form' => $form->createView(),
            'userDays' => $userDays,
        ]);
    }
}
