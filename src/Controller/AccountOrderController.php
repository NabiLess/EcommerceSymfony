<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/compte/mes-commandes", name="account_order")
     */
    public function index(): Response
    {
        // entity manager gestion de notre entité appelle notre entité order et notre method dans son repository qui trouve les commandes payées par utilisateur
        $orders = $this->entityManager->getRepository(Order::class)->findSuccesOrders($this->getUser());
        
        return $this->render('account/order.html.twig',[
            'orders' => $orders
        ]);
    }
    /**
     * @Route("/compte/mes-commandes/{reference}", name="account_order_show")
     */
    public function show($reference): Response
    {
        $order= $this->entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_order');
        }
        
        return $this->render('account/order_show.html.twig',[
            'order' => $order
        ]);
    }
}
