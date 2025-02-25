<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType; // Import RegistrationFormType instead of UserType
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')] // Applique la restriction à TOUTES les méthodes
final class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Fetch all users
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Créer et gérer le formulaire d'édition de l'utilisateur
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un mot de passe a été fourni
            $plainPassword = $user->getPlainPassword(); // Récupérer le mot de passe en clair
    
            if (!empty($plainPassword)) {
                // Si le mot de passe est fourni, on le hache
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }
    
            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();
            
            // Redirection vers la page d'administration
            return $this->redirectToRoute('app_admin');
        }
    
        // Retourner la vue du formulaire avec les données de l'utilisateur
        return $this->render('admin/useredit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    

    #[Route('/user/delete/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // CSRF token check to protect against CSRF attacks
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin'); // Redirect to dashboard after deletion
    }
    #[Route('/admin/search', name: 'admin_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $email = $request->query->get('email', '');
        $users = $entityManager->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.email LIKE :email')
            ->setParameter('email', '%' . $email . '%')
            ->getQuery()
            ->getResult();
    
        $data = array_map(function (User $user) {
            return [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }, $users);
    
        return $this->json($data);
    }
    
#[Route('/admin/csrf-token/{id}', name: 'app_user_csrf', methods: ['GET'])]
public function getCsrfToken(int $id): JsonResponse
{
    return new JsonResponse(['token' => $this->csrfTokenManager->getToken('delete' . $id)->getValue()]);
}


}
