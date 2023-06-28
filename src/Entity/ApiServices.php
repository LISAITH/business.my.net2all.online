<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ApiServicesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApiServicesRepository::class)
 */
#[ApiResource(itemOperations: [
    'get','put', "patch", "delete",
    'get-affiliation' => ['route_name' => 'get-affiliation', 'openapi_context' => [
        'summary'     => 'Get',
        'description' => "",
        
    ]],
    'update-afiiliation' => ['route_name' => 'update-afiiliation'],
    'afiiliation-installation-demande' => ['route_name' => 'afiiliation-installation-demande'],
    'afiiliation-treate-demande' => ['route_name' => 'afiiliation-treate-demande'],
    'apiservice-type' => ['route_name' => 'apiservice-type'],
    'get-one-affiliation' => ['route_name' => 'get-one-affiliation'],
])]
class ApiServices
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
    private $id_enseigne;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_services;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_entreprise;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_installed;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $nim;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $type_ifu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_treated;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_set;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_admin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $baseurl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $webstockUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $api_key;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $mode;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $can_send_sms;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title_sms;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $installation_status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $is_user_validator;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEnseigne(): ?int
    {
        return $this->id_enseigne;
    }

    public function setIdEnseigne(int $id_enseigne): self
    {
        $this->id_enseigne = $id_enseigne;

        return $this;
    }

    public function getIdServices(): ?int
    {
        return $this->id_services;
    }

    public function setIdServices(int $id_services): self
    {
        $this->id_services = $id_services;

        return $this;
    }

    public function getIdEntreprise(): ?int
    {
        return $this->id_entreprise;
    }

    public function setIdEntreprise(int $id_entreprise): self
    {
        $this->id_entreprise = $id_entreprise;

        return $this;
    }

    public function isIsInstalled(): ?bool
    {
        return $this->is_installed;
    }

    public function setIsInstalled(?bool $is_installed): self
    {
        $this->is_installed = $is_installed;

        return $this;
    }

    public function getNim(): ?string
    {
        return $this->nim;
    }

    public function setNim(?string $nim): self
    {
        $this->nim = $nim;

        return $this;
    }

    public function getTypeIfu(): ?string
    {
        return $this->type_ifu;
    }

    public function setTypeIfu(?string $type_ifu): self
    {
        $this->type_ifu = $type_ifu;

        return $this;
    }

    public function isIsTreated(): ?bool
    {
        return $this->is_treated;
    }

    public function setIsTreated(?bool $is_treated): self
    {
        $this->is_treated = $is_treated;

        return $this;
    }

    public function isIsSet(): ?bool
    {
        return $this->is_set;
    }

    public function setIsSet(?bool $is_set): self
    {
        $this->is_set = $is_set;

        return $this;
    }

    public function isIsAdmin(): ?bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(?bool $is_admin): self
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    public function getBaseurl(): ?string
    {
        return $this->baseurl;
    }

    public function setBaseurl(?string $baseurl): self
    {
        $this->baseurl = $baseurl;

        return $this;
    }

    public function getWebstockUrl(): ?string
    {
        return $this->webstock_url;
    }

    public function setWebstockUrl(?string $webstock_url): self
    {
        $this->webstock_url = $webstock_url;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->api_key;
    }

    public function setApiKey(?string $api_key): self
    {
        $this->api_key = $api_key;

        return $this;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(?string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getCanSendSms(): ?string
    {
        return $this->can_send_sms;
    }

    public function setCanSendSms(?string $can_send_sms): self
    {
        $this->can_send_sms = $can_send_sms;

        return $this;
    }

    public function getTitleSms(): ?string
    {
        return $this->title_sms;
    }

    public function setTitleSms(?string $title_sms): self
    {
        $this->title_sms = $title_sms;

        return $this;
    }

    public function getInstallationStatus(): ?int
    {
        return $this->installation_status;
    }

    public function setInstallationStatus(?int $installation_status): self
    {
        $this->installation_status = $installation_status;

        return $this;
    }

    public function getIsUserValidator(): ?int
    {
        return $this->is_user_validator;
    }

    public function setIsUserValidator(?int $is_user_validator): self
    {
        $this->is_user_validator = $is_user_validator;

        return $this;
    }
}