<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AbonnementRepository::class)
 */
#[ApiResource]
class Abonnement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero_serie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero_activation;

    /**
     * @ORM\ManyToOne(targetEntity=Distributeur::class, inversedBy="abonnements")
     */
    private $distributeur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity=ActivationAbonnements::class, mappedBy="abonnement", cascade={"persist", "remove"})
     */
    private $activationAbonnements;

    /**
     * @ORM\ManyToOne(targetEntity=PointVente::class, inversedBy="abonnements")
     */
    private $point_vente;

 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroSerie(): ?string
    {
        return $this->numero_serie;
    }

    public function setNumeroSerie(string $numero_serie): self
    {
        $this->numero_serie = $numero_serie;

        return $this;
    }

    public function getNumeroActivation(): ?string
    {
        return $this->numero_activation;
    }

    public function setNumeroActivation(string $numero_activation): self
    {
        $this->numero_activation = $numero_activation;

        return $this;
    }

    public function getDistributeur(): ?Distributeur
    {
        return $this->distributeur;
    }

    public function setDistributeur(?Distributeur $distributeur): self
    {
        $this->distributeur = $distributeur;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getActivationAbonnements(): ?ActivationAbonnements
    {
        return $this->activationAbonnements;
    }

    public function setActivationAbonnements(ActivationAbonnements $activationAbonnements): self
    {
        // set the owning side of the relation if necessary
        if ($activationAbonnements->getAbonnement() !== $this) {
            $activationAbonnements->setAbonnement($this);
        }

        $this->activationAbonnements = $activationAbonnements;

        return $this;
    }

    public function getPointVente(): ?PointVente
    {
        return $this->point_vente;
    }

    public function setPointVente(?PointVente $point_vente): self
    {
        $this->point_vente = $point_vente;

        return $this;
    }

   
}
