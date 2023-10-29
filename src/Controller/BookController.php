<?php

namespace App\Controller;
use DateTimeInterface;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;
use App\Form\OmarbookType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/showDBbook', name: 'showDBbook')]

    public function showDBbook(BookRepository $bookRepo, Request $request): Response
    {
        $form = $this->createForm(OmarbookType::class); // Create the search form
    
        $form->handleRequest($request); // Handle form submission if it's been submitted
    
        if ($form->isSubmitted() && $form->isValid()) {
            // If the form is submitted and valid, use the search criteria
            $datainput = $form->getData();
           var_dump($datainput); 
           $x = $bookRepo->find($datainput); // Use findBy method with search criteria
        } else {
            // If the form is not submitted or not valid, get all books
            //3. Afficher la liste des livres Trier par auteur comme indiquée dans la figuresuivante:
           //    $x = $bookRepo->orderByUserName();
           //4. Afficher la liste des livres publiés avant l’année 2023 dont l’auteur a plus de 35livres
           $x = $bookRepo->findBooksBefore2023ByAuthorsWithMoreThan35Books();

           //5
           
           $totalscifi = $bookRepo->findSumScienceFictionBooks();
//6


        }
    
        return $this->render('book/showDBbook.html.twig', [
            'books' => $x,
            'searchbook' => $form->createView(),
            'totalscifi'=>$totalscifi,
        ]);
    }

    #[Route('/datshow', name: 'datshow')]

    public function listBooksPublishedBetweenDates(BookRepository $bookRepo,Request $request): Response
    {
        $form = $this->createForm(OmarbookType::class); // Create the search form
    
        $form->handleRequest($request); // Handle form submission if it's been submitted
        $totalscifi="valeur de test lesci fi est dans la premier methode ";
        if ($form->isSubmitted() && $form->isValid()) {
            // If the form is submitted and valid, use the search criteria
            $datainput = $form->getData();
           var_dump($datainput); 
           $x = $bookRepo->find($datainput); // Use findBy method with search criteria
        } else {
        $startDate = new \DateTime('2014-01-01');
        $endDate = new \DateTime('2022-12-31');

        $books = $bookRepo->findBooksPublishedBetweenDates($startDate, $endDate);
        }
        return $this->render('book/showDBbook.html.twig', [
            'books' => $books,
            'searchbook' => $form->createView(),
            'totalscifi'=>$totalscifi,

        ]);
    }
    /*#[Route('/addstaticbook', name: 'addstaticbook')]
    public function addstaticbook(ManagerRegistry $manager): Response
{$em = $manager->getManager();
    $book = new Book();
    
        $book->setTitle("les miserables");
        $book->setCategory("mystery");
        $book->setPublicationDate();
        $book->setPublished(1);

        $em->persist($book);
        $em->flush();

        return new Response("add with succcess");
    }*/
    #[Route('/
    ;', name: 'addbook')]
    public function addbook( ManagerRegistry $manager,Request $req): Response
    {
        $em = $manager->getManager();
       

        $book = new Book();
      
        $form = $this->createForm(BookType::class,$book);
        $form->add('ajouter',SubmitType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('showDBbook');
        }

        return $this->renderForm('book/add.html.twig', array(
            'f' => $form
        ));
    }
   
#[Route('/editbook/{id}', name: 'app_editBook')]
public function edit(ManagerRegistry $manager,BookRepository $repository, $id, Request $request)
{
    $book = $repository->find($id);
    $form = $this->createForm(BookType::class, $book);
    $form->add('Edit', SubmitType::class);
     $em=$manager->getManager();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('showDBbook');
    }

    return $this->render('book/edit.html.twig', [
        'f' => $form->createView(),

        
    ]);
}
#[Route('/deletebook/{id}', name: 'deletebook')]
    public function deletebook($id, ManagerRegistry $manager, BookRepository $bookRepo): Response
    {
        $emm = $manager->getManager();
        $Idremove= $bookRepo->find($id);
        $emm->remove($Idremove);
        $emm->flush();


        return $this->redirectToRoute('showDBbook');
    }




}
