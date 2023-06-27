<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaysRepository::class)
 */
#[ApiResource]
class Pays
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
    private $libelle_pays;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $indicatif;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Entreprises::class, mappedBy="pays")
     */
    private $entreprises;

    /**
     * @ORM\OneToMany(targetEntity=Particuliers::class, mappedBy="pays")
     */
    private $particuliers;

    public function __construct()
    {
        $this->entreprises = new ArrayCollection();
        $this->particuliers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibellePays(): ?string
    {
        return $this->libelle_pays;
    }

    public function setLibellePays(string $libelle_pays): self
    {
        $this->libelle_pays = $libelle_pays;

        return $this;
    }

    public function getIndicatif(): ?string
    {
        return $this->indicatif;
    }

    public function setIndicatif(string $indicatif): self
    {
        $this->indicatif = $indicatif;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Entreprises>
     */
    public function getEntreprises(): Collection
    {
        return $this->entreprises;
    }

    public function addEntreprise(Entreprises $entreprise): self
    {
        if (!$this->entreprises->contains($entreprise)) {
            $this->entreprises[] = $entreprise;
            $entreprise->setPays($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprises $entreprise): self
    {
        if ($this->entreprises->removeElement($entreprise)) {
            // set the owning side to null (unless already changed)
            if ($entreprise->getPays() === $this) {
                $entreprise->setPays(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Particuliers>
     */
    public function getParticuliers(): Collection
    {
        return $this->particuliers;
    }

    public function addParticulier(Particuliers $particulier): self
    {
        if (!$this->particuliers->contains($particulier)) {
            $this->particuliers[] = $particulier;
            $particulier->setPays($this);
        }

        return $this;
    }

    public function removeParticulier(Particuliers $particulier): self
    {
        if ($this->particuliers->removeElement($particulier)) {
            // set the owning side to null (unless already changed)
            if ($particulier->getPays() === $this) {
                $particulier->setPays(null);
            }
        }

        return $this;
    }
}
