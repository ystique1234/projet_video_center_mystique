<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SubscriptionController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/subscription", name="app_subscription")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        
        // Vérifiez si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifiez si l'utilisateur est déjà premium
        if (in_array('ROLE_PREMIUM', $user->getRoles(), true)) {
            return $this->redirectToRoute('app_home'); // Redirige vers la page d'accueil si déjà premium
        }

        return $this->render('subscription/index.html.twig', [
            'controller_name' => 'SubscriptionController',
        ]);
    }

    
      #[Route(path:'/subscription/subscribe', name:'app_subscription_subscribe')]
    
    public function subscribe(Request $request): Response
    {
        $user = $this->getUser();

        // Vérifiez si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour souscrire.');
        }

        // Simule une logique de paiement réussie (à adapter avec une vraie intégration de paiement)
        $paymentSuccess = true;

        if ($paymentSuccess) {
            // Met à jour l'utilisateur en tant que membre premium
            if (!in_array('ROLE_PREMIUM', $user->getRoles(), true)) {
                $user->addRole('ROLE_PREMIUM');
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }

            // Redirige vers une page de succès
            return $this->redirectToRoute('app_subscription_success');
        }

        // Sinon, redirige vers une page d'échec
        return $this->redirectToRoute('app_subscription_failure');
    }

    #[Route(path: '/subscription/success', name:'app_subscription_success')]
    public function success(): Response
    {
        return $this->render('subscription/success.html.twig', [
            'message' => 'Votre abonnement a été activé avec succès!',
        ]);
    }

    #[Route(path: '/subscription/failure', name:'app_subscription_failure')]
     
    public function failure(): Response
    {
        return $this->render('subscription/failure.html.twig', [
            'message' => 'Le paiement a échoué. Veuillez réessayer.',
        ]);
    }
}
