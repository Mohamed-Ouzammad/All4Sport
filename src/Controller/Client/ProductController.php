<?php

namespace App\Controller\Client;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[
    Route(
        '/p',
        name: 'app_client_',
        requirements: ['host' => '%app.host.client%'],
        defaults: ['host' => '%app.host.client%'],
        host: '{host}'
    )
]
class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product', requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function viewProduct($slug, ProductRepository $productRepository): Response
    {
        // Rechercher le produit en fonction du slug saisi
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if (!$product) {
            // Gérer le cas où le produit n'a pas été trouvé
            throw $this->createNotFoundException('Produit non trouvée');
        }

        // Accéder à la catégorie du produit
        $category = $product->getCategory();

        // Vérifier si la catégorie a un pourcentage de réduction associé
        $discountPercentage = $category ? $category->getDiscountPercentage() : 0;

        // Rendre le template en passant le produit
        return $this->render('client/product/product.html.twig', [
            'product' => $product,
            'discountPercentage' => $discountPercentage,
        ]);
    }
}
