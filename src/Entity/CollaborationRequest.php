<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CollaborationRequestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CollaborationRequestRepository::class)
 */
#[ApiResource(itemOperations: [
   'get', 'put', "patch", "delete",
    'get-collab-req' => ['route_name' => 'get-collab-req', 'openapi_context' => [
        'summary'     => 'Get',
        'description' => "",
        
    ]],
])]
class CollaborationRequest
{	
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
	
    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $service_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $enseigne_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $expediteur_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $expediteur_type;

    /**
     * @ORM\Column(type="integer")
     */
    private $destinataire_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $destinataire_type;
	 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getEnseigneId(): ?int
    {
        return $this->enseigne_id;
    }

    public function setEnseigneId(int $enseigne_id): self
    {
        $this->enseigne_id = $enseigne_id;

        return $this;
    }

    public function getExpediteurId(): ?int
    {
        return $this->expediteur_id;
    }

    public function setExpediteurId(int $expediteur_id): self
    {
        $this->expediteur_id = $expediteur_id;

        return $this;
    }

    public function getExpediteurType(): ?string
    {
        return $this->expediteur_type;
    }

    public function setExpediteurType(string $expediteur_type): self
    {
        $this->expediteur_type = $expediteur_type;

        return $this;
    }

    public function getDestinataireId(): ?int
    {
        return $this->destinataire_id;
    }

    public function setDestinataireId(int $destinataire_id): self
    {
        $this->destinataire_id = $destinataire_id;

        return $this;
    }

    public function getDestinataireType(): ?string
    {
        return $this->destinataire_type;
    }

    public function setDestinataireType(string $destinataire_type): self
    {
        $this->destinataire_type = $destinataire_type;

        return $this;
    }
	
	

    
}
