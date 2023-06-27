<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\FacturesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FacturesRepository::class)
 */
#[ApiResource(itemOperations: [
    'get','put', "patch", "delete",
    'get-factures' => ['route_name' => 'get-factures', 'openapi_context' => [
        'summary'     => 'Get',
        'description' => "",
        
    ]],
])]
class Factures
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
    private $enseigne;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $client_name;

    /**
     * @ORM\Column(type="integer")
     */
    private $client_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $facture_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $facture_ref;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prixttc;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pdflink;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $data_nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $data_prenoms;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $data_phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $data_logo;

    /**
     * @ORM\Column(type="integer")
     */
    private $particulier_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnseigne(): ?string
    {
        return $this->enseigne;
    }

    public function setEnseigne(string $enseigne): self
    {
        $this->enseigne = $enseigne;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->client_name;
    }

    public function setClientName(string $client_name): self
    {
        $this->client_name = $client_name;

        return $this;
    }

    public function getClientId(): ?int
    {
        return $this->client_id;
    }

    public function setClientId(int $client_id): self
    {
        $this->client_id = $client_id;

        return $this;
    }

    public function getFactureId(): ?int
    {
        return $this->facture_id;
    }

    public function setFactureId(int $facture_id): self
    {
        $this->facture_id = $facture_id;

        return $this;
    }

    public function getFactureRef(): ?string
    {
        return $this->facture_ref;
    }

    public function setFactureRef(string $facture_ref): self
    {
        $this->facture_ref = $facture_ref;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPrixttc(): ?string
    {
        return $this->prixttc;
    }

    public function setPrixttc(string $prixttc): self
    {
        $this->prixttc = $prixttc;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPdflink(): ?string
    {
        return $this->pdflink;
    }

    public function setPdflink(string $pdflink): self
    {
        $this->pdflink = $pdflink;

        return $this;
    }

    public function getDataNom(): ?string
    {
        return $this->data_nom;
    }

    public function setDataNom(string $data_nom): self
    {
        $this->data_nom = $data_nom;

        return $this;
    }

    public function getDataPrenoms(): ?string
    {
        return $this->data_prenoms;
    }

    public function setDataPrenoms(string $data_prenoms): self
    {
        $this->data_prenoms = $data_prenoms;

        return $this;
    }

    public function getDataPhone(): ?string
    {
        return $this->data_phone;
    }

    public function setDataPhone(string $data_phone): self
    {
        $this->data_phone = $data_phone;

        return $this;
    }

    public function getDataLogo(): ?string
    {
        return $this->data_logo;
    }

    public function setDataLogo(string $data_logo): self
    {
        $this->data_logo = $data_logo;

        return $this;
    }

    public function getParticulierId(): ?int
    {
        return $this->particulier_id;
    }

    public function setParticulierId(int $particulier_id): self
    {
        $this->particulier_id = $particulier_id;

        return $this;
    }
}
