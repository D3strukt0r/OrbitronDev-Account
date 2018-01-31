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
     * @var \App\Entity\UserProfiles
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \App\Entity\UserProfiles
     */
    public function getUserProfile()
    {
        return $this->userProfile;
    }

    /**
     * @param \App\Entity\UserProfiles $userProfile
     *
     * @return $this
     */
    public function setUserProfile(UserProfiles $userProfile)
    {
        $this->userProfile = $userProfile;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param null|string $street
     *
     * @return $this
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHouseNumber()
    {
        return $this->house_number;
    }

    /**
     * @param null|string $houseNumber
     *
     * @return $this
     */
    public function setHouseNumber($houseNumber)
    {
        $this->house_number = $houseNumber;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }

    /**
     * @param null|string $zipCode
     *
     * @return $this
     */
    public function setZipCode($zipCode)
    {
        $this->zip_code = $zipCode;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param null|string $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param null|string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id'           => $this->id,
            'street'       => $this->street,
            'house_number' => $this->house_number,
            'zip_code'     => $this->zip_code,
            'city'         => $this->city,
            'country'      => $this->country,
        ];
    }
}
