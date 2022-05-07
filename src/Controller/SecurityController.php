<?php

namespace App\Controller;

use App\Form\EditProfilType;
use App\Form\TypesType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {


        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/utilisateurs/modifier", name="modifier_profil")
     */
    public function editProfil(Request $request , UserRepository  $repository, UserPasswordHasherInterface $userPasswordHasher)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $repository->add($user);



            $this->addFlash('message', 'Utilisateur modifié avec succès');

            return $this->redirectToRoute('modifier_profil');
        }

        return $this->render('admin/editProfil.html.twig', [
            'editProfilForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/utilisateurs", name="utilisateurs")
     */
    public function usersList(UserRepository $userRepository)
    {
        $users = $userRepository->findBy([
            'active'=>true
        ]);


        $userInactive = $userRepository->findBy([
            'active'=> false
        ]);

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'usersInactive'=> $userInactive
        ]);
    }

}
