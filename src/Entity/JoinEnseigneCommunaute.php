<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\JoinEnseigneCommunauteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JoinEnseigneCommunauteRepository::class)
 */
#[ApiResource(itemOperations: [
    'get','put', "patch", "delete",
    'get-joined-communaute' => ['route_name' => 'get-joined-communaute', 'openapi_context' => [
        'summary'     => 'Type 1 for particulier, 6 for entreprise',
        'description' => "",
        
    ]]
])]
class JoinEnseigneCommunaute
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
    private $user_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type_user;

    /**
     * @ORM\Column(type="integer")
     */
    private $communaute_id;

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

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getTypeUser(): ?string
    {
        return $this->type_user;
    }

    public function setTypeUser(string $type_user): self
    {
        $this->type_user = $type_user;

        return $this;
    }

    public function getCommunauteId(): ?int
    {
        return $this->communaute_id;
    }

    public function setCommunauteId(int $communaute_id): self
    {
        $this->communaute_id = $communaute_id;

        return $this;
    }
}
