<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/orders', name: 'orders_')]
class OrdersController extends AbstractController
{

    #[Route('/add', name: 'add')]
    public function add(SessionInterface $session, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);
        $orderDetails = [];

        if ($panier === []) {
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('app_home');
        }

        // le panier est pas vide , on crée la commande

        $order = new Orders();
        $order->setUsers($this->getUser());
        $order->setReference(uniqid());

        $total = 0;

        foreach ($panier as $item => $quantity) {
            $orderDetail = new OrdersDetails(); // Changed variable name to avoid overwriting

            // on va chercher le recipe
            $recipe = $repository->find($item);
            $price = $recipe->getPrice();

            // on crée le detail de commande

            $orderDetail->setRecipes($recipe);
            $orderDetail->setPrice($price);
            $orderDetail->setQuantity($quantity);

            $order->addOrdersDetail($orderDetail);

            // Calculate total price
            $total += $price * $quantity;

                // Add order detail to array
            $orderDetails[] = $orderDetail;
        }

        $em->persist($order);
        $em->flush();
        $session->remove('panier');

        return $this->render('orders/index.html.twig', [
            'orderDetails' => $orderDetails,
            'total' => $total, // Pass total to the template
            'reference' => $order->getReference()
        ]);
    }}
