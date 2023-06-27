<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VirementsEcashRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VirementsEcashRepository::class)
 */
#[ApiResource(itemOperations: [
    'get','put', "patch", "delete",
    'compte_ecash_user' => ['route_name' => 'get_ecash_compte'],
    'compte_ecash_number' => ['route_name' => 'ecash_compte_number'],
],
normalizationContext: ['groups' => ['virementsEcash']]
)]
class VirementsEcash
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups("virementsEcash")]
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CompteEcash::class, inversedBy="virementsEcashes")
     */
    #[Groups("virementsEcash")]
    private $id_compte_envoyeur;


    /**
     * @ORM\Column(type="float")
     */
    #[Groups("virementsEcash")]
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups("virementsEcash")]
    private $date_transaction;

    /**
     * @ORM\ManyToOne(targetEntity=CompteEcash::class, inversedBy="virementsEcashe")
     */
    private $id_compte_receveur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCompteEnvoyeur(): ?CompteEcash
    {
        return $this->id_compte_envoyeur;
    }

    public function setIdCompteEnvoyeur(?CompteEcash $id_compte_envoyeur): self
    {
        $this->id_compte_envoyeur = $id_compte_envoyeur;

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

    public function getIdCompteReceveur(): ?CompteEcash
    {
        return $this->id_compte_receveur;
    }

    public function setIdCompteReceveur(?CompteEcash $id_compte_receveur): self
    {
        $this->id_compte_receveur = $id_compte_receveur;

        return $this;
    }
}
