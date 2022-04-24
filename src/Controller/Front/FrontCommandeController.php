<?php

namespace App\Controller\Front;

use DateTime;
use App\Entity\Card;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;


class FrontCommandeController extends AbstractController
{
   /**
     * @Route("cart/add/{id}", name="add_cart")
     */
    public function addCart($id, SessionInterface $sessionInterface)
    {
        $cart = $sessionInterface->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $sessionInterface->set('cart', $cart);

        return $this->redirectToRoute('show_produit', ['id' => $id]);
    }

    /**
     * @Route("cart", name="front_show_cart")
     */
    public function showCart(SessionInterface $sessionInterface, ProduitRepository $produitRepository)
    {
        $cart = $sessionInterface->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'produit' => $produitRepository->find($id),
                'quantite' => $quantity
            ];
        }

        return $this->render('front/cart.html.twig', ['items' => $cartWithData]);
    }

    /**
     * @Route("cart/delete/{id}", name="delete_cart")
     */
    public function deleteCart($id, SessionInterface $sessionInterface)
    {
        $cart = $sessionInterface->get('cart', []);

        if (!empty($cart[$id] && $cart[$id] == 1)) {
            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }

        $sessionInterface->set('cart', $cart);

        return $this->redirectToRoute('front_show_cart');
    }

    /**
     * @Route("profile/create/commande", name="create_command")
     */
    public function createCommand(
        SessionInterface $sessionInterface,
        ProduitRepository $produitRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        UserRepository $userRepository,
        MailerInterface $mailerInterface
    ) {

        $commande = new Commande();

        $cart = $sessionInterface->get('cart', []);

        $price_commande = 0;

        $user = $this->getUser();

        $user_mail = $user->getUserIdentifier();
        $user_true = $userRepository->findOneBy(['email' => $user_mail]);
        $commande->setUser($user_true);

        $commande->setDateEnregistrement(new \DateTime("NOW"));
        $commande->setPrix($price_commande);


            $entityManagerInterface->persist($commande);
            $entityManagerInterface->flush();

            foreach ($cart as $id_car => $quantity) {
                $card = new Card();
                $card->setCommande($commande);
                $produit = $produitRepository->find($id_car);
                $card->setProduct($produit);
                $card->setQuantite($quantity);
                $price_produit = $produit->getPrix();
                $card->setPrixProduit($price_produit);
                $price_commande = $price_commande + ($price_produit * $quantity);
                $produit_stock = $produit->getStock();
                $car_stock_final = $produit_stock - $quantity;
                $produit->setStock($car_stock_final);
                $entityManagerInterface->persist($produit);
                $entityManagerInterface->persist($card);
                $entityManagerInterface->flush();
                unset($cart[$id_car]);
                $sessionInterface->set('cart', $cart);
            }
            $commande->setPrix($price_commande);

            $email = (new TemplatedEmail())
                ->from("test@test.com") 
                ->to($user_mail) 
                ->subject('Commande') 
                ->htmlTemplate('front/email.html.twig') 
                ->context([
                    'price' => $price_commande 
                ]);

            $mailerInterface->send($email);

            $entityManagerInterface->persist($commande);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("list_product");
        

    }
}
