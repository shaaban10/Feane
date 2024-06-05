<?php
namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Reservation;
use App\Form\ReservationType;

use App\Repository\CarouselleRepository;
use App\Repository\CategoryRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
private EntityManagerInterface $entityManager;

public function __construct(EntityManagerInterface $entityManager)
{
$this->entityManager = $entityManager;

}

#[Route('/', name: 'app_home')]
public function index(Request $request, RecipeRepository $repository, CategoryRepository $categoryRepository, CarouselleRepository $carouselleRepository): Response
{
$query = $request->query->get('query');
$recipes = [];

if ($query) {
$recipeRepository = $this->entityManager->getRepository(Recipe::class);
    $recipes = $recipeRepository->findByTitleOrContent($query);


} else {
    $page = $request->query->getInt('page', 1);

    $recipes = $repository->paginateRecipe($page);
}

$categories = $categoryRepository->findAll();
$carouselle = $carouselleRepository->findAll();

$reservations = new Reservation();
$form = $this->createForm(ReservationType::class, $reservations);
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
$this->entityManager->persist($reservations);
$this->entityManager->flush();
$this->addFlash('success', 'Votre réservation a bien été prise en compte');
return $this->redirectToRoute('app_home');
}

return $this->render('home/index.html.twig', [
'recipes' => $recipes,
'categories' => $categories,
'carouselle' => $carouselle,
'form' => $form->createView(),
'query' => $query,
]);
}

#[Route('/recipes/{id}/show/', name: 'recipe-show', methods: ['GET', 'POST'])]
public function show(int $id,IngredientRepository $repository): Response
{
$recipe = $this->entityManager->getRepository(Recipe::class)->find($id);
$ingredients = $recipe->getIngredient();


return $this->render('recipeShow.html.twig', ['recipe' => $recipe, 'ingredients' => $ingredients]);
}





}
