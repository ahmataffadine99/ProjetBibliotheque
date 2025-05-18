<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordForm;
use App\Form\ResetPasswordRequestForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Uid\Uuid;



#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
   #[Route('', name: 'app_forgot_password_request')]
    public function request(
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ResetPasswordRequestForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $resetToken = Uuid::v4()->toRfc4122();
                $user->setResetToken($resetToken);
                $user->setResetTokenCreatedAt(new \DateTimeImmutable());
                $entityManager->persist($user);
                $entityManager->flush();

                $email = (new TemplatedEmail())
                    ->from($this->getParameter('app.mailer_from'))
                    ->to($user->getEmail())
                    ->subject('Votre demande de réinitialisation de mot de passe')
                    ->htmlTemplate('reset_password/reset_password.html.twig')
                    ->context([
                        'resetToken' => $resetToken,
                        'tokenLifetime' => $this->getParameter('app.reset_password_token_lifetime'),
                    ])
                ;

                $mailer->send($email);

                $this->addFlash('success', 'Un e-mail de réinitialisation de mot de passe vous a été envoyé. Veuillez vérifier votre boîte de réception (et votre dossier spam).');

                return $this->redirectToRoute('app_check_email');
            }

            $this->addFlash('reset_password_error', 'Aucun compte n\'a été trouvé pour cette adresse e-mail.');

            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
            'reset_password_error' => $this->addFlash('reset_password_error', null) ? $this->get('session')->getFlashBag()->get('reset_password_error')[0] ?? null : null,
        ]);
    }
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        return $this->render('reset_password/check_email.html.twig', [
            'tokenLifetime' => $this->getParameter('app.reset_password_token_lifetime'),
        ]);
    }
#[Route('/{token}', name: 'app_reset_password')]
    public function reset(Request $request, string $token, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('reset_password_error', 'Ce lien de réinitialisation de mot de passe est invalide.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        if ($user->getResetTokenCreatedAt() < new \DateTimeImmutable('-' . $this->getParameter('app.reset_password_token_lifetime') . ' seconds')) {
            $this->addFlash('reset_password_error', 'Ce lien de réinitialisation de mot de passe a expiré. Veuillez en demander un nouveau.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        $form = $this->createForm(ResetPasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $user->setResetToken(null);
            $user->setResetTokenCreatedAt(null);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
            'token' => $token,
        ]);
    }
}
