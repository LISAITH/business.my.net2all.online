<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ReglementsEcashRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReglementsEcashRepository::class)
 */
#[ApiResource(normalizationContext: ['groups' => ['reglementsEcash']])]
class ReglementsEcash
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups("reglementsEcash")]
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SousCompte::class, inversedBy="reglementsEcashes")
     */
    #[Groups("reglementsEcash")]
    private $id_sous_compte_envoyeur;


    /**
     * @ORM\ManyToOne(targetEntity=SousCompte::class, inversedBy="reglementsEcashe")
     */
    #[Groups("reglementsEcash")]
    private $id_sous_compte_receveur;

    /**
     * @ORM\Column(type="float")
     */
    #[Groups("reglementsEcash")]
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups("reglementsEcash")]
    private $date_transaction;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSousCompteEnvoyeur(): ?SousCompte
    {
        return $this->id_sous_compte_envoyeur;
    }

    public function setIdSousCompteEnvoyeur(?SousCompte $id_sous_compte_envoyeur): self
    {
        $this->id_sous_compte_envoyeur = $id_sous_compte_envoyeur;

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

    public function getIdSousCompteReceveur(): ?SousCompte
    {
        return $this->id_sous_compte_receveur;
    }

    public function setIdSousCompteReceveur(?SousCompte $id_sous_compte_receveur): self
    {
        $this->id_sous_compte_receveur = $id_sous_compte_receveur;

        return $this;
    }
}
