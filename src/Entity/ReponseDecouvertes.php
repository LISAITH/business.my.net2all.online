<?php

namespace App\Entity;

use App\Repository\ReponseDecouvertesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=ReponseDecouvertesRepository::class)
 */
#[ApiResource]
class ReponseDecouvertes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionDecouvertes::class, inversedBy="reponseDecouvertes")
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity=Decouvertes::class, mappedBy="reponse")
     */
    private $decouvertes;

    /**
     * @ORM\OneToMany(targetEntity=Prospections::class, mappedBy="potentiel")
     */
    private $prospections;

    public function __construct()
    {
        $this->decouvertes = new ArrayCollection();
        $this->prospections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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

    public function getQuestion(): ?QuestionDecouvertes
    {
        return $this->question;
    }

    public function setQuestion(?QuestionDecouvertes $question): self
    {
        $this->question = $question;

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
            $decouverte->setReponse($this);
        }

        return $this;
    }

    public function removeDecouverte(Decouvertes $decouverte): self
    {
        if ($this->decouvertes->removeElement($decouverte)) {
            // set the owning side to null (unless already changed)
            if ($decouverte->getReponse() === $this) {
                $decouverte->setReponse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Prospections>
     */
    public function getProspections(): Collection
    {
        return $this->prospections;
    }

    public function addProspection(Prospections $prospection): self
    {
        if (!$this->prospections->contains($prospection)) {
            $this->prospections[] = $prospection;
            $prospection->setPotentiel($this);
        }

        return $this;
    }

    public function removeProspection(Prospections $prospection): self
    {
        if ($this->prospections->removeElement($prospection)) {
            // set the owning side to null (unless already changed)
            if ($prospection->getPotentiel() === $this) {
                $prospection->setPotentiel(null);
            }
        }

        return $this;
    }
}
