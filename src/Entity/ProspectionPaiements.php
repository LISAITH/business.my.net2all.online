<?php

namespace App\Entity;

use App\Repository\ProspectionPaiementsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UE;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=ProspectionPaiementsRepository::class)
 */
#[ApiResource]
class ProspectionPaiements
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $annee_mois;

    /**
     * @ORM\Column(type="datetime")
     */
    private $done_at;

    /**
     * @ORM\ManyToOne(targetEntity=Particuliers::class, inversedBy="prospectionPaiements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $particulier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getAnneeMois(): ?string
    {
        return $this->annee_mois;
    }

    public function setAnneeMois(string $annee_mois): self
    {
        $this->annee_mois = $annee_mois;

        return $this;
    }

    public function getDoneAt(): ?\DateTimeInterface
    {
        return $this->done_at;
    }

    public function setDoneAt(\DateTimeInterface $done_at): self
    {
        $this->done_at = $done_at;

        return $this;
    }

    public function getParticulier(): ?Particuliers
    {
        return $this->particulier;
    }

    public function setParticulier(?Particuliers $particulier): self
    {
        $this->particulier = $particulier;

        return $this;
    }
}
