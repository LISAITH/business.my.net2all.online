<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SousCompteRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=SousCompteRepository::class)
 */
#[ApiResource]
class SousCompte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(["compteEcash","reglementsEcash"])]
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    #[Groups(["compteEcash","reglementsEcash"])]
    private $solde;

    /**
     * @ORM\ManyToOne(targetEntity=CompteEcash::class, inversedBy="sousComptes",cascade={"persist", "remove"})
     */
    #[Groups(["reglementsEcash"])]
    private $compteEcash;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    #[Groups(["compteEcash","reglementsEcash"])]
    private $numeroSousCompte;

    /**
     * @ORM\ManyToOne(targetEntity=Services::class)
     */
    #[Groups(["compteEcash","reglementsEcash"])]
    private $service;

    /**
     * @ORM\OneToMany(targetEntity=ReglementsEcash::class, mappedBy="id_sous_compte_envoyeur")
     */
    private $reglementsEcashes;

    /**
     * @ORM\OneToMany(targetEntity=ReglementsEcash::class, mappedBy="id_sous_compte_receveur")
     */
    private $reglementsEcashe;


    public function __construct()
    {
        $this->reglementsEcashes = new ArrayCollection();
        $this->reglementsEcashe = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getCompteEcash(): ?CompteEcash
    {
        return $this->compteEcash;
    }

    public function setCompteEcash(?CompteEcash $compteEcash): self
    {
        $this->compteEcash = $compteEcash;

        return $this;
    }

    public function getNumeroSousCompte(): ?string
    {
        return $this->numeroSousCompte;
    }

    public function setNumeroSousCompte(string $numeroSousCompte): self
    {
        $this->numeroSousCompte = $numeroSousCompte;

        return $this;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(?Services $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return Collection<int, ReglementsEcash>
     */
    public function getReglementsEcashes(): Collection
    {
        return $this->reglementsEcashes;
    }

    public function addReglementsEcash(ReglementsEcash $reglementsEcash): self
    {
        if (!$this->reglementsEcashes->contains($reglementsEcash)) {
            $this->reglementsEcashes[] = $reglementsEcash;
            $reglementsEcash->setIdSousCompteEnvoyeur($this);
        }

        return $this;
    }

    public function removeReglementsEcash(ReglementsEcash $reglementsEcash): self
    {
        if ($this->reglementsEcashes->removeElement($reglementsEcash)) {
            // set the owning side to null (unless already changed)
            if ($reglementsEcash->getIdSousCompteEnvoyeur() === $this) {
                $reglementsEcash->setIdSousCompteEnvoyeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReglementsEcash>
     */
    public function getReglementsEcashe(): Collection
    {
        return $this->reglementsEcashe;
    }

    public function addReglementsEcashe(ReglementsEcash $reglementsEcashe): self
    {
        if (!$this->reglementsEcashe->contains($reglementsEcashe)) {
            $this->reglementsEcashe[] = $reglementsEcashe;
            $reglementsEcashe->setIdSousCompteReceveur($this);
        }

        return $this;
    }

    public function removeReglementsEcashe(ReglementsEcash $reglementsEcashe): self
    {
        if ($this->reglementsEcashe->removeElement($reglementsEcashe)) {
            // set the owning side to null (unless already changed)
            if ($reglementsEcashe->getIdSousCompteReceveur() === $this) {
                $reglementsEcashe->setIdSousCompteReceveur(null);
            }
        }

        return $this;
    }

}
