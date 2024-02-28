<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(EntityManagerInterface $em, Request $r): Response
    {
        $un_article = new Article();
        $form = $this->createForm(ArticleType::class, $un_article);

        $form->handleRequest($r);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($un_article);
            $em->flush();
        }

        $articles = $em->getRepository(Article::class)->findAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'form' => $form->createView()
        ]);
    }

    #[Route('article/delete/{id}', name:'app_article_delete')]
    public function delete(Request $r, EntityManagerInterface $em, Article $article){
        if($this->isCsrfTokenValid('delete'.$article->getId(), $r->request->get('csrf'))){
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute(('app_article'));
    }
}
