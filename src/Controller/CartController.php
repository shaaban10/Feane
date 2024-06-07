<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Orders;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, RecipeRepository $recipeRepository)
    {
        $panier = $session->get('panier', []);

        $data = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $recipe = $recipeRepository->find($id);

            $data[] = [
                'recipe' => $recipe,
                'quantity' => $quantity
            ];
            $total += $recipe->getPrice() * $quantity;
        }

        return $this->render('cart/index.html.twig', compact('data', 'total'));
    }

    #[Route('//{id}', name: 'add')]
    public function add(Recipe $recipe, SessionInterface $session)
    {
        $id = $recipe->getId();
        $panier = $session->get('panier', []);

        if (empty($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }

        $session->set('panier', $panier);

        // Update session expiration time
        $session->getMetadataBag(new MetadataBag());
        $session->migrate(true, 18000); // 18000 seconds = 5 hours

        $session->save(); // Save session to apply the new expiration time

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Recipe $recipe, SessionInterface $session)
    {
        $id = $recipe->getId();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);

        // Update session expiration time
        $session->getMetadataBag(new MetadataBag());
        $session->migrate(true, 18000); // 18000 seconds = 5 hours

        $session->save(); // Save session to apply the new expiration time

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Recipe $recipe, SessionInterface $session)
    {
        $id = $recipe->getId();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        // Update session expiration time
        $session->getMetadataBag(new MetadataBag());
        $session->migrate(true, 18000); // 18000 seconds = 5 hours

        $session->save(); // Save session to apply the new expiration time

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/empty', name: 'empty')]
    public function empty(SessionInterface $session)
    {
        $session->remove('panier');

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/checkout', name: 'checkout')]
    public function checkout(SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $panier = $session->get('panier', []);
        if (empty($panier)) {
            return $this->redirectToRoute('cart_index');
        }
        $order = new Orders();
        $order->setReference(uniqid('order_'));
        $user = $this->getUser();
        $order->setUsers($user);
        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('addresses_index', ['reference' => $order->getReference()]);
    }
}
