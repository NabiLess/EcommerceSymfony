<?php
 
namespace App\Controller;
 
use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
 
class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, Request $request, $reference)
    {
        $cart = $request->getSession()->get('ecart');
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $order = $entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        if(!$order){
            return $this->redirectToRoute('order');
        }
 
        foreach ($order->getOrderDetails()->getValues() as $line) {
            $line_object = $entityManager->getRepository(Product::class)->findOneBy(['name' => $line->getProduct()]);
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $line->getPrice(),
                    'product_data' => [
                        'name' => $line->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$line_object->getIllustration()],
                    ],
                ],
                'quantity' => $line->getQuantity(),
            ];
        }
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $order->getCarrierPrice(),
                    'product_data' => [
                        'name' => $order->getCarrierName(),
                        'images' => [$YOUR_DOMAIN],
                    ],
                ],
                'quantity' => 1,
            ];
        
 
        Stripe::setApiKey('sk_test_51KCOdHHfP4sWIoSqHumBfb5B2TyABjdBPKYNhdA9TxknMcPyue2cxbDlbBicN45SJXkxX3i1Bw9TvcIhY4mErNGH00AQHmoGUk');

 
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [
                $product_for_stripe
            ],
            'payment_method_types' => [
                'card',
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);
 
        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();
        return $this->redirect($checkout_session->url);
    }
}