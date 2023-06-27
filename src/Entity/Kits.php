<?php

namespace App\Entity;

use App\Repository\KitsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=KitsRepository::class)
 */
class Kits
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
    private $numero_serie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero_activation;



    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

  
 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroSerie(): ?string
    {
        return $this->numero_serie;
    }

    public function setNumeroSerie(string $numero_serie): self
    {
        $this->numero_serie = $numero_serie;

        return $this;
    }

    public function getNumeroActivation(): ?string
    {
        return $this->numero_activation;
    }

    public function setNumeroActivation(string $numero_activation): self
    {
        $this->numero_activation = $numero_activation;

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
