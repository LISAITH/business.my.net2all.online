<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompteEcashRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CompteEcashRepository::class)
 */
#[ApiResource(normalizationContext: ['groups' => ['compteEcash']])]
class CompteEcash
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read","user:write"})
     */
    #[Groups(["compteEcash","virementsEcash","virementsBancaires"])]
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"user:read","user:write"})
     */
    #[Groups(["compteEcash","virementsEcash","virementsBancaires"])]
    private $solde;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user:read","user:write"})
     */
    #[Groups(["compteEcash","virementsEcash","virementsBancaires"])]
    private $numeroCompte;

    /**
     * @ORM\OneToMany(targetEntity=SousCompte::class, mappedBy="compteEcash",cascade={"persist", "remove"})
     */
    #[Groups(["compteEcash","virementsEcash","virementsBancaires"])]
    private $sousComptes;

    // /**
    //  * @ORM\OneToOne(targetEntity=Particuliers::class, cascade={"persist", "remove"})
    //  */
    // #[Groups("compteEcash")]
    // private $particulier;

    // /**
    //  * @ORM\OneToOne(targetEntity=Entreprises::class, cascade={"persist", "remove"})
    //  */
    // #[Groups("compteEcash")]
    // private $entreprise;

    /**
     * @ORM\OneToMany(targetEntity=VirementsEcash::class, mappedBy="id_compte_envoyeur")
     */
    private $virementsEcashes;


    /**
     * @ORM\OneToMany(targetEntity=VirementsBancaires::class, mappedBy="id_compte_ecash")
     */
    private $virementsBancaires;

    /**
     * @ORM\OneToOne(targetEntity=Entreprises::class, inversedBy="compteEcash", cascade={"persist", "remove"})
     */
    #[Groups("compteEcash")]
    private $entreprise;

    /**
     * @ORM\OneToOne(targetEntity=Particuliers::class, inversedBy="compteEcash", cascade={"persist", "remove"})
     */
    #[Groups("compteEcash")]
    private $particulier;

    /**
     * @ORM\OneToMany(targetEntity=Recharges::class, mappedBy="compte_ecash")
     */
    private $recharges;

    /**
     * @ORM\OneToMany(targetEntity=VirementsEcash::class, mappedBy="id_compte_receveur")
     */
    private $virementsEcashe;

    // /**
    //  * @ORM\OneToOne(targetEntity=Entreprises::class, inversedBy="compteEcash", cascade={"persist", "remove"})
    //  */
    // private $entreprise_id;

    public function __construct()
    {
        $this->sousComptes = new ArrayCollection();
        $this->virementsEcashes = new ArrayCollection();
        $this->virementsBancaires = new ArrayCollection();
        $this->recharges = new ArrayCollection();
        $this->virementsEcashe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }

    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;

        return $this;
    }

    /**
     * @return Collection<int, SousCompte>
     */
    public function getSousComptes(): Collection
    {
        return $this->sousComptes;
    }

    public function addSousCompte(SousCompte $sousCompte): self
    {
        if (!$this->sousComptes->contains($sousCompte)) {
            $this->sousComptes[] = $sousCompte;
            $sousCompte->setCompteEcash($this);
        }

        return $this;
    }

    public function removeSousCompte(SousCompte $sousCompte): self
    {
        if ($this->sousComptes->removeElement($sousCompte)) {
            // set the owning side to null (unless already changed)
            if ($sousCompte->getCompteEcash() === $this) {
                $sousCompte->setCompteEcash(null);
            }
        }

        return $this;
    }

    // public function getParticulier(): ?Particuliers
    // {
    //     return $this->particulier;
    // }

    // public function setParticulier(?Particuliers $particulier): self
    // {
    //     $this->particulier = $particulier;

    //     return $this;
    // }

    // public function getEntreprise(): ?Entreprises
    // {
    //     return $this->entreprise;
    // }

    // public function setEntreprise(?Entreprises $entreprise): self
    // {
    //     $this->entreprise = $entreprise;

    //     return $this;
    // }

    /**
     * @return Collection<int, VirementsEcash>
     */
    public function getVirementsEcashes(): Collection
    {
        return $this->virementsEcashes;
    }

    public function addVirementsEcash(VirementsEcash $virementsEcash): self
    {
        if (!$this->virementsEcashes->contains($virementsEcash)) {
            $this->virementsEcashes[] = $virementsEcash;
            $virementsEcash->setIdCompteEnvoyeur($this);
        }

        return $this;
    }

    public function removeVirementsEcash(VirementsEcash $virementsEcash): self
    {
        if ($this->virementsEcashes->removeElement($virementsEcash)) {
            // set the owning side to null (unless already changed)
            if ($virementsEcash->getIdCompteEnvoyeur() === $this) {
                $virementsEcash->setIdCompteEnvoyeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VirementsBancaires>
     */
    public function getVirementsBancaires(): Collection
    {
        return $this->virementsBancaires;
    }

    public function addVirementsBancaire(VirementsBancaires $virementsBancaire): self
    {
        if (!$this->virementsBancaires->contains($virementsBancaire)) {
            $this->virementsBancaires[] = $virementsBancaire;
            $virementsBancaire->setIdCompteEcash($this);
        }

        return $this;
    }

    public function removeVirementsBancaire(VirementsBancaires $virementsBancaire): self
    {
        if ($this->virementsBancaires->removeElement($virementsBancaire)) {
            // set the owning side to null (unless already changed)
            if ($virementsBancaire->getIdCompteEcash() === $this) {
                $virementsBancaire->setIdCompteEcash(null);
            }
        }

        return $this;
    }

    // public function getEntrepriseId(): ?Entreprises
    // {
    //     return $this->entreprise_id;
    // }

    // public function setEntrepriseId(?Entreprises $entreprise_id): self
    // {
    //     $this->entreprise_id = $entreprise_id;

    //     return $this;
    // }

    public function getEntreprise(): ?Entreprises
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprises $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getParticulier(): ?Particuliers
    {
        return $this->particulier;
    }

    public function setParticulier(?Particuliers $particulier): self
    {
        $this->particulier = $particulier;

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
            $recharge->setCompteEcash($this);
        }

        return $this;
    }

    public function removeRecharge(Recharges $recharge): self
    {
        if ($this->recharges->removeElement($recharge)) {
            // set the owning side to null (unless already changed)
            if ($recharge->getCompteEcash() === $this) {
                $recharge->setCompteEcash(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VirementsEcash>
     */
    public function getVirementsEcashe(): Collection
    {
        return $this->virementsEcashe;
    }

    public function addVirementsEcashe(VirementsEcash $virementsEcashe): self
    {
        if (!$this->virementsEcashe->contains($virementsEcashe)) {
            $this->virementsEcashe[] = $virementsEcashe;
            $virementsEcashe->setIdCompteReceveur($this);
        }

        return $this;
    }

    public function removeVirementsEcashe(VirementsEcash $virementsEcashe): self
    {
        if ($this->virementsEcashe->removeElement($virementsEcashe)) {
            // set the owning side to null (unless already changed)
            if ($virementsEcashe->getIdCompteReceveur() === $this) {
                $virementsEcashe->setIdCompteReceveur(null);
            }
        }

        return $this;
    }
}
