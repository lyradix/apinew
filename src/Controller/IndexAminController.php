<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexAminController extends AbstractController
{
    #[Route('/admin', name: 'app_index_amin', methods: ['GET', 'POST'])]
    public function admin(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        return $this->render('index_amin/index.html.twig', [
            'controller_name' => 'IndexAminController',
        ]);
    }
}
