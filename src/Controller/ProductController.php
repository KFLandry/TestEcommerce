<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
    #[Route('/product', name: 'create_product')]
    public function createProduct(EntityManagerInterface $entityManager): response
    {
        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(1999);
        $product->setDescription("Ergonomic and stylish");

        //tell doctrine you want to (eventually) save the product
        $entityManager->persist($product);

        //actually executes the queries
        $entityManager->flush();

        return new response('save new product with id' . $product->getId());
    }
}
