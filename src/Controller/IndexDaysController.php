<?php

namespace App\Controller;

use App\Entity\Days;
use App\Form\DayformType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexDaysController extends AbstractController
{
    #[Route('/days', name: 'index_days')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $days = new Days();
        $form = $this->createForm(DayformType::class, $days);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($days);
            $entityManager->flush();

            $this->addFlash('success', 'Days saved successfully!');

            return $this->redirectToRoute('index_days');
        }

        return $this->render('index_days/index.html.twig', [
            'DayformType' => $form->createView(),
        ]);
    }
}
