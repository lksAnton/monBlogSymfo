<?php

namespace App\Controller;

use App\Entity\Type;
use App\Entity\User;
use App\Form\EditProfilType;
use App\Form\EditUserType;
use App\Form\FormType;
use App\Repository\ArticleRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }



    /**
     * @Route("/superadmin/utilisateurs/modifier/{id}", name="modifier_utilisateur")
     */
    public function editUser(User $user, Request $request , UserRepository  $repository,$id)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        $user = $repository->findOneBy([
            'id'=>$id
        ]);

        if ($form->isSubmitted() && $form->isValid()) {

            $repository->add($user);

            $this->addFlash('message', 'Utilisateur modifié avec succès');

            return $this->redirectToRoute('utilisateurs');
        }

        return $this->render('admin/edituser.html.twig', [
            'userForm' => $form->createView(),
            'user'=> $user
        ]);
    }

    #[Route('/deleteUser', name: 'deleteUser', methods: ['GET', 'POST'])]
    public function deleteUser(UserRepository $userRepository, $id){
        $user = $userRepository->findOneBy(['id' => $id]);
        $userRepository->remove($user);

        return $this->redirectToRoute('utilisateurs');
    }


    #[Route('/profile/changeActiveUser/{id}', name:'desactiverUser', methods: ['GET', 'POST'])]
    public function changeActiveUser(UserRepository $userRepository, $id){
        // choisi comme id les élément de produit repository
        $user = $userRepository->findOneBy([
            'id'=>$id
        ]);

        if ($user->getActive() == true){
            // met l'active en false
            $user->setActive(false);
        }else{
            $user->setActive(true);
        }


        // uptade la bdd
        $userRepository->add($user);

        // redirection vers le name de la public function
        return $this->redirectToRoute('utilisateurs');
    }

        //////////////////////// CHECK SI LE PROFIL EST BAN OU PAS ////////////////////


    /**
     * @Route("/check", name="check")
     */
    public function checkProfil(Request $request , UserRepository  $repository)
    {
        $user = $this->getUser();
        if ($user->getActive() == false){
            return $this->redirectToRoute('app_logout');
        }

        return $this->redirectToRoute('home');
    }

   ///////////////////////////////FONCTION POUR LES TYPE ///////////////////////////////////////////////


    #[Route('/type', name: 'type', methods: ['GET', 'POST'])]
    public function type(Request $request, TypeRepository $typeRepository){
        $type = new Type();
        $form = $this->createForm(FormType::class, $type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $typeRepository->add($type);
        }
        $types = $typeRepository->findBy([], ['name' => 'ASC']);
        return $this->render('admin/type.html.twig', [
            'typeForm' => $form->createView(),
            'types' => $types
        ]);
    }

    #[Route('/deleteType/{id}', name: 'deleteType', methods: ['GET', 'POST'])]
    public function deleteType(TypeRepository $typeRepository, $id){
        $type = $typeRepository->findOneBy(['id' => $id]);
        $typeRepository->remove($type);
        return $this->redirectToRoute('type');
    }




}
