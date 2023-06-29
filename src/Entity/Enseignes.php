<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\EnseigneController;
use App\Repository\EnseignesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EnseignesRepository::class)
 * @UniqueEntity(fields={"nom_enseigne"}, message="An account existed with same name")
 */

#[ApiResource(
    normalizationContext: ['groups' => ['getEnseigneWithEnterprise']],
    itemOperations: [
        'get', 'put', "patch", "delete",
        'enseignes_by_user' => ['route_name' => 'post_enseignes'],
        'active-360' => ['route_name' => 'active-360'],
        'validate_ens' => ['route_name' => 'validate_ens']
    ]
)]

class Enseignes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(["getEnseigneWithEnterprise", "getCollaborationRequestWithEnseigneAndEnterprise"])]
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprises::class, inversedBy="enseignes")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups(["getEnseigneWithEnterprise", "getCollaborationRequestWithEnseigneAndEnterprise"])]
    private $entreprise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(["getEnseigneWithEnterprise", "getCollaborationRequestWithEnseigneAndEnterprise", "getCollaborationWithEnseigneAndEnterprise"])]
    private $nom_enseigne;


    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $code_enseigne;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $url_image;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $logo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $is_validated;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $is_360_installed;

    /**
     * @ORM\OneToOne(targetEntity=Prospections::class, mappedBy="enseigne", cascade={"persist", "remove"})
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $prospections;


    /**
     * @ORM\OneToOne(targetEntity=ActivationAbonnements::class, mappedBy="enseigne", cascade={"persist", "remove"})
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $activationAbonnements;

    /**
     * @ORM\OneToMany(targetEntity=SouscriptionFormules::class, mappedBy="enseigne")
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $souscriptionFormules;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(["getEnseigneWithEnterprise"])]
    private $address;



    public function __construct()
    {
        $this->souscriptionFormules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntreprise(): ?Entreprises
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprises $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getNomEnseigne(): ?string
    {
        return $this->nom_enseigne;
    }

    public function setNomEnseigne(string $nom_enseigne): self
    {
        $this->nom_enseigne = $nom_enseigne;

        return $this;
    }

    public function getCodeEnseigne(): ?string
    {
        return $this->code_enseigne;
    }

    public function setCodeEnseigne(string $code_enseigne): self
    {
        $this->code_enseigne = $code_enseigne;

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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function isIsValidated(): ?bool
    {
        return $this->is_validated;
    }

    public function setIsValidated(?bool $is_validated): self
    {
        $this->is_validated = $is_validated;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function isIs360Installed(): ?bool
    {
        return $this->is_360_installed;
    }

    public function setIs360Installed(?bool $is_360_installed): self
    {
        $this->is_360_installed = $is_360_installed;

        return $this;
    }

    public function addSouscriptionFormule(SouscriptionFormules $souscriptionFormule): self
    {
        if (!$this->souscriptionFormules->contains($souscriptionFormule)) {
            $this->souscriptionFormules[] = $souscriptionFormule;
            $souscriptionFormule->setEnseigne($this);
        }

        return $this;
    }

    public function removeSouscriptionFormule(SouscriptionFormules $souscriptionFormule): self
    {
        if ($this->souscriptionFormules->removeElement($souscriptionFormule)) {
            // set the owning side to null (unless already changed)
            if ($souscriptionFormule->getEnseigne() === $this) {
                $souscriptionFormule->setEnseigne(null);
            }
        }

        return $this;
    }

    public function getProspections(): ?Prospections
    {
        return $this->prospections;
    }

    public function setProspections(Prospections $prospections): self
    {
        // set the owning side of the relation if necessary
        if ($prospections->getEnseigne() !== $this) {
            $prospections->setEnseigne($this);
        }

        $this->prospections = $prospections;

        return $this;
    }


    public function getActivationAbonnements(): ?ActivationAbonnements
    {
        return $this->activationAbonnements;
    }

    public function setActivationAbonnements(?ActivationAbonnements $activationAbonnements): self
    {
        // unset the owning side of the relation if necessary
        if ($activationAbonnements === null && $this->activationAbonnements !== null) {
            $this->activationAbonnements->setEnseigne($this);
        }

        // set the owning side of the relation if necessary
        if ($activationAbonnements !== null && $activationAbonnements->getEnseigne() !== $this) {
            $activationAbonnements->setEnseigne($this);
        }

        $this->activationAbonnements = $activationAbonnements;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }
}
