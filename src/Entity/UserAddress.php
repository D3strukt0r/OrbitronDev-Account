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
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $street;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $house_number;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $zip_code;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $city;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $country;

    /**
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return UserProfiles The user profile
     */
    public function getUserProfile(): UserProfiles
    {
        return $this->userProfile;
    }

    /**
     * @param UserProfiles $userProfile The user profile
     *
     * @return $this
     */
    public function setUserProfile(UserProfiles $userProfile): self
    {
        $this->userProfile = $userProfile;

        return $this;
    }

    /**
     * @return string|null The street
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string|null $street The street
     *
     * @return $this
     */
    public function setStreet(string $street = null): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return string|null The house number
     */
    public function getHouseNumber(): ?string
    {
        return $this->house_number;
    }

    /**
     * @param string|null $houseNumber The house number
     *
     * @return $this
     */
    public function setHouseNumber(string $houseNumber = null): self
    {
        $this->house_number = $houseNumber;

        return $this;
    }

    /**
     * @return string|null The zip code
     */
    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    /**
     * @param string|null $zipCode The zip code
     *
     * @return $this
     */
    public function setZipCode(string $zipCode = null): self
    {
        $this->zip_code = $zipCode;

        return $this;
    }

    /**
     * @return string|null The city
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city The city
     *
     * @return $this
     */
    public function setCity(string $city = null): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null The country
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country The country
     *
     * @return $this
     */
    public function setCountry(string $country = null): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return array An array of all the attributes in the object
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
