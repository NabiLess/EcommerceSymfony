<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccesController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_succes")
     */
    public function index($stripeSessionId, Request $request)
    {
        $cart = $request->getSession()->get('ecart');

        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($order->getState() == 0) {
            $cart->clear();
           
            $order->setState(1);
            $this->entityManager->flush();
            // getOrderDetails()->getValues() Regarde dans le Order entity remove details pour appliquer au mail test
            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstname()."<br>Merci pour votre commande n° ".$order->getReference()."<br><br>Livraison à l'adresse suivante ".$order->getDelivery()."<br>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sapiente optio at dolor qui modi saepe magnam pariatur nemo error. Aspernatur laboriosam facilis perferendis nam nihil, 
            ipsum laudantium rem soluta eius!";
            $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Votre commande LeBipBip',$content);
        }
        // dd($order->getOrderDetails()) ;
        return $this->render('order_succes/index.html.twig', [
            'order' => $order
        ]);
    }
}
