<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends AbstractController
{

#[Route('/payment', name: 'payment')]
public function paymentPage()
{
// Render the payment page with the Stripe public key
return $this->render('payment/index.html.twig', [
'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
]);
}

/**
* @Route("/create-payment-intent", name="create-payment-intent", methods={"POST"})
*/
#[Route('/create-payment-intent', name: 'create-payment-intent')]
public function createPaymentIntent(Request $request): Response
{
Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

$amount = 1000; // Amount in cents (e.g., 10.00 USD)

$paymentIntent = PaymentIntent::create([
'amount' => $amount,
'currency' => 'usd',
]);

return new Response(json_encode(['clientSecret' => $paymentIntent->client_secret]));
}
Shaaban123@
#[Route('/create-payment-token', name: 'create-payment-token')]
    public function paymentSuccess()
    {
        return $this->render('payment/success.html.twig');
    }
}
