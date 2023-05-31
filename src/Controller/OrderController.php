<?php

namespace App\Controller;

use DateTime;
use Stripe\Stripe;
use App\Entity\Cart;
use App\Entity\Order;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use ProxyManager\Generator\Util\UniqueIdentifierGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{ 
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande", name="order")
     */
    public function index(Request $request): Response
    {
        $cart = $request->getSession()->get("ecart");
        if (!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute('account_adress_add');
        }
        
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

      
        return $this->render('order/index.html.twig',
        [
            'form' => $form->createView(),
            'cart' => $cart
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $cart = $request->getSession()->get("ecart");
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           $date = new DateTimeImmutable();

           $carriers = $form->get('carriers')->getData();

           $delivery = $form->get('adresses')->getData();

           $delivery_content = $delivery->getFirstName().' '.$delivery->getLastName();
           $delivery_content .= '<br/>'.$delivery->getPhone();
           if ($delivery->getCompany()) {
               $delivery_content .= '<br/>'.$delivery->getCompany();
           }
           $delivery_content .= '<br/>'.$delivery->getAddress();
           $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity().' '.$delivery->getCountry();
           $delivery_content .= '<br/>'.$delivery->getCountry();

           $order = new Order;
           $reference = mb_strtoupper($date->format('dmY').'-'.uniqid());
           $order->setReference($reference);
           $order->setUser($this->getUser());
           $order->setCreatedAt($date);
           $order->setCarrierName($carriers->getName());
           $order->setCarrierPrice($carriers->getPrice());
           $order->setDelivery($delivery_content);
           $order->setState(0);

           $this->entityManager->persist($order);

           
           foreach ($cart->getProducts() as $line) {
               $orderDetails = new OrderDetails;
               $orderDetails->setMyOrder($order);
               $orderDetails->setProduct($line['product']->getName());
               $orderDetails->setQuantity($line['quantity']);
               $orderDetails->setPrice($line['product']->getPrice());
               $orderDetails->setTotal($line['product']->getPrice() * $line['quantity'] );

               $this->entityManager->persist($orderDetails);

            
           }
          
       $this->entityManager->flush();
           

        return $this->render('order/add.html.twig',
        [
            
            'carrier' => $carriers,
            'delivery' => $delivery_content,
            'reference' => $order->getReference(),
        ]); 


        }
      return $this->redirectToRoute('cart');
    }
}
