<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PartenaireRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UE;

/**
 * @ORM\Entity(repositoryClass=PartenaireRepository::class)
 * @ApiResource()
 * @UE("nom_partenaire",message="Ce nom existe déjà")
 */
class Partenaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     * min=2,
     * max=40,
     * minMessage="le nom doit contenir au moins {{limit}} caractères",
     * minMessage="le nom doit contenir au plus {{limit}} caractères")
     * @Groups({"user:read", "user:write"})
     */
    private $nom_partenaire;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Valid()
     * @Groups({"user:read", "user:write"})
     */
    private $code_pays;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Valid()
     * @Groups({"user:read", "user:write"})
     */
    private $cle_partenaire;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="partenaire", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $user;


    /**
     * @ORM\OneToMany(targetEntity=Distributeur::class, mappedBy="partenaire")
     */
    private $distributeurs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPartenaire(): ?string
    {
        return $this->nom_partenaire;
    }

    public function setNomPartenaire(string $nom_partenaire): self
    {
        $this->nom_partenaire = $nom_partenaire;

        return $this;
    }

    public function getCodePays(): ?string
    {
        return $this->code_pays;
    }

    public function setCodePays(string $code_pays): self
    {
        $this->code_pays = $code_pays;

        return $this;
    }

    public function getClePartenaire(): ?string
    {
        return $this->cle_partenaire;
    }

    public function setClePartenaire(string $cle_partenaire): self
    {
        $this->cle_partenaire = $cle_partenaire;

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


    /**
     * @return Collection<int, Distributeur>
     */
    public function getDistributeurs(): Collection
    {
        return $this->distributeurs;
    }

    public function addDistributeur(Distributeur $distributeur): self
    {
        if (!$this->distributeurs->contains($distributeur)) {
            $this->distributeurs[] = $distributeur;
            $distributeur->setPartenaire($this);
        }

        return $this;
    }

    public function removeDistributeur(Distributeur $distributeur): self
    {
        if ($this->distributeurs->removeElement($distributeur)) {
            // set the owning side to null (unless already changed)
            if ($distributeur->getPartenaire() === $this) {
                $distributeur->setPartenaire(null);
            }
        }

        return $this;
    }
}
