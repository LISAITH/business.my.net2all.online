<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ActivationAbonnementsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UE;

/**
 * @ORM\Entity(repositoryClass=ActivationAbonnementsRepository::class)
 * @UE("abonnement",message="Ce abonnement  a été déjà attribué")
 * @UE("enseigne",message="Cet enseigne a déjà un abonnement")
 */
#[ApiResource]
class ActivationAbonnements
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Abonnement::class, inversedBy="activationAbonnements", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $abonnement;

    /**
     * @ORM\OneToOne(targetEntity=Enseignes::class, inversedBy="activationAbonnements", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $enseigne;

    /**
     * @ORM\ManyToOne(targetEntity=PointVente::class, inversedBy="activationAbonnements")
     */
    private $point_vente;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbonnement(): ?Abonnement
    {
        return $this->abonnement;
    }

    public function setAbonnement(Abonnement $abonnement): self
    {
        $this->abonnement = $abonnement;

        return $this;
    }

    public function getEnseigne(): ?Enseignes
    {
        return $this->enseigne;
    }

    public function setEnseigne(Enseignes $enseigne): self
    {
        $this->enseigne = $enseigne;

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
}
