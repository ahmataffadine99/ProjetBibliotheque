<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche', methods: ['GET'])]
    public function recherche(Request $request, LivreRepository $livreRepository): Response
    {
        $motCle = $request->query->get('mot_cle');

        if (!$motCle) {
            return $this->render('recherche/index.html.twig', [
                'livres' => [],
                'motCle' => $motCle,
            ]);
        }

        $livres = $livreRepository->findByKeyword($motCle);

        return $this->render('recherche/index.html.twig', [
            'livres' => $livres,
            'motCle' => $motCle,
        ]);
    }
}