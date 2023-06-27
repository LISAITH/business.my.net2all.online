<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BanksRepository;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=BanksRepository::class)
 */
#[ApiResource]
class Banks
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
    private $nom_bank;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_image;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomBank(): ?string
    {
        return $this->nom_bank;
    }

    public function setNomBank(string $nom_bank): self
    {
        $this->nom_bank = $nom_bank;

        return $this;
    }

    public function getUrlImage(): ?string
    {
        return $this->url_image;
    }

    public function setUrlImage(?string $url_image): self
    {
        $this->url_image = $url_image;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
