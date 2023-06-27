<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ServicesRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UE;

/**
 * @ORM\Entity(repositoryClass=ServicesRepository::class)
 * @ApiResource()
 * @UE("libelle",message="Ce nom existe déjà")
 */
class Services
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups("compteEcash")]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=255)
     */
    #[Groups("compteEcash")]
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=5, max=255)
     */
    #[Groups("compteEcash")]
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Image(
     *     maxSize = "10M",
     *     minWidth = 200,
     *     maxWidth = 5000,
     *     minHeight = 200,
     *     maxHeight = 5000,
     *     mimeTypes = {
     *      "image/jpeg",
     *      "image/jpg",
     *      "image/png",
     *      "image/gif"
     *     }
     * )
     */
    #[Groups("compteEcash")]
    private $logo;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    #[Groups("compteEcash")]
    private $etat;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    #[Groups("compteEcash")]
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups("compteEcash")]
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $app_url;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $required_installation;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAppUrl(): ?string
    {
        return $this->app_url;
    }

    public function setAppUrl(?string $app_url): self
    {
        $this->app_url = $app_url;

        return $this;
    }

    public function isRequiredInstallation(): ?bool
    {
        return $this->required_installation;
    }

    public function setRequiredInstallation(?bool $required_installation): self
    {
        $this->required_installation = $required_installation;

        return $this;
    }
}
