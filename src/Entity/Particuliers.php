<?php

namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ParticuliersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ParticuliersRepository::class)
 */
#[ApiResource]
class Particuliers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    #[Groups("compteEcash")]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read","user:write"})
     */
    #[Groups("compteEcash")]
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read","user:write"})
     */
    #[Groups("compteEcash")]
    private $prenoms;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read","user:write"})
     */
    #[Groups("compteEcash")]
    private $num_tel;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="particuliers")
     *  @Groups({"user:read","user:write"})
     */
    private $pays;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="particuliers",cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups("compteEcash")]
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=CompteEcash::class, mappedBy="particulier", cascade={"persist", "remove"})
     * @Groups({"user:read","user:write"})
     */
    private $compteEcash;

    /**
     * @ORM\OneToOne(targetEntity=ParticulierProfilProspections::class, mappedBy="particulier", cascade={"persist", "remove"})
     */
    private $particulierProfilProspections;

    /**
     * @ORM\OneToMany(targetEntity=ParticulierProfilProspections::class, mappedBy="parent")
     */
    private $parentParticulier;

    /**
     * @ORM\OneToMany(targetEntity=Prospections::class, mappedBy="particulier")
     */
    private $prospections;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $genre;

    /**
     * @ORM\OneToMany(targetEntity=ProspectionPaiements::class, mappedBy="particulier")
     */
    private $prospectionPaiements;

    public function __construct()
    {
        $this->parentParticulier = new ArrayCollection();
        $this->prospections = new ArrayCollection();
        $this->prospectionPaiements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }


    public function getNomPrenoms(): ?string
    {
        return $this->nom." ". $this->prenoms;
    }

    public function setPrenoms(string $prenoms): self
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->num_tel;
    }

    public function setNumTel(string $num_tel): self
    {
        $this->num_tel = $num_tel;

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCompteEcash(): ?CompteEcash
    {
        return $this->compteEcash;
    }

    public function setCompteEcash(?CompteEcash $compteEcash): self
    {
        // unset the owning side of the relation if necessary
        if ($compteEcash === null && $this->compteEcash !== null) {
            $this->compteEcash->setParticulier(null);
        }

        // set the owning side of the relation if necessary
        if ($compteEcash !== null && $compteEcash->getParticulier() !== $this) {
            $compteEcash->setParticulier($this);
        }

        $this->compteEcash = $compteEcash;

        return $this;
    }

    public function getParticulierProfilProspections(): ?ParticulierProfilProspections
    {
        return $this->particulierProfilProspections;
    }
    public function getProfilId(): ?int
    {
        if($this->particulierProfilProspections)
        return $this->particulierProfilProspections->getProfilProspection()->getId();
        else return null;
    }
    public function getProfilLibelle()
    {
        if($this->particulierProfilProspections)
        return $this->particulierProfilProspections->getProfilProspection()->getLibelle();
        else return null;
    }

    public function setParticulierProfilProspections(ParticulierProfilProspections $particulierProfilProspections): self
    {
        // set the owning side of the relation if necessary
        if ($particulierProfilProspections->getParticulier() !== $this) {
            $particulierProfilProspections->setParticulier($this);
        }

        $this->particulierProfilProspections = $particulierProfilProspections;

        return $this;
    }

    /**
     * @return Collection<int, ParticulierProfilProspections>
     */
    public function getParentParticulier(): Collection
    {
        return $this->parentParticulier;
    }

    public function addParentParticulier(ParticulierProfilProspections $parentParticulier): self
    {
        if (!$this->parentParticulier->contains($parentParticulier)) {
            $this->parentParticulier[] = $parentParticulier;
            $parentParticulier->setParent($this);
        }

        return $this;
    }

    public function removeParentParticulier(ParticulierProfilProspections $parentParticulier): self
    {
        if ($this->parentParticulier->removeElement($parentParticulier)) {
            // set the owning side to null (unless already changed)
            if ($parentParticulier->getParent() === $this) {
                $parentParticulier->setParent(null);
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
            $prospection->setParticulier($this);
        }

        return $this;
    }

    public function removeProspection(Prospections $prospection): self
    {
        if ($this->prospections->removeElement($prospection)) {
            // set the owning side to null (unless already changed)
            if ($prospection->getParticulier() === $this) {
                $prospection->setParticulier(null);
            }
        }

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection<int, ProspectionPaiements>
     */
    public function getProspectionPaiements(): Collection
    {
        return $this->prospectionPaiements;
    }

    public function addProspectionPaiement(ProspectionPaiements $prospectionPaiement): self
    {
        if (!$this->prospectionPaiements->contains($prospectionPaiement)) {
            $this->prospectionPaiements[] = $prospectionPaiement;
            $prospectionPaiement->setParticulier($this);
        }

        return $this;
    }

    public function removeProspectionPaiement(ProspectionPaiements $prospectionPaiement): self
    {
        if ($this->prospectionPaiements->removeElement($prospectionPaiement)) {
            // set the owning side to null (unless already changed)
            if ($prospectionPaiement->getParticulier() === $this) {
                $prospectionPaiement->setParticulier(null);
            }
        }

        return $this;
    }
}
