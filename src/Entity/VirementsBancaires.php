<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VirementsBancairesRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VirementsBancairesRepository::class)
 */
#[ApiResource(normalizationContext: ['groups' => ['virementsBancaires']])]
class VirementsBancaires
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(["virementsBancaires"])]
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CompteEcash::class, inversedBy="virementsBancaires")
     */
    #[Groups(["virementsBancaires"])]
    private $id_compte_ecash;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(["virementsBancaires"])]
    private $numero_bancaire;

    /**
     * @ORM\Column(type="float")
     */
    #[Groups(["virementsBancaires"])]
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(["virementsBancaires"])]
    private $date_transaction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCompteEcash(): ?CompteEcash
    {
        return $this->id_compte_ecash;
    }

    public function setIdCompteEcash(?CompteEcash $id_compte_ecash): self
    {
        $this->id_compte_ecash = $id_compte_ecash;

        return $this;
    }

    public function getNumeroBancaire(): ?string
    {
        return $this->numero_bancaire;
    }

    public function setNumeroBancaire(string $numero_bancaire): self
    {
        $this->numero_bancaire = $numero_bancaire;

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

    public function getDateTransaction(): ?\DateTimeInterface
    {
        return $this->date_transaction;
    }

    public function setDateTransaction(\DateTimeInterface $date_transaction): self
    {
        $this->date_transaction = $date_transaction;

        return $this;
    }
}
