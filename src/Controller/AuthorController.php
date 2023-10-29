<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\LaaterType;
use App\Form\MinmaxType;
use App\Form\SearchType;

class AuthorController extends AbstractController
{ 


    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }




    
    #[Route('/deleteAuthorsWithNoBooks', name: 'deleteAuthorsWithNoBooks')]

    public function deleteAuthorsWithNoBooks(AuthorRepository $authorRepository)
    {
        $authorRepository->deleteAuthorsWithNoBooks();

        return $this->redirectToRoute('showDBauthor'); // Redirect to a suitable route after deletion
    }

    #[Route('/showDBauthor', name: 'showDBauthor')]
    public function showDBauthor(AuthorRepository $authorRepo,Request $req,ManagerRegistry $manager,Request $request): Response
    {
        $authors=$authorRepo->findAll();

        $form = $this->createForm(LaaterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid($request)) {
            $data = $form->getData();
            $minBookCount = $data->getmin();
            $maxBookCount = $data->getmax();
            $authors = $authorRepo->minmax($minBookCount, $maxBookCount);
        }
return $this->render('author/showDB.html.twig', ['authors'=>$authors,            'form' => $form->createView(),
]);


    }


    #[Route('/showbyasc', name: 'showbyasc')]
    public function showbyasc(AuthorRepository $authorRepo,Request $req,ManagerRegistry $manager): Response
    {



$author=$authorRepo->orderbyemail();
return $this->render('author/showDB.html.twig', ['authors'=>$author]);


    }


    #[Route('/showbook/{id}', name: 'showbook')]
    public function showbook($id,AuthorRepository $repo,Request $req): Response
    {
        
        //$x = $authorRepo->findAll();
        //$x=$authorRepo->orderByUserName();
        //$x=$authorRepo->searchByAlphabet();
        // $book=$repo->searchById($id);
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($req);
        
        return $this->renderForm('author/showDB.html.twig', [
            'f' => $form
        ]);
       /* return $this->render('author/showDB.html.twig', [
            'authors' => $x
        ]);*/
    }




    #[Route('/addstaticSauthor', name: 'addstaticSauthor')]
    public function addstaticSauthor(ManagerRegistry $manager): Response
    {
        $em = $manager->getManager();
        $author = new Author();

        $author->setUsername("3a56");
        $author->setEmail("3a56@esprit.tn");
        $em->persist($author);
        $em->flush();

        return new Response("add with succcess");
    }

    #[Route('/addauthor  ', name: 'addauthor')]
    public function addauthor(ManagerRegistry $manager, Request $req): Response
    {
        $em = $manager->getManager();
        $author = new Author();
        $form = $this->createForm(AuthorType::class,   $author);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('showDBauthor');
        }

        return $this->renderForm('author/add.html.twig', [
            'f' => $form
        ]);
    }

     #[Route('/editauthor/{id}', name: 'editauthor')]
    public function editauthor($id, ManagerRegistry $manager, AuthorRepository $authorrepo, Request $req): Response
    {
        // var_dump($id) . die();

        $em = $manager->getManager();
        $idData = $authorrepo->find($id);
        // var_dump($idData) . die();
        $form = $this->createForm(AuthorType::class, $idData);
        $form->handleRequest($req);

        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($idData);
            $em->flush();

            return $this->redirectToRoute('showDBauthor');
        }

        return $this->renderForm('author/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/deleteauthor/{id}', name: 'deleteauthor')]
    public function deleteauthor($id, ManagerRegistry $manager, AuthorRepository $repo): Response
    {
        $emm = $manager->getManager();
        $idremove = $repo->find($id);
        $emm->remove($idremove);
        $emm->flush();


        return $this->redirectToRoute('showDBauthor');
    }

    #[Route('/showauthor/{id}', name: 'app_showauthor')]
    public function showauthor(AuthorRepository $repo,$id): Response
    {

        $authors = $repo->find($id);
        return $this->render('author/show.html.twig', [
            'authorshtml' => $authors]);
    }

     #[Route('/authorDetails/{id}', name: 'authorDetails')]
    public function authorDetails($id,AuthorRepository $repository): Response
    {
        //var_dump($id) . die();
$author=$repository->find($id);

       
        

        return $this->render('author/details.html.twig', [
            'author' => $author
        ]);
    }}
