<?php

namespace App\Entity;

use App\Repository\ProfilProspectionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=ProfilProspectionsRepository::class)
 */
#[ApiResource]
class ProfilProspections
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
     * @ORM\OneToMany(targetEntity=ParticulierProfilProspections::class, mappedBy="profil_prospection")
     */
    private $particulierProfilProspections;

    public function __construct()
    {
        $this->particulierProfilProspections = new ArrayCollection();
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

    /**
     * @return Collection<int, ParticulierProfilProspections>
     */
    public function getParticulierProfilProspections(): Collection
    {
        return $this->particulierProfilProspections;
    }

    public function addParticulierProfilProspection(ParticulierProfilProspections $particulierProfilProspection): self
    {
        if (!$this->particulierProfilProspections->contains($particulierProfilProspection)) {
            $this->particulierProfilProspections[] = $particulierProfilProspection;
            $particulierProfilProspection->setProfilProspection($this);
        }

        return $this;
    }

    public function removeParticulierProfilProspection(ParticulierProfilProspections $particulierProfilProspection): self
    {
        if ($this->particulierProfilProspections->removeElement($particulierProfilProspection)) {
            // set the owning side to null (unless already changed)
            if ($particulierProfilProspection->getProfilProspection() === $this) {
                $particulierProfilProspection->setProfilProspection(null);
            }
        }

        return $this;
    }
}
