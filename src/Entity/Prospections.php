<?php

namespace App\Entity;

use App\Repository\ProspectionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=ProspectionsRepository::class)
 */
#[ApiResource]
class Prospections
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Particuliers::class, inversedBy="prospections")
     */
    private $particulier;

    /**
     * @ORM\Column(type="datetime")
     */
    private $done_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="prospections", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Entreprises::class, inversedBy="prospections", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $entreprise;

    /**
     * @ORM\OneToOne(targetEntity=Enseignes::class, inversedBy="prospections", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $enseigne;

    /**
     * @ORM\ManyToOne(targetEntity=Particuliers::class)
     */
    private $validator;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validated_at;

    /**
     * @ORM\OneToMany(targetEntity=Decouvertes::class, mappedBy="prospection")
     */
    private $decouvertes;

    /**
     * @ORM\ManyToOne(targetEntity=ReponseDecouvertes::class, inversedBy="prospections")
     */
    private $potentiel;

    public function __construct()
    {
        $this->decouvertes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDoneAt(): ?\DateTimeInterface
    {
        return $this->done_at;
    }

    public function setDoneAt(\DateTimeInterface $done_at): self
    {
        $this->done_at = $done_at;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEntreprise(): ?Entreprises
    {
        return $this->entreprise;
    }

    public function setEntreprise(Entreprises $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getEnseigne(): ?Enseignes
    {
        return $this->enseigne;
    }

    public function setEnseigne(Enseignes $enseigne): self
    {
        $this->enseigne = $enseigne;

        return $this;
    }

    public function getValidator(): ?Particuliers
    {
        return $this->validator;
    }

    public function setValidator(?Particuliers $validator): self
    {
        $this->validator = $validator;

        return $this;
    }

    public function getValidatedAt(): ?\DateTimeInterface
    {
        return $this->validated_at;
    }

    public function setValidatedAt(?\DateTimeInterface $validated_at): self
    {
        $this->validated_at = $validated_at;

        return $this;
    }

    /**
     * @return Collection<int, Decouvertes>
     */
    public function getDecouvertes(): Collection
    {
        return $this->decouvertes;
    }

    public function addDecouverte(Decouvertes $decouverte): self
    {
        if (!$this->decouvertes->contains($decouverte)) {
            $this->decouvertes[] = $decouverte;
            $decouverte->setProspection($this);
        }

        return $this;
    }

    public function removeDecouverte(Decouvertes $decouverte): self
    {
        if ($this->decouvertes->removeElement($decouverte)) {
            // set the owning side to null (unless already changed)
            if ($decouverte->getProspection() === $this) {
                $decouverte->setProspection(null);
            }
        }

        return $this;
    }

    public function getPotentiel(): ?ReponseDecouvertes
    {
        return $this->potentiel;
    }

    public function setPotentiel(?ReponseDecouvertes $potentiel): self
    {
        $this->potentiel = $potentiel;

        return $this;
    }
}
