<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'app_admin_')] 
class AdminController extends AbstractController
{
    #[Route('', name: 'index')] 
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
