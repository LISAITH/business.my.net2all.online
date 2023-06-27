<?php

namespace App\Entity;

use App\Repository\DecouvertesRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=DecouvertesRepository::class)
 */
#[ApiResource]
class Decouvertes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionDecouvertes::class, inversedBy="decouvertes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity=ReponseDecouvertes::class, inversedBy="decouvertes")
     */
    private $reponse;

    /**
     * @ORM\ManyToOne(targetEntity=Prospections::class, inversedBy="decouvertes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $prospection;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?QuestionDecouvertes
    {
        return $this->question;
    }

    public function setQuestion(?QuestionDecouvertes $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getReponse(): ?ReponseDecouvertes
    {
        return $this->reponse;
    }

    public function setReponse(?ReponseDecouvertes $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getProspection(): ?Prospections
    {
        return $this->prospection;
    }

    public function setProspection(?Prospections $prospection): self
    {
        $this->prospection = $prospection;

        return $this;
    }
}
