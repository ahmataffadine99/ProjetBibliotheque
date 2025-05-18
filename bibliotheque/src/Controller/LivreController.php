<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreForm;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted; 
use Doctrine\ORM\EntityManagerInterface; 
use App\Form\DiscussionForm; 
use App\Entity\Discussion; 
use App\Repository\DiscussionRepository; 
use Knp\Component\Pager\PaginatorInterface; 

#[Route('/livre')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'app_livre_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')] 
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }
#[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] 
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreForm::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre); 
            $entityManager->flush(); 

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(), 
        ]);
    }
 #[Route('/{id}/{page?1}', name: 'app_livre_show', methods: ['GET', 'POST'], requirements: ['page' => '\d+'])]
    public function show(Livre $livre, DiscussionRepository $discussionRepository, Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, int $page = 1): Response
    {
        $query = $discussionRepository->createQueryBuilder('d')
            ->where('d.livre = :livre')
            ->orderBy('d.DateCreation', 'DESC')
            ->setParameter('livre', $livre)
            ->getQuery();

        $paginatedDiscussions = $paginator->paginate(
            $query, 
            $page, 
            3
        );

        $discussion = new Discussion();
        $form = $this->createForm(DiscussionForm::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $discussion->setLivre($livre);
                $discussion->setDateCreation(new \DateTimeImmutable());
                $discussion->setAuteur($this->getUser());
                $entityManager->persist($discussion);
                $entityManager->flush();

                return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('warning', 'Vous devez être connecté pour poster une discussion.');
            }
        }

        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
            'discussions' => $paginatedDiscussions, 
            'discussionForm' => $form->createView(),
        ]);
    }

   #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] 
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response 
    {
        $form = $this->createForm(LivreForm::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush(); 

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(), 
        ]);
    }
#[Route('/{id}', name: 'app_livre_delete', methods: ['POST'])] 
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager, LivreRepository $livreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre); 
            $entityManager->flush(); 
        }

    return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);

    }
}