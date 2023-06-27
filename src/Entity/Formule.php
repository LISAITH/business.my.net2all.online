<?php

namespace App\Entity;

use App\Repository\FormuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormuleRepository::class)
 */
class Formule
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
    private $nom_formule;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $prix;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=Services::class, inversedBy="formules")
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity=SouscriptionFormules::class, mappedBy="formule")
     */
    private $souscriptionFormules;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->souscriptionFormules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFormule(): ?string
    {
        return $this->nom_formule;
    }

    public function setNomFormule(string $nom_formule): self
    {
        $this->nom_formule = $nom_formule;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

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

    /**
     * @return Collection<int, Services>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Services $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }

        return $this;
    }

    public function removeService(Services $service): self
    {
        $this->services->removeElement($service);

        return $this;
    }

    /**
     * @return Collection<int, SouscriptionFormules>
     */
    public function getSouscriptionFormules(): Collection
    {
        return $this->souscriptionFormules;
    }

    public function addSouscriptionFormule(SouscriptionFormules $souscriptionFormule): self
    {
        if (!$this->souscriptionFormules->contains($souscriptionFormule)) {
            $this->souscriptionFormules[] = $souscriptionFormule;
            $souscriptionFormule->setFormule($this);
        }

        return $this;
    }

    public function removeSouscriptionFormule(SouscriptionFormules $souscriptionFormule): self
    {
        if ($this->souscriptionFormules->removeElement($souscriptionFormule)) {
            // set the owning side to null (unless already changed)
            if ($souscriptionFormule->getFormule() === $this) {
                $souscriptionFormule->setFormule(null);
            }
        }

        return $this;
    }
}
