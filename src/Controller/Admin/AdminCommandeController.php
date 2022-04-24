<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCommandeController extends AbstractController
{
    /**
     * @Route("admin/commandes", name="admin_list_commande")
     */
    public function adminListCommande(CommandeRepository $commandeRepository)
    {
        $commandes = $commandeRepository->findAll();

        return $this->render("admin/list_commandes.html.twig", ['commandes' => $commandes]);
    }

    /**
     * @Route("admin/commande/{id}", name="admin_show_commande")
     */
    public function adminShowCommande($id, CommandeRepository $commandeRepository)
    {
        $commande = $commandeRepository->find($id);

        return $this->render("admin/show_commande.html.twig", ['commande' => $commande]);
    }

    /**
     * @Route("admin/create/commande", name="admin_create_commande")
     */
    public function createCommande(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $commande = new Commande();

        $commandeForm = $this->createForm(CommandeType::class, $commande);

        $commandeForm->handleRequest($request);

        if($commandeForm->isSubmitted() && $commandeForm->isValid()){
            
            $commande->setDateEnregistrement(new \DateTime("NOW"));

            $entityManagerInterface->persist($commande);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_list_commande');
        }

        return $this->render("admin/form_commande.html.twig", ['commandeForm' => $commandeForm->createView()]);
    }

    /**
     * @Route("admin/update/commande/{id}", name="admin_update_commande")
     */
    public function adminUpdateCommande($id, CommandeRepository $commandeRepository, EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $sluggerInterface)
    {
        $commande = $commandeRepository->find($id);

        $commandeForm = $this->createForm(CommandeType::class, $commande);

        $commandeForm->handleRequest($request);

        if($commandeForm->isSubmitted() && $commandeForm->isValid()){

            $commande->setDateEnregistrement(new \DateTime("NOW"));

            $entityManagerInterface->persist($commande);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_list_commande');
        }

        return $this->render("admin/form_commande.html.twig", ['commandeForm' => $commandeForm->createView()]);
    }

    /**
     * @Route("admin/delete/commande/{id}", name="admin_delete_commande")
     */
    public function adminDeleteCommande($id, CommandeRepository $commandeRepository, EntityManagerInterface $entityManagerInterface)
    {
        $commande = $commandeRepository->find($id);

        $entityManagerInterface->remove($commande);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('admin_list_commande');
    }
}
