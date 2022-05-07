<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentFormType;
use App\Form\NewArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    #[Route('/commentaire', name: 'app_commentaire')]
    public function index(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }

/*
    #[Route('/newcomment/article/{id}', name: 'newComment', methods: ['GET', 'POST'])]
    public function newTask (ArticleRepository $articleRepository, Request $request, UserRepository $userRepository, CommentaireRepository $commentaireRepository){

        $comment = new Commentaire();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()){
            $comment->setDatecreate(new \DateTime('now'));
            $comment->setActive(true)
                ->setUser($this->getUser())
                ->setArticle($this->getArticle());


            $commentaireRepository->add($comment);
            return $this->redirectToRoute('app_article');
        }
        return $this->render('article/newArticle.html.twig', [
            'comment'=> $comment,
            'commentForm' => $commentForm->createView()
        ]);
    }
*/
}
