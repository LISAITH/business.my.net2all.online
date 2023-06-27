<?php

namespace App\Entity;

use App\Repository\QuestionDecouvertesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=QuestionDecouvertesRepository::class)
 */
#[ApiResource]
class QuestionDecouvertes
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
     * @ORM\OneToMany(targetEntity=ReponseDecouvertes::class, mappedBy="question")
     */
    private $reponseDecouvertes;

    /**
     * @ORM\OneToMany(targetEntity=Decouvertes::class, mappedBy="question")
     */
    private $decouvertes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $multiple;

    public function __construct()
    {
        $this->reponseDecouvertes = new ArrayCollection();
        $this->decouvertes = new ArrayCollection();
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

    /**
     * @return Collection<int, ReponseDecouvertes>
     */
    public function getReponseDecouvertes(): Collection
    {
        return $this->reponseDecouvertes;
    }

    public function addReponseDecouverte(ReponseDecouvertes $reponseDecouverte): self
    {
        if (!$this->reponseDecouvertes->contains($reponseDecouverte)) {
            $this->reponseDecouvertes[] = $reponseDecouverte;
            $reponseDecouverte->setQuestion($this);
        }

        return $this;
    }

    public function removeReponseDecouverte(ReponseDecouvertes $reponseDecouverte): self
    {
        if ($this->reponseDecouvertes->removeElement($reponseDecouverte)) {
            // set the owning side to null (unless already changed)
            if ($reponseDecouverte->getQuestion() === $this) {
                $reponseDecouverte->setQuestion(null);
            }
        }

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
            $decouverte->setQuestion($this);
        }

        return $this;
    }

    public function removeDecouverte(Decouvertes $decouverte): self
    {
        if ($this->decouvertes->removeElement($decouverte)) {
            // set the owning side to null (unless already changed)
            if ($decouverte->getQuestion() === $this) {
                $decouverte->setQuestion(null);
            }
        }

        return $this;
    }

    public function isMultiple(): ?bool
    {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;

        return $this;
    }
}
