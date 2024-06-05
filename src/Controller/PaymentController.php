<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/order/create-session-stripe/{reference}', name: 'payment_stripe')]
    public function stripeCheckout($reference, EntityManagerInterface $em, UrlGeneratorInterface $generator): RedirectResponse
    {
        $recipeStripe = [];
        $order = $em->getRepository(Orders::class)->findOneBy(['reference' => $reference]);

        if (!$order) {
            return $this->redirectToRoute('cart_index');
        }

        foreach ($order->getOrdersDetails() as $orderDetail) {
            $recipe = $em->getRepository(Recipe::class)->findOneBy(['id' => $orderDetail->getRecipes()->getId()]);

            if ($recipe) {
                $recipeStripe[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $orderDetail->getPrice() * 100, // Stripe requires amount in cents
                        'product_data' => [
                            'name' => $recipe->getTitle(),
                        ],
                    ],
                    'quantity' => $orderDetail->getQuantity(),
                ];
            }
        }

        Stripe::setApiKey('sk_test_51FD959K2mccuf6tpKGHVUE69nMRnJaIQNXXUwYkXA2YWwUQa7ohvt7TaLSSIYdROl1on41hz4MLCCqlFFjuOSiv100Jkotg1uV');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => $recipeStripe,
            'mode' => 'payment',
            'success_url' => $generator->generate('payment_success', ['reference' => $order->getReference()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $generator->generate('payment_error', ['reference' => $order->getReference()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/order/success/{reference}', name: 'payment_success', methods: ['GET'])]
    public function stripeSuccess($reference): Response
    {
        return $this->render('payment/succes.html.twig',[   'reference' => $reference,]);
    }

    #[Route('/order/error/{reference}', name: 'payment_error', methods: ['GET'])]
    public function stripeError($reference): Response
    {
        return $this->render('payment/error.html.twig',[   'reference' => $reference,]);
    }
}
