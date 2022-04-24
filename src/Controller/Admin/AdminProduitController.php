<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminProduitController extends AbstractController
{
   /**
     * @Route("admin/produits", name="admin_list_product")
     */
    public function adminListProduit(ProduitRepository $produitRepository)
    {
        $produits = $produitRepository->findAll();

        return $this->render("admin/list_produits.html.twig", ['produits' => $produits]);
    }

    /**
     * @Route("admin/produit/{id}", name="admin_show_produit")
     */
    public function adminShowProduit($id, ProduitRepository $produitRepository)
    {
        $produit = $produitRepository->find($id);

        return $this->render("admin/show_product.html.twig", ['produit' => $produit]);
    }

    /**
     * @Route("admin/create/produit", name="admin_create_produit")
     */
    public function createProduit(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $produit = new Produit();

        $produitForm = $this->createForm(ProduitType::class, $produit);

        $produitForm->handleRequest($request);

        if($produitForm->isSubmitted() && $produitForm->isValid()){
            $imageFile = $produitForm->get('photo')->getData();

            if ($imageFile) {


                $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFileName = $sluggerInterface->slug($originalFileName);

                $newFileName = $safeFileName . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFileName
                );

                $produit->setPhoto($newFileName);
            }

            $produit->setDateEnregistrement(new \DateTime("NOW"));

            $entityManagerInterface->persist($produit);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_list_product');
        }

        return $this->render("admin/form_produit.html.twig", ['produitForm' => $produitForm->createView()]);
    }

    /**
     * @Route("admin/update/produit/{id}", name="admin_update_produit")
     */
    public function adminUpdateProduit($id, ProduitRepository $produitRepository, EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $sluggerInterface)
    {
        $produit = $produitRepository->find($id);

        $produitForm = $this->createForm(ProduitType::class, $produit);

        $produitForm->handleRequest($request);

        if($produitForm->isSubmitted() && $produitForm->isValid()){
            $imageFile = $produitForm->get('photo')->getData();

            if ($imageFile) {


                $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFileName = $sluggerInterface->slug($originalFileName);

                $newFileName = $safeFileName . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFileName
                );

                $produit->setPhoto($newFileName);
            }

            $produit->setDateEnregistrement(new \DateTime("NOW"));

            $entityManagerInterface->persist($produit);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_list_product');
        }

        return $this->render("admin/form_produit.html.twig", ['produitForm' => $produitForm->createView()]);
    }

    /**
     * @Route("admin/delete/produit/{id}", name="admin_delete_produit")
     */
    public function adminDeleteProduit($id, ProduitRepository $produitRepository, EntityManagerInterface $entityManagerInterface)
    {
        $produit = $produitRepository->find($id);

        $entityManagerInterface->remove($produit);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('admin_list_product');
    }
}
