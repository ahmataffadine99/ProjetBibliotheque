<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Form\EditDiscussionForm;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/discussion')]
class DiscussionController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_discussion_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'discussion')]
    public function edit(Request $request, Discussion $discussion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditDiscussionForm::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discussion->setDateCreation(new \DateTimeImmutable()); 
            $entityManager->flush();

            return $this->redirectToRoute('app_livre_show', ['id' => $discussion->getLivre()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('discussion/edit.html.twig', [
            'discussion' => $discussion,
            'editForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_discussion_delete', methods: ['POST'])]
    #[IsGranted('delete', 'discussion')]
    public function delete(Request $request, Discussion $discussion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$discussion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($discussion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livre_show', ['id' => $discussion->getLivre()->getId()], Response::HTTP_SEE_OTHER);
    }
}