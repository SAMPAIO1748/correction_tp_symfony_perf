<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    private $commande;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    private $product;

    #[ORM\Column(type: 'integer')]
    private $quantite;

    #[ORM\Column(type: 'string', length: 255)]
    private $prix_produit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getProduct(): ?Produit
    {
        return $this->product;
    }

    public function setProduct(?Produit $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixProduit(): ?string
    {
        return $this->prix_produit;
    }

    public function setPrixProduit(string $prix_produit): self
    {
        $this->prix_produit = $prix_produit;

        return $this;
    }
}
