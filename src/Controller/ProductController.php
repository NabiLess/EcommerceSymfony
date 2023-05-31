<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(Request $request, ProductRepository $repo): Response
    {
        // on instancie une nouvelle recherche de part notre service Search
        $search = new Search;
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $repo->findWithSearch($search);
        }else {
            $products = $repo->findAll();
        }
        return $this->render('product/index.html.twig',
        [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/produit/{slug}", name="product")
     */
    public function show($slug): Response
    {
        $product= $this->entityManager->getRepository(Product::class)->findOneBy(['slug' => $slug]);
        $products = $this->entityManager->getRepository(Product::class)->findBy(['isBest' => true]);

        if (!$product) {
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig',
        [
            'product' => $product,
            'products' => $products
        ]);
    }
}
