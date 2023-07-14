<?php

namespace App\Entity;

use App\Repository\PointVenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=PointVenteRepository::class)
 */
#[ApiResource]
class PointVente
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
    private $nom_point_vente;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Distributeur::class, inversedBy="pointVentes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $distributeur;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="pointVente", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Abonnement::class, mappedBy="point_vente")
     */
    private $abonnements;

    /**
     * @ORM\OneToMany(targetEntity=ActivationAbonnements::class, mappedBy="point_vente")
     */
    private $activationAbonnements;

     /**
     * @ORM\OneToMany(targetEntity=SouscriptionFormules::class, mappedBy="point_vente")
     */
    private $souscriptionFormules;

    /**
     * @ORM\OneToMany(targetEntity=Recharges::class, mappedBy="point_vente")
     */
    private $recharges;

    

    public function __construct()
    {

        $this->abonnements = new ArrayCollection();
        $this->activationAbonnements = new ArrayCollection();
        $this->souscriptionFormules = new ArrayCollection();
        $this->recharges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPointVente(): ?string
    {
        return $this->nom_point_vente;
    }

    public function setNomPointVente(string $nom_point_vente): self
    {
        $this->nom_point_vente = $nom_point_vente;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status=false): self
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDistributeur(): ?Distributeur
    {
        return $this->distributeur;
    }

    public function setDistributeur(?Distributeur $distributeur): self
    {
        $this->distributeur = $distributeur;

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

    /**
     * @return Collection<int, SouscriptionFormules>
     */
    public function getSouscriptionFormules(): Collection
    {
        return $this->souscriptionFormules;
    }

    public function addSouscriptionFormule(SouscriptionFormules $souscriptionFormule): self
    {
        if (!$this->souscriptionFormules->contains($souscriptionFormule)) {
            $this->souscriptionFormules[] = $souscriptionFormule;
            $souscriptionFormule->setPointVente($this);
        }

        return $this;
    }

    public function removeSouscriptionFormule(SouscriptionFormules $souscriptionFormule): self
    {
        if ($this->souscriptionFormules->removeElement($souscriptionFormule)) {
            // set the owning side to null (unless already changed)
            if ($souscriptionFormule->getPointVente() === $this) {
                $souscriptionFormule->setPointVente(null);
            }
        }

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
            $abonnement->setPointVente($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): self
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getPointVente() === $this) {
                $abonnement->setPointVente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ActivationAbonnements>
     */
    public function getActivationAbonnements(): Collection
    {
        return $this->activationAbonnements;
    }

    public function addActivationAbonnement(ActivationAbonnements $activationAbonnement): self
    {
        if (!$this->activationAbonnements->contains($activationAbonnement)) {
            $this->activationAbonnements[] = $activationAbonnement;
            $activationAbonnement->setPointVente($this);
        }

        return $this;
    }

    public function removeActivationAbonnement(ActivationAbonnements $activationAbonnement): self
    {
        if ($this->activationAbonnements->removeElement($activationAbonnement)) {
            // set the owning side to null (unless already changed)
            if ($activationAbonnement->getPointVente() === $this) {
                $activationAbonnement->setPointVente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Recharges>
     */
    public function getRecharges(): Collection
    {
        return $this->recharges;
    }

    public function addRecharge(Recharges $recharge): self
    {
        if (!$this->recharges->contains($recharge)) {
            $this->recharges[] = $recharge;
            $recharge->setPointVente($this);
        }

        return $this;
    }

    public function removeRecharge(Recharges $recharge): self
    {
        if ($this->recharges->removeElement($recharge)) {
            // set the owning side to null (unless already changed)
            if ($recharge->getPointVente() === $this) {
                $recharge->setPointVente(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom_point_vente;
    }
}