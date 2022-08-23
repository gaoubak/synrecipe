<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'ingredient_index')]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/ingredient/index.html.twig',[
            'ingredients' => $ingredients
        ]);
    }

    #[Route('/ingredient/nouveau', name: 'ingredient.new' , methods: ['POST','GET'])]
    public function new(Request $request,EntityManagerInterface $manager) : Response
    {
        $ingredients = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredients);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           $ingredients = $form->getData();

           $manager->persist($ingredients);
           $manager->flush();
           
           $this->addFlash(
            'success',
            'Votre ingrédient a été créé avec succès !'
        );
            return $this->redirectToRoute('ingredient_index');  
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ingredient/edit/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit( Ingredient $ingredient, Request $request , EntityManagerInterface $manager) : Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           $ingredients = $form->getData();

           $manager->persist($ingredients);
           $manager->flush();
           
           $this->addFlash(
            'success',
            'Votre ingrédient a été modifier avec succès !'
        );
            return $this->redirectToRoute('ingredient_index');  
        }

        return $this->render('pages/ingredient/edit.html.twig' , [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ingredient/delete/{id}', name: 'ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager,Ingredient $ingredient): Response
    {
        if (!$ingredient){
            return $this->redirectToRoute('ingredient_index');
            $this->addFlash(
                'warning',
                'l\'ingredient en question n\'a pas été trouver !'
            );

        }
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimer avec succès !'
        );

        return $this->redirectToRoute('ingredient_index');
    }
}
