<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VirementsInterBancaireRepository;

/**
 * @ORM\Entity(repositoryClass=VirementsInterBancaireRepository::class)
 */
#[ApiResource]
class VirementsInterBancaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="virementsInterBancaires")
     */
    private $id_user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero_bank_envoyeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero_bank_receveur;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_transaction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getNumeroBankEnvoyeur(): ?string
    {
        return $this->numero_bank_envoyeur;
    }

    public function setNumeroBankEnvoyeur(string $numero_bank_envoyeur): self
    {
        $this->numero_bank_envoyeur = $numero_bank_envoyeur;

        return $this;
    }

    public function getNumeroBankReceveur(): ?string
    {
        return $this->numero_bank_receveur;
    }

    public function setNumeroBankReceveur(string $numero_bank_receveur): self
    {
        $this->numero_bank_receveur = $numero_bank_receveur;

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
