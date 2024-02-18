<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/blog/show/{id}', name: 'show_blog')]
    public function show(ArticleRepository $repo, $id): Response
    {
        $articles = $repo->find($id);
        return $this->render('blog/show.html.twig', [
            'article' => $articles
        ]);
    }

    #[Route('/blog/new', name: 'blog_create')]
    #[Route('/blog/edit/{id}', name: 'blog_edit')]
    public function form(Request $request, EntityManagerInterface $manager, Article $article = null)
    {
        if (!$article) {
            // Mode création : Créez un nouvel objet Article
            $article = new Article();
            $article->setCreatedAt(new \DateTime());
        }
    
        // Créez le formulaire en utilisant l'objet Article approprié (nouveau ou édité)
        $form = $this->createForm(ArticleType::class, $article);
    
        dump($request);
        
        // Gérez la soumission du formulaire
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegardez l'article en base de données
            $manager->persist($article);
            $manager->flush();
    
            // Redirigez l'utilisateur vers la page de visualisation de l'article (ou toute autre page souhaitée)
            return $this->redirectToRoute('show_blog', [
                'id' => $article->getId()
            ]);
        }
    
        // Affichez le formulaire dans le modèle Twig approprié
        return $this->render('blog/form.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null, // Si l'ID de l'article n'est pas null, c'est le mode édition
        ]);
    }
}
