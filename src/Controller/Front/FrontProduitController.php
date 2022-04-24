<?php

namespace App\Controller\Front;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontProduitController extends AbstractController
{
    /**
     * @Route("/", name="list_product")
     */
    public function listProduit(ProduitRepository $produitRepository)
    {
        $produits = $produitRepository->findAll();

        return $this->render("front/list_produits.html.twig", ['produits' => $produits]);
    }

    /**
     * @Route("/produit/{id}", name="show_produit")
     */
    public function showProduit($id, ProduitRepository $produitRepository)
    {
        $produit = $produitRepository->find($id);

        return $this->render("front/show_product.html.twig", ['produit' => $produit]);
    }
}
