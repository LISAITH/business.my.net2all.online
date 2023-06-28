<?php

namespace App\Entity;

use App\Controller\Api\ApiMeController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"numero"}, message="There is already an account with this numero")
 * @ApiResource(
 * normalizationContext={"groups"={"user:read"}},
 * denormalizationContext={"groups"={"user:write"}}
 * )
 */
//#[ApiResource(
//    collectionOperations: [
//        "me" => [
//            'pagination_enabled' => false,
//            'path' => '/me',
//            'method' => 'get',
//            'controller' => ApiMeController::class,
//            'read' => false,
//            'security' => 'is_granted("ROLE_USER")',
//        ]
//    ],
//    itemOperations:
//)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read","user:write"})
     */
    #[Groups("compteEcash")]
    private $email;

    /**
     * @Groups({"user:read"})
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @Groups({"user:write"})
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups({"user:write"})
     * @SerializedName("password")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read"})
     */
    private $numero;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read","user:write"})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Entreprises::class, mappedBy="user")
     * @Groups({"user:read","user:write"})
     */
    private $entreprises;

    /**
     * @ORM\OneToMany(targetEntity=Particuliers::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"user:read","user:write"})
     */
    private $particuliers;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="user",cascade={"persist"})
     * @Groups({"user:read","user:write"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read","user:write"})
     */
    private $image_url;
    /** 
     * @ORM\OneToMany(targetEntity=VirementsInterBancaire::class, mappedBy="id_user")
     */
    private $virementsInterBancaires;

    /**
     * @ORM\OneToOne(targetEntity=Prospections::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $prospections;

    /**
     * @ORM\OneToOne(targetEntity=Distributeur::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $distributeur;

    /**
     * @ORM\OneToOne(targetEntity=Partenaire::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $partenaire;

    /**
     * @ORM\OneToOne(targetEntity=PointVente::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $pointVente;

    /**
     * @ORM\OneToOne(targetEntity=Plateforme::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $plateforme;

    public function __construct()
    {
        
        $this->entreprises = new ArrayCollection();
        $this->particuliers = new ArrayCollection();
        $this->virementsInterBancaires = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function __toString()
    {
        return 'USER';
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status = false): self
    {
        $this->status = $status;
        return $this;
    }

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(Partenaire $partenaire): self
    {
        // set the owning side of the relation if necessary
        if ($partenaire->getUser() !== $this) {
            $partenaire->setUser($this);
        }

        $this->partenaire = $partenaire;

        return $this;
    }

    public function getPlateforme(): ?Plateforme
    {
        return $this->plateforme;
    }

    public function setPlateforme(Plateforme $plateforme): self
    {
        // set the owning side of the relation if necessary
        if ($plateforme->getUser() !== $this) {
            $plateforme->setUser($this);
        }

        $this->plateforme = $plateforme;

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
            $entreprise->setUser($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprises $entreprise): self
    {
        if ($this->entreprises->removeElement($entreprise)) {
            // set the owning side to null (unless already changed)
            if ($entreprise->getUser() === $this) {
                $entreprise->setUser(null);
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
            $particulier->setUser($this);
        }

        return $this;
    }

    public function removeParticulier(Particuliers $particulier): self
    {
        if ($this->particuliers->removeElement($particulier)) {
            // set the owning side to null (unless already changed)
            if ($particulier->getUser() === $this) {
                $particulier->setUser(null);
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
        if ($prospections->getUser() !== $this) {
            $prospections->setUser($this);
        }

        $this->prospections = $prospections;

        return $this;
    }



    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): self
    {
        $this->image_url = $image_url;
        return $this;
    }
    /**
     * @return Collection<int, VirementsInterBancaire>
     */
    public function getVirementsInterBancaires(): Collection
    {
        return $this->virementsInterBancaires;
    }

    public function addVirementsInterBancaire(VirementsInterBancaire $virementsInterBancaire): self
    {
        if (!$this->virementsInterBancaires->contains($virementsInterBancaire)) {
            $this->virementsInterBancaires[] = $virementsInterBancaire;
            $virementsInterBancaire->setIdUser($this);
        }

        return $this;
    }

    public function removeVirementsInterBancaire(VirementsInterBancaire $virementsInterBancaire): self
    {
        if ($this->virementsInterBancaires->removeElement($virementsInterBancaire)) {
            // set the owning side to null (unless already changed)
            if ($virementsInterBancaire->getIdUser() === $this) {
                $virementsInterBancaire->setIdUser(null);
            }
        }

        return $this;
    }

    public function getDistributeur(): ?Distributeur
    {
        return $this->distributeur;
    }

    public function setDistributeur(Distributeur $distributeur): self
    {
        // set the owning side of the relation if necessary
        if ($distributeur->getUser() !== $this) {
            $distributeur->setUser($this);
        }

        $this->distributeur = $distributeur;

        return $this;
    }
    
    public function getPointVente(): ?PointVente
    {
        return $this->pointVente;
    }

    public function setPointVente(?PointVente $pointVente): self
    {
        // unset the owning side of the relation if necessary
        if ($pointVente === null && $this->pointVente !== null) {
            $this->pointVente->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($pointVente !== null && $pointVente->getUser() !== $this) {
            $pointVente->setUser($this);
        }

        $this->pointVente = $pointVente;

        return $this;
    }


}