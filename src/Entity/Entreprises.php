<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EntreprisesRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EntreprisesRepository::class)
 */
#[ApiResource]
class Entreprises
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */

    #[Groups(["compteEcash", "getEnseigneWithEnterprise"])]
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
    #[Groups(["compteEcash", "getEnseigneWithEnterprise"])]
    private $nom_entreprise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read","user:write"})
     */
    #[Groups(["compteEcash", "getEnseigneWithEnterprise"])]
    private $url_image;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read","user:write"})
     */
    #[Groups("compteEcash")]
    private $num_tel;

    /**
     * @ORM\OneToMany(targetEntity=Enseignes::class, mappedBy="entreprise", orphanRemoval=true)
     * @Groups({"user:read","user:write"})
     */
    #[Groups("compteEcash")]
    private $enseignes;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="entreprises")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user:read","user:write"})
     */
    #[Groups("compteEcash")]
    private $pays;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="entreprises",cascade={"persist"})
     */
    #[Groups("compteEcash")]
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Prospections::class, mappedBy="entreprise", cascade={"persist", "remove"})
     */
    private $prospections;

    /**
     * @ORM\OneToOne(targetEntity=CompteEcash::class, mappedBy="entreprise", cascade={"persist", "remove"})
     * @Groups({"user:read","user:write"})
     */
    private $compteEcash;



    public function __construct()
    {
        $this->enseignes = new ArrayCollection();
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

    public function setPrenoms(string $prenoms): self
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nom_entreprise;
    }

    public function setNomEntreprise(string $nom_entreprise): self
    {
        $this->nom_entreprise = $nom_entreprise;

        return $this;
    }

    public function getUrlImage(): ?string
    {
        return $this->url_image;
    }

    public function setUrlImage(string $url_image): self
    {
        $this->url_image = $url_image;

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

    /**
     * @return Collection<int, Enseigne>
     */
    public function getEnseignes(): Collection
    {
        return $this->enseignes;
    }

    public function addEnseigne(Enseignes $enseigne): self
    {
        if (!$this->enseignes->contains($enseigne)) {
            $this->enseignes[] = $enseigne;
            $enseigne->setEntreprise($this);
        }

        return $this;
    }

    public function removeEnseigne(Enseignes $enseigne): self
    {
        if ($this->enseignes->removeElement($enseigne)) {
            // set the owning side to null (unless already changed)
            if ($enseigne->getEntreprise() === $this) {
                $enseigne->setEntreprise(null);
            }
        }

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


    public function getProspections(): ?Prospections
    {
        return $this->prospections;
    }

    public function setProspections(Prospections $prospections): self
    {
        // set the owning side of the relation if necessary
        if ($prospections->getEntreprise() !== $this) {
            $prospections->setEntreprise($this);
        }

        $this->prospections = $prospections;
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
            $this->compteEcash->setEntreprise(null);
        }

        // set the owning side of the relation if necessary
        if ($compteEcash !== null && $compteEcash->getEntreprise() !== $this) {
            $compteEcash->setEntreprise($this);
        }

        $this->compteEcash = $compteEcash;


        return $this;
    }
}
