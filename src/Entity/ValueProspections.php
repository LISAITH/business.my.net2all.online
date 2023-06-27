<?php

namespace App\Entity;

use App\Repository\ValueProspectionsRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=ValueProspectionsRepository::class)
 */
#[ApiResource]
class ValueProspections
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $done_at;

    /**
     * @ORM\ManyToOne(targetEntity=ParamProspections::class, inversedBy="valueProspections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $param_prospection;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDoneAt(): ?\DateTimeInterface
    {
        return $this->done_at;
    }

    public function setDoneAt(\DateTimeInterface $done_at): self
    {
        $this->done_at = $done_at;

        return $this;
    }

    public function getParamProspection(): ?ParamProspections
    {
        return $this->param_prospection;
    }

    public function setParamProspection(?ParamProspections $param_prospection): self
    {
        $this->param_prospection = $param_prospection;

        return $this;
    }
}
