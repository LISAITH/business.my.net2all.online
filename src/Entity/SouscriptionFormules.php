<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SouscriptionFormulesRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SouscriptionFormulesRepository::class)
 */
#[ApiResource]
class SouscriptionFormules
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Enseignes::class, inversedBy="souscriptionFormules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $enseigne;

    /**
     * @ORM\ManyToOne(targetEntity=Formule::class, inversedBy="souscriptionFormules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formule;

    /**
     * @ORM\ManyToOne(targetEntity=PointVente::class, inversedBy="souscriptionFormules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $point_vente;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_validated;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $date_debut;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $date_fin;

    public function __construct()
    {
        $this->date_debut = new \DatetimeImmutable();
        $date = new \DateTimeImmutable();
        $this->date_fin = $date->add(new \DateInterval('P30D'));
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnseigne(): ?Enseignes
    {
        return $this->enseigne;
    }

    public function setEnseigne(?Enseignes $enseigne): self
    {
        $this->enseigne = $enseigne;

        return $this;
    }

    public function getFormule(): ?Formule
    {
        return $this->formule;
    }

    public function setFormule(?Formule $formule): self
    {
        $this->formule = $formule;

        return $this;
    }

    public function getPointVente(): ?PointVente
    {
        return $this->point_vente;
    }

    public function setPointVente(?PointVente $point_vente): self
    {
        $this->point_vente = $point_vente;

        return $this;
    }

    public function isIsValidated(): ?bool
    {
        return $this->is_validated;
    }

    public function setIsValidated(bool $is_validated): self
    {
        $this->is_validated = $is_validated;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeImmutable $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeImmutable $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }
}