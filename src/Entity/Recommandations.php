<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RecommandationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecommandationsRepository::class)
 */
#[ApiResource]
class Recommandations
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numero_recommande;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email_recommande;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="integer")
     */
    private $pays_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $guest_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $guest_temp_password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getNumeroRecommande(): ?string
    {
        return $this->numero_recommande;
    }

    public function setNumeroRecommande(?string $numero_recommande): self
    {
        $this->numero_recommande = $numero_recommande;

        return $this;
    }

    public function getEmailRecommande(): ?string
    {
        return $this->email_recommande;
    }

    public function setEmailRecommande(?string $email_recommande): self
    {
        $this->email_recommande = $email_recommande;

        return $this;
    }

    public function getUserType(): ?int
    {
        return $this->user_type;
    }

    public function setUserType(int $user_type): self
    {
        $this->user_type = $user_type;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getPaysId(): ?int
    {
        return $this->pays_id;
    }

    public function setPaysId(int $pays_id): self
    {
        $this->pays_id = $pays_id;

        return $this;
    }

    public function getGuestId(): ?int
    {
        return $this->guest_id;
    }

    public function setGuestId(?int $guest_id): self
    {
        $this->guest_id = $guest_id;

        return $this;
    }

    public function getGuestTempPassword(): ?string
    {
        return $this->guest_temp_password;
    }

    public function setGuestTempPassword(?string $guest_temp_password): self
    {
        $this->guest_temp_password = $guest_temp_password;

        return $this;
    }
}
