<?php

namespace App\Controller;


use App\Entity\Cart as ECart;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CartController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    private function getCart(): Ecart
    {
        $request = $this->requestStack->getCurrentRequest();
        if(!$request->getSession()->get("ecart")){
            $request->getSession()->set("ecart", new ECart());
        }
        return $request->getSession()->get("ecart");
    }

    private function sendToSession($cart)
    {
        $request = $this->requestStack->getCurrentRequest();
        $request->getSession()->set("ecart", $cart);
    }
 
    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(): Response
    {
        
        return $this->render('cart/index.html.twig', [
           'cart' => $this->getCart()
        ]);
    }
    /**
     * @Route("/cart/add/{id}", name="add_cart")
     */
    public function add(Product $product): Response
    {
        /*$cart->add($id);*/
        $cart = $this->getCart();
        $cart->add($product);
        $this->sendToSession($cart); 

        return $this->redirectToRoute('cart');

    }
    /**
     * @Route("/cart/remove", name="remove_cart")
     */
    public function remove(): Response
    {
        $cart = $this->getCart();
        $cart->clear();
        $this->sendToSession($cart);
        return $this->redirectToRoute('products');
    }
    /**
     * @Route("/cart/delete/{id}", name="delete_cart")
     */
    public function delete(Product $product): Response
    {
        $cart = $this->getCart();
        $cart->delete($product);
        $this->sendToSession($cart);
        return $this->redirectToRoute('cart');
    }
    /**
     * @Route("/cart/decrease/{id}", name="decrease_cart")
     */
    public function decrease(Product $product): Response
    {
        $cart = $this->getCart();
        $cart->remove($product);
        $this->sendToSession($cart);
        return $this->redirectToRoute('cart');
    }

}
