<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\FraisOperationsRepository;

/**
 * @ORM\Entity(repositoryClass=FraisOperationsRepository::class)
 */
#[ApiResource]
class FraisOperations
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $frais_reglement;

    /**
     * @ORM\Column(type="float")
     */
    private $frais_ecash_vers_sous_compte;

    /**
     * @ORM\Column(type="float")
     */
    private $frais_inter_ecash;

    /**
     * @ORM\Column(type="float")
     */
    private $frais_ecash_bank;

    /**
     * @ORM\Column(type="float")
     */
    private $frais_inter_bank;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFraisReglement(): ?float
    {
        return $this->frais_reglement;
    }

    public function setFraisReglement(float $frais_reglement): self
    {
        $this->frais_reglement = $frais_reglement;

        return $this;
    }

    public function getFraisEcashVersSousCompte(): ?float
    {
        return $this->frais_ecash_vers_sous_compte;
    }

    public function setFraisEcashVersSousCompte(float $frais_ecash_vers_sous_compte): self
    {
        $this->frais_ecash_vers_sous_compte = $frais_ecash_vers_sous_compte;

        return $this;
    }

    public function getFraisInterEcash(): ?float
    {
        return $this->frais_inter_ecash;
    }

    public function setFraisInterEcash(float $frais_inter_ecash): self
    {
        $this->frais_inter_ecash = $frais_inter_ecash;

        return $this;
    }

    public function getFraisEcashBank(): ?float
    {
        return $this->frais_ecash_bank;
    }

    public function setFraisEcashBank(float $frais_ecash_bank): self
    {
        $this->frais_ecash_bank = $frais_ecash_bank;

        return $this;
    }

    public function getFraisInterBank(): ?float
    {
        return $this->frais_inter_bank;
    }

    public function setFraisInterBank(float $frais_inter_bank): self
    {
        $this->frais_inter_bank = $frais_inter_bank;

        return $this;
    }
}
