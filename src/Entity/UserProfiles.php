<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_profiles")
 */
class UserProfiles
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", inversedBy="profile")
     * @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $surname;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $gender;

    /**
     * @var DateTime|null
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthday;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $website;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $picture;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $active_address;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="UserAddress", mappedBy="userProfile", cascade={"persist", "remove"},
     *                                            orphanRemoval=true)
     */
    protected $addresses;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    /**
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User The user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user The user
     *
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null The first name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name The first name
     *
     * @return $this
     */
    public function setName(string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null The last name
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname The last name
     *
     * @return $this
     */
    public function setSurname(string $surname = null): self
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return int|null The gender
     */
    public function getGender(): ?int
    {
        return $this->gender;
    }

    /**
     * @param int|null $gender The gender
     *
     * @return $this
     */
    public function setGender(int $gender = null): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return DateTime|null The birth date
     */
    public function getBirthday(): ?DateTime
    {
        return $this->birthday;
    }

    /**
     * @param DateTime|null $birthday The birth date
     *
     * @return $this
     */
    public function setBirthday(DateTime $birthday = null): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return string|null The website
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string|null $website The website
     *
     * @return $this
     */
    public function setWebsite(string $website = null): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return string|null The profile picture URI
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * @param string|null $picture The profile picture URI
     *
     * @return $this
     */
    public function setPicture(string $picture = null): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return int|null The active address
     */
    public function getActiveAddress(): ?int
    {
        return $this->active_address;
    }

    /**
     * @param int|null $activeAddress The active address
     *
     * @return $this
     */
    public function setActiveAddress(int $activeAddress = null): self
    {
        $this->active_address = $activeAddress;

        return $this;
    }

    /**
     * @return UserAddress[] All the addresses
     */
    public function getAddresses(): array
    {
        return $this->addresses->toArray();
    }

    /**
     * @param UserAddress $address The address
     *
     * @return $this
     */
    public function addAddress(UserAddress $address): self
    {
        $this->addresses->add($address);
        $address->setUserProfile($this);

        return $this;
    }

    /**
     * @param UserAddress $address The address
     *
     * @return $this
     */
    public function removeAddress(UserAddress $address): self
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
        }

        return $this;
    }

    /**
     * @return array All the user info in one array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'website' => $this->website,
            'picture' => $this->picture,
            'active_address' => $this->active_address,
            'addresses' => $this->addresses->toArray(),
        ];
    }
}
