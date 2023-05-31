<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
       
        $url =  $this->generateUrl('home',[
            'stripeSessionId' => $stripeSessionId
        ],
        UrlGeneratorInterface::ABSOLUTE_URL
    );
        $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstname().",<br>Malheureusement une erreur de paiement est survenue lors de la validation pour votre commande n° ".$order->getReference()."<br><br><a href='".$url."'>Revenir sur le site ?</a>"."<br><br>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sapiente optio at dolor qui modi saepe magnam pariatur nemo error. Aspernatur laboriosam facilis perferendis nam nihil, 
            ipsum laudantium rem soluta eius!";
            $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Erreur commande LeBipBip',$content);
        return $this->render('order_cancel/index.html.twig',[
            'order' => $order
        ]);
    }
}
