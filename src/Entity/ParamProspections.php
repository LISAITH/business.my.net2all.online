<?php

namespace App\Entity;

use App\Repository\ParamProspectionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=ParamProspectionsRepository::class)
 */
#[ApiResource]
class ParamProspections
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
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $done_at;

    /**
     * @ORM\OneToMany(targetEntity=ValueProspections::class, mappedBy="param_prospection")
     */
    private $valueProspections;

    public function __construct()
    {
        $this->valueProspections = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDoneAt(): ?\DateTimeInterface
    {
        return $this->done_at;
    }

    public function setDoneAt(?\DateTimeInterface $done_at): self
    {
        $this->done_at = $done_at;

        return $this;
    }

    /**
     * @return Collection<int, ValueProspections>
     */
    public function getValueProspections(): Collection
    {
        return $this->valueProspections;
    }

    public function addValueProspection(ValueProspections $valueProspection): self
    {
        if (!$this->valueProspections->contains($valueProspection)) {
            $this->valueProspections[] = $valueProspection;
            $valueProspection->setParamProspection($this);
        }

        return $this;
    }

    public function removeValueProspection(ValueProspections $valueProspection): self
    {
        if ($this->valueProspections->removeElement($valueProspection)) {
            // set the owning side to null (unless already changed)
            if ($valueProspection->getParamProspection() === $this) {
                $valueProspection->setParamProspection(null);
            }
        }

        return $this;
    }
}
