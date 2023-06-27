<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TypeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 */
class Type
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read","user:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read","user:write"})
     */
    private $designation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read","user:write"})
     */
    private $sys_name;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="type")
     */
    private $users;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $num;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="type")
     */
    private $user;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->user = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getSysName(): ?string
    {
        return $this->sys_name;
    }

    public function setSysName(string $sys_name): self
    {
        $this->sys_name = $sys_name;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setType($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getType() === $this) {
                $user->setType(null);
            }
        }

        return $this;
    }

    public function getNum(): ?int
    {
        return $this->num;
    }

    public function setNum(int $num): self
    {
        $this->num = $num;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }
}
