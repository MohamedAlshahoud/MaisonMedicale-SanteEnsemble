<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private ResetPasswordHelperInterface $resetPasswordHelper;
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    public function __construct(
        ResetPasswordHelperInterface $resetPasswordHelper,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ) {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    #[Route('/reset-password', name: 'app_forgot_password_request')]
    public function request(Request $request): Response
    {
        // Crée le formulaire de demande de réinitialisation d’email
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            $user = $this->entityManager->getRepository(\App\Entity\Patient::class)
                ->findOneBy(['email' => $email]);

            if (!$user) {
                // Pour des raisons de sécurité, on affiche un message générique
                return $this->redirectToRoute('app_check_email');
            }

            return $this->processSendingPasswordResetEmail($request, $user);
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(Request $request, $user): Response
    {
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (\SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                'Un problème est survenu lors de la demande de réinitialisation de mot de passe - %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('contact@afamiashop.be', 'Maison Médicale'))
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'user' => $user,
            ]);

        $this->mailer->send($email);

        // Stocke le token en session pour la route de confirmation
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }

    #[Route('/reset-password/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Affiche un message demandant à l'utilisateur de vérifier sa boîte mail
        return $this->render('reset_password/check_email.html.twig');
    }

    #[Route('/reset-password/reset/{token}', name: 'app_reset_password')]
    public function reset(
        Request $request,
        string $token = null,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($token) {
            // Stocke le token en session pour la validation dans la route sans token
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('Token manquant');
        }

        $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);

        if (null === $user) {
            $this->addFlash('reset_password_error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashedPassword);

            $this->entityManager->flush();

            $this->resetPasswordHelper->removeResetRequest($token);

            $this->addFlash('success', 'Mot de passe modifié avec succès.');

            return $this->redirectToRoute('app_connexion');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
