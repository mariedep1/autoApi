<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)

 * @UniqueEntity(fields={"username"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("user:details")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=14)
     * @Groups("user:details")
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:details")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:details")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:details")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups("user:details")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Garage::class, mappedBy="user", orphanRemoval=true)
     * @Groups("user:details")
     */
    private $garages;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups("user:details")
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity=Ad::class, mappedBy="user", orphanRemoval=true)
     */
    private $ads;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups("user:details")
     */
    private $roles = [];

    public function __construct()
    {
        $this->garages = new ArrayCollection();
        $this->ads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiret(): ?int
    {
        return $this->siret;
    }

    public function setSiret(int $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(int $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Garage[]
     */
    public function getGarages(): Collection
    {
        return $this->garages;
    }

    public function addGarage(Garage $garage): self
    {
        if (!$this->garages->contains($garage)) {
            $this->garages[] = $garage;
            $garage->setUser($this);
        }

        return $this;
    }

    public function removeGarage(Garage $garage): self
    {
        if ($this->garages->removeElement($garage)) {
            // set the owning side to null (unless already changed)
            if ($garage->getUser() === $this) {
                $garage->setUser(null);
            }
        }

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = "ROLE_USER";
        return array_unique($roles);
    }
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setUser($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): self
    {
        if ($this->ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getUser() === $this) {
                $ad->setUser(null);
            }
        }

        return $this;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
