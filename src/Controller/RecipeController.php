<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;



class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe_index', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        /* 
        * This controller displays all recipe
        *
        * @param PaginatorInterface $paginator
        * @param Request $request
        * @param IngredientRepository $ingredientRepository
        * @return Response
        */
        $recipes = $paginator->paginate(
            $recipeRepository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
        
    }
    #[Route('/recipe/nouveau', name: 'recipe.new' , methods: ['POST','GET'])]
    public function new(Request $request,EntityManagerInterface $manager) : Response
    {
        $recipes = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipes);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           $recipes = $form->getData();

           $manager->persist($recipes);
           $manager->flush();
           
           $this->addFlash(
            'success',
            'Votre Recette a été créé avec succès !'
        );
            return $this->redirectToRoute('recipe_index');  
        }
        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/recipe/edit/{id}', name:'recipe.edit', methods: ['POST','GET'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager) : Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
    
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre Recette a été modifier avec succès!'
            ); 
            return $this->redirectToRoute('recipe_index');
        }
        return $this->render('pages/recipe/edit.html.twig' , [
            'form' => $form->createView(),
        ]);
        
    }
    #[Route('/recipe/delete/{id}', name:'recipe.delete', methods: ['GET'])]
    public function delete(Recipe $recipe, Request $request, EntityManagerInterface $manager) : Response
    {
        
        if (!$recipe){
            return $this->redirectToRoute('recipe_index');
            $this->addFlash(
                'warning',
                'la Recette en question n\'a pas été trouver !'
            );

        }
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre recette a été supprimer avec succès !'
        );

        return $this->redirectToRoute('recipe_index');
    }

}