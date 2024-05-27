<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('admin', name: 'admin.')]
class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'index')]
    public function index(RecipeRepository $repository): Response
    {
        $recipes = $repository->findAll();
        return $this->render('recipe/index.html.twig',['recipes'=>$recipes]);
    }
    #[Route('/recipes/create', name: 'recipe_add')]
    public function create(EntityManagerInterface $em,Request $request, CategoryRepository $category): Response
    {
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'Recipe created succefully!');
            return $this->redirectToRoute('admin.index');
        }
        return $this->render('recipe/create.html.twig',['form'=>$form->createView()]);
    }
    #[Route('/recipes/{id}/edit', name: 'recipe-edit', methods: ['GET', 'POST'])]
    public  function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'Recipe updated succefully!');
            return $this->redirectToRoute('admin.index');
        }
        return $this->render('recipe/edit.html.twig',['form'=>$form->createView()]);

    }
    #[Route('/recipes/{id}/show/', name: 'recipe-show', methods: ['GET', 'POST'])]
    public  function show( int $id, EntityManagerInterface $em): Response {
        $recipe = $em->getRepository(Recipe::class)->find($id);

        return $this->render('recipe/show.html.twig',['recipe'=>$recipe]);

    }
    #[Route('/recipes/{id}', name: 'recipe-delete',  methods: ['DELETE'] , requirements: ['id'=> Requirement::DIGITS])]
    public function remove(int $id, EntityManagerInterface $em)
    {
        $message = 'Votre Recipes a été bien supprimé';
       $recipe = $em->getRepository(Recipe::class)->find($id);
       if(!$recipe){
           throw $this->createNotFoundException('No recipe found for id '.$id);
       }
        $em->remove($recipe);

        $em->flush();


        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin.index');
    }
}
