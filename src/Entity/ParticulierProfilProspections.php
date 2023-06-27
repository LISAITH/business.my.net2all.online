<?php

namespace App\Entity;

use App\Repository\ParticulierProfilProspectionsRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=ParticulierProfilProspectionsRepository::class)
 */
#[ApiResource]
class ParticulierProfilProspections
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Particuliers::class, inversedBy="particulierProfilProspections", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $particulier;

    /**
     * @ORM\ManyToOne(targetEntity=ProfilProspections::class, inversedBy="particulierProfilProspections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profil_prospection;

    /**
     * @ORM\ManyToOne(targetEntity=Particuliers::class, inversedBy="parentParticulier")
     */
    private $parent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parent_parent;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticulier(): ?Particuliers
    {
        return $this->particulier;
    }

    public function setParticulier(Particuliers $particulier): self
    {
        $this->particulier = $particulier;

        return $this;
    }

    public function getProfilProspection(): ?ProfilProspections
    {
        return $this->profil_prospection;
    }

    public function setProfilProspection(?ProfilProspections $profil_prospection): self
    {
        $this->profil_prospection = $profil_prospection;

        return $this;
    }

    public function getParent(): ?Particuliers
    {
        return $this->parent;
    }

    public function setParent(?Particuliers $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParentParent(): ?string
    {
        return $this->parent_parent;
    }

    public function setParentParent(?string $parent_parent): self
    {
        $this->parent_parent = $parent_parent;

        return $this;
    }

  
}
