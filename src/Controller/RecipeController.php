<?php

namespace App\Controller;


use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Entity\User;
use App\Entity\Ingredient;

use App\Entity\Recipe;
use App\Form\IngriedentType;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;

use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

#[Route('admin', name: 'admin.')]
#[IsGranted('ROLE_ADMIN')]

class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'recipes.index')]
    public function index(RecipeRepository $repository): Response
    {
        $recipes = $repository->findAll();
        return $this->render('admin/recipe/index.html.twig',['recipes'=>$recipes]);
    }
    #[Route('/recipes/create', name: 'recipe_add')]
    public function create(EntityManagerInterface $em,Request $request, CategoryRepository $category,IngredientRepository $repository): Response
    {
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'Recipe created succefully!');
            return $this->redirectToRoute('admin.recipes.index');
        }
        return $this->render('admin/recipe/create.html.twig',['form'=>$form->createView()]);
    }
    #[Route('/recipes/{id}/edit', name: 'recipe-edit', methods: ['GET', 'POST'])]
    public  function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'Recipe updated succefully!');
            return $this->redirectToRoute('admin.recipes.index');
        }
        return $this->render('admin/recipe/edit.html.twig',['form'=>$form->createView()]);

    }
    #[Route('/recipes/{id}/show/', name: 'recipe-show', methods: ['GET', 'POST'])]
    public  function show( int $id, EntityManagerInterface $em): Response {
        $recipe = $em->getRepository(Recipe::class)->find($id);

        return $this->render('admin/recipe/show.html.twig',['recipe'=>$recipe]);

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
        return $this->redirectToRoute('admin.recipes.index');
    }




    #[Route('/ingredient', name: 'ingredient.index')]
    public function indexIngredient(IngredientRepository $ingredientRepository): Response
    {

        $ingredients = $ingredientRepository->findAll();
        return $this->render('admin/ingredient/index.html.twig',['ingredients'=>$ingredients]);
    }
    #[Route('/ingredient/create', name: 'ingredient_add')]
    public function createIngredient(EntityManagerInterface $em,Request $request): Response
    {
        $ingredient = new Ingredient();

        $form = $this->createForm(IngriedentType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em->persist($ingredient);
            $em->flush();
            $this->addFlash('success', 'Ingredient created succefully!');
            return $this->redirectToRoute('admin.ingredient.index');
        }
        return $this->render('admin/ingredient/create.html.twig',['form'=>$form->createView()]);
    }
    #[Route('/ingredient/{id}/edit', name: 'ingredient-edit', methods: ['GET', 'POST'])]
    public  function editIngredient(Ingredient $ingredient, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(IngriedentType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'Ingredient updated succefully!');
            return $this->redirectToRoute('admin.ingredient.index');
        }
        return $this->render('admin/ingredient/edit.html.twig',['form'=>$form->createView()]);

    }
    #[Route('/ingredient/{id}/show/', name: 'ingredient-show', methods: ['GET', 'POST'])]
    public  function showIngredient( int $id, EntityManagerInterface $em): Response {
        $ingredient = $em->getRepository(Ingredient::class)->find($id);

        return $this->render('admin/ingredient/show.html.twig',['ingredient'=>$ingredient]);

    }
    #[Route('/ingredient/{id}', name: 'ingredient-delete',  methods: ['DELETE'] , requirements: ['id'=> Requirement::DIGITS])]
    public function removeIngredient(int $id, EntityManagerInterface $em)
    {
        $message = 'Votre Ingredients a été bien supprimé';
        $recipe = $em->getRepository(Ingredient::class)->find($id);
        if(!$recipe){
            throw $this->createNotFoundException('No Ingredient found for id '.$id);
        }
        $em->remove($recipe);

        $em->flush();


        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin.recipes.index');
    }
    #[Route('/commandes', name: 'commandes')]
    public function commandes(RecipeRepository $repository,EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
    {
        // Fetch all users
        $users = $em->getRepository(User::class)->findAll();

        $ordersWithDetails = [];
        foreach ($users as $user) {
            // Fetch orders related to the user
            $orders = $em->getRepository(Orders::class)->findBy(['users' => $user]);

            foreach ($orders as $order) {
                $orderDetails = $em->getRepository(OrdersDetails::class)->findBy(['orders' => $order]);

                $detailsWithRecipes = [];
                foreach ($orderDetails as $detail) {
                    $recipe = $detail->getRecipes();
                    $detailsWithRecipes[] = [
                        'detail' => $detail,
                        'recipeName' => $recipe ? $recipe->getTitle() : null,
                    ];
                }

                $ordersWithDetails[] = [
                    'order' => $order,
                    'user' => $user, // Include the user information
                    'details' => $detailsWithRecipes,
                ];
            }
        }
        $query = $request->query->get('q'); // Get the search query from the request
        if ($query === null) {
            $query = ''; // Set $query to an empty string if it's null
        }

        $page = $request->query->getInt('page', 1);

        $recipe = $repository->findByTitleOrContent($query);
        return $this->render('commandes/index.html.twig', [
            'ordersWithDetails' => $ordersWithDetails,
            'recipe'=>$recipe
        ]);}}