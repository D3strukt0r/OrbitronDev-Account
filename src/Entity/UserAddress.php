<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_addresses")
 */
class UserAddress
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var UserProfiles
     * @ORM\ManyToOne(targetEntity="UserProfiles", inversedBy="addresses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $userProfile;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $street;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $house_number;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $zip_code;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $city;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $country;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return UserProfiles
     */
    public function getUserProfile(): UserProfiles
    {
        return $this->userProfile;
    }

    /**
     * @param UserProfiles $userProfile
     *
     * @return $this
     */
    public function setUserProfile(UserProfiles $userProfile): self
    {
        $this->userProfile = $userProfile;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param null|string $street
     *
     * @return $this
     */
    public function setStreet(string $street = null): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHouseNumber(): ?string
    {
        return $this->house_number;
    }

    /**
     * @param null|string $houseNumber
     *
     * @return $this
     */
    public function setHouseNumber(string $houseNumber = null): self
    {
        $this->house_number = $houseNumber;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    /**
     * @param null|string $zipCode
     *
     * @return $this
     */
    public function setZipCode(string $zipCode = null): self
    {
        $this->zip_code = $zipCode;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param null|string $city
     *
     * @return $this
     */
    public function setCity(string $city = null): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param null|string $country
     *
     * @return $this
     */
    public function setCountry(string $country = null): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'street' => $this->street,
            'house_number' => $this->house_number,
            'zip_code' => $this->zip_code,
            'city' => $this->city,
            'country' => $this->country,
        ];
    }
}
