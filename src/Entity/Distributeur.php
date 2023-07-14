<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Repository\DistributeurRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UE;

/**
 * @ORM\Entity(repositoryClass=DistributeurRepository::class)
 * @ApiResource()
 * @UE("nom_distributeur",message="Ce nom existe déjà")
 */
class Distributeur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Length(
     * min=2,
     * max=40,
     * minMessage="le nom doit contenir au moins {{ limit }} caractères",
     * maxMessage="le nom doit contenir au plus {{ limit }} caractères")
     */
    private $nom_distributeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cle_distributeur;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="distributeur", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     * 
     */
    private $user;


    /**
     * @ORM\OneToMany(targetEntity=Abonnement::class, mappedBy="distributeur")
     */
    private $abonnements;

    /**
     * @ORM\OneToMany(targetEntity=PointVente::class, mappedBy="distributeur")
     */
    private $pointVentes;

     /**
     * @ORM\ManyToOne(targetEntity=Partenaire::class, inversedBy="distributeurs")
     */
    private $partenaire;

    public function __construct(){
        $this->abonnements = new ArrayCollection();
        $this->pointVentes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDistributeur(): ?string
    {
        return $this->nom_distributeur;
    }

    public function setNomDistributeur(string $nom_distributeur): self
    {
        $this->nom_distributeur = $nom_distributeur;

        return $this;
    }

    public function getCleDistributeur(): ?string
    {
        return $this->cle_distributeur;
    }

    public function setCleDistributeur(string $cle_distributeur): self
    {
        $this->cle_distributeur = $cle_distributeur;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user =NULL): self
    {
        $this->user = $user;

        return $this;
    }

   /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): self
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements[] = $abonnement;
            $abonnement->setDistributeur($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): self
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getDistributeur() === $this) {
                $abonnement->setDistributeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PointVente>
     */
    public function getPointVentes(): Collection
    {
        return $this->pointVentes;
    }

    public function addPointVente(PointVente $pointVente): self
    {
        if (!$this->pointVentes->contains($pointVente)) {
            $this->pointVentes[] = $pointVente;
            $pointVente->setDistributeur($this);
        }

        return $this;
    }

    public function removePointVente(PointVente $pointVente): self
    {
        if ($this->pointVentes->removeElement($pointVente)) {
            // set the owning side to null (unless already changed)
            if ($pointVente->getDistributeur() === $this) {
                $pointVente->setDistributeur(null);
            }
        }

        return $this;
    }

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(?Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    public function __toString()
    {
        return $this->nom_distributeur;
    }
}