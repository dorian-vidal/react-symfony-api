<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;



class ArticleController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }



    #[Route('/article', name: 'app_article')]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $articles = $this->doctrine->getRepository(Article::class)
            ->findAll();
        $data = [];
        foreach ($articles as $product) {
           $data[] = [
               'id' => $product->getId(),
               'name' => $product->getTitle(),
               'description' => $product->getDescription(),
           ];
        }

        return $this->json($data);
    }

    #[Route('/add-article', name: 'app_add_article', methods : 'POST')]
    public function new(Request $request): JsonResponse 
    {
        
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        if($title == "" && $description == "") {
            $message = [
                'message' => "les champs title et description sont obligatoires",
                'status' => 400
            ];
            return $this->json($message);
        }
        $entityManager = $this->doctrine->getManager();
        $article = new Article();
        $article->setTitle($title);
        $article->setDescription($description);
 
        $entityManager->persist($article);
        $entityManager->flush();
 
        return $this->json('Created new article successfully with id ' . $article->getId());
    }

    #[Route('/article/{id}', name: 'get_article', methods : 'GET')]
    public function show(int $id): JsonResponse
    {
        if($id == "") {
            return this->json('fournir un id valable');
        }
        $article = $this->doctrine->getRepository(Article::class)
            ->find($id);
 
        if (!$article) {
 
            return $this->json('No article found for id' . $id, 404);
        }
 
        $data =  [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'description' => $article->getDescription(),
        ];
         
        return $this->json($data);
    }
}
