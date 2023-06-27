<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CollaborationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CollaborationRepository::class)
 */
#[ApiResource(itemOperations: [
    'get','put', "patch", "delete",
    'get-collab' => ['route_name' => 'get-collab', 'openapi_context' => [
        'summary'     => 'Get',
        'description' => "",
        
    ]],
    'get-collab-member' => ['route_name' => 'get-collab-member', 'openapi_context' => [
        'summary'     => 'Get',
        'description' => "",
        
    ]],
    
])]
class Collaboration
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
    private $enseigne_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $service_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $joined_at;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $profiles = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $droits = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identification_number;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnseigneId(): ?int
    {
        return $this->enseigne_id;
    }

    public function setEnseigneId(int $enseigne_id): self
    {
        $this->enseigne_id = $enseigne_id;

        return $this;
    }

    public function getServiceId(): ?int
    {
        return $this->service_id;
    }

    public function setServiceId(int $service_id): self
    {
        $this->service_id = $service_id;

        return $this;
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

    public function getJoinedAt(): ?\DateTimeInterface
    {
        return $this->joined_at;
    }

    public function setJoinedAt(\DateTimeInterface $joined_at): self
    {
        $this->joined_at = $joined_at;

        return $this;
    }

    public function getProfiles(): ?array
    {
        return $this->profiles;
    }

    public function setProfiles(?array $profiles): self
    {
        $this->profiles = $profiles;

        return $this;
    }

    public function getDroits(): ?array
    {
        return $this->droits;
    }

    public function setDroits(?array $droits): self
    {
        $this->droits = $droits;

        return $this;
    }

    public function getIdentificationNumber(): ?string
    {
        return $this->identification_number;
    }

    public function setIdentificationNumber(string $identification_number): self
    {
        $this->identification_number = $identification_number;

        return $this;
    }
}
