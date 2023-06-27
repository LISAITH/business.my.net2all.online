<?php

namespace App\Entity;

use App\Repository\RechargesRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=RechargesRepository::class)
 */
#[ApiResource]
class Recharges
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PointVente::class, inversedBy="recharges")
     */
    private $point_vente;

    /**
     * @ORM\ManyToOne(targetEntity=CompteEcash::class, inversedBy="recharges")
     */
    private $compte_ecash;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_recharge;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCompteEcash(): ?CompteEcash
    {
        return $this->compte_ecash;
    }

    public function setCompteEcash(?CompteEcash $compte_ecash): self
    {
        $this->compte_ecash = $compte_ecash;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateRecharge(): ?\DateTimeInterface
    {
        return $this->date_recharge;
    }

    public function setDateRecharge(\DateTimeInterface $date_recharge): self
    {
        $this->date_recharge = $date_recharge;

        return $this;
    }
}
