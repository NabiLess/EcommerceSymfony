<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Cart as ECart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
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
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $nbProduct = $this->entityManager->createQuery('SELECT COUNT(p) as nbProd FROM App\Entity\Product p ' )->getSingleScalarResult();
        $nbOrder = $this->entityManager->createQuery(' SELECT COUNT(o) as nbOrder FROM App\Entity\Order o WHERE o.state > 0' )->getSingleScalarResult();
        $nbUser = $this->entityManager->createQuery(' SELECT COUNT(DISTINCT(o.user)) as nbUser FROM App\Entity\Order o WHERE o.state > 0' )->getSingleScalarResult();

        $products = $this->entityManager->getRepository(Product::class)->findBy(['isBest' => true]);
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'nbProduct' => $nbProduct,
            'nbOrder' => $nbOrder,
            'nbUser' => $nbUser,
            'cart' => $this->getCart()
        ]);
    }
}
