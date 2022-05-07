<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentFormType;
use App\Form\NewArticleType;
use App\Form\ThirdTestType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{


    #[Route('/article', name: 'app_article', methods: ['GET', 'POST'])]
    public function index(ArticleRepository $articleRepository, TypeRepository $typeRepository, CommentaireRepository $commentaireRepository, Request $request): Response
    {


        $articles = $articleRepository->findBy([
            'active'=>true
        ]);
        $articlesInactive = $articleRepository->findBy([
            'active'=>false
        ]);

        //$articleLiked = $articles->getLikedby()->count();


        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'articlesInnactive'=>$articlesInactive,
            //'articleliked' =>$articleLiked

        ]);

    }

    #[Route('/profile/newArticle', name: 'newArticle', methods: ['GET', 'POST'])]
    public function newTask (ArticleRepository $articleRepository, Request $request, UserRepository $userRepository){

        $article = new Article();
        $articleForm = $this->createForm(NewArticleType::class, $article);
        $articleForm->handleRequest($request);
        if ($articleForm->isSubmitted() && $articleForm->isValid()){
            $article->setCreatedate(new \DateTime('now'));
            $article->setActive(true)
                    ->setUser($this->getUser());

            $img = $articleForm->get('img')->getData();
            //creer un id unique de l'image sous format de data
            $imgName = md5(uniqid()) . '.' . $img->guessExtension();

            //on met l'image récuperer dans le dossier public/uploadDirectory grace au parametre creer dans le services.yaml
            $img->move($this->getParameter('uploadDirectory'), $imgName);

            // ajouter l'id unique $imgName dans le $prod
            $article->setImg($imgName);

            $articleRepository->add($article);
            return $this->redirectToRoute('app_article');
        }
        return $this->render('article/newArticle.html.twig', [
            'articles'=> $article,
            'articleForm' => $articleForm->createView()
        ]);
    }


     #[Route('/article/{id}', name:'showarticle', methods: ['GET', 'POST'])]

    public function showArticle(ArticleRepository $articleRepository, $id, Request $request, CommentaireRepository $commentaireRepository)
    {
        $articles = $articleRepository->findOneBy(['id' => $id]);
        $articleLiked = $articles->getLikedby()->count();

        if ($articles->getLikedby()->contains($this->getUser()) == false) {
            $jaimeca = true;

        }else{
            $jaimeca = false;
        }

        $comment = new Commentaire();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()){
            $comment->setDatecreate(new \DateTime('now'));
            $comment->setActive(true)
                ->setUser($this->getUser())
                ->setArticle($articles);

            $commentaireRepository->add($comment);
            return $this->redirectToRoute('app_article');
        }
        $comment = $commentaireRepository->findBy([
            'active'=> true
        ]);
        $commentInactive = $commentaireRepository->findBy([
            'active'=> false
        ]);


        return $this->render('article/showArticle.html.twig', [
            'articles' => $articles,
            'comments'=> $comment,
            'commentInactive'=> $commentInactive,
            'commentForm' => $commentForm->createView(),
            'articleliked' =>$articleLiked,
            'jaime'=>$jaimeca

        ]);
    }


    #[Route('/profile/changeActivearticle/{id}', name:'changeActiveArticle', methods: ['GET', 'POST'])]
    public function changeActiveProd(ArticleRepository $articleRepository, $id){
        // choisi comme id les élément de produit repository
        $article = $articleRepository->findOneBy([
            'id'=>$id
        ]);

        if ($article->getActive() == true){
            // met l'active en false
            $article->setActive(false);
        }else{
            $article->setActive(true);
        }


        // uptade la bdd
        $articleRepository->add($article);

        // redirection vers le name de la public function
        return $this->redirectToRoute('app_article');
    }

    #[Route('/profile/changeActiveComment/{id}', name:'changeActiveComment', methods: ['GET', 'POST'])]
    public function changeActiveComment(CommentaireRepository $commentaireRepository, $id){
        // choisi comme id les élément de produit repository
        $comment = $commentaireRepository->findOneBy([
            'id'=>$id
        ]);

        if ($comment->getActive() == true){
            // met l'active en false
            $comment->setActive(false);
        }else{
            $comment->setActive(true);
        }


        // uptade la bdd
        $commentaireRepository->add($comment);

        // redirection vers le name de la public function
        return $this->redirectToRoute('app_article');
    }



    //////////////////////////////////TEST DU FAV ////////////////////////////////

    #[Route('/profile/changeActiveLike/{id}', name:'changeActiveLike', methods: ['GET', 'POST'])]
    public function addFav(ArticleRepository $articleRepository, $id, UserRepository $userRepository, Request $request)
    {
        $articlelike= $articleRepository->findOneBy([
            'id' => $id
        ]);

        if ($articlelike->getLikedby()->contains($this->getUser()) == false) {
            $articlelike->addLikedby($this->getUser());
        }else{
            $articlelike->removeLikedby($this->getUser());
        }

        $articleRepository->add($articlelike);

        return $this->redirectToRoute('showarticle',[ 'id'=> $id]);
    }
}
