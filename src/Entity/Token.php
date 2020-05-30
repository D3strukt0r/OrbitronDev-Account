<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tokens")
 */
class Token
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, length=191)
     */
    protected $token;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $job;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $expires;

    /**
     * @var array|null
     * @ORM\Column(type="array", nullable=true)
     */
    protected $optional_info;

    /**
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string The token
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token The token
     *
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string The job to do
     */
    public function getJob(): string
    {
        return $this->job;
    }

    /**
     * @param string $job The job to do
     *
     * @return $this
     */
    public function setJob(string $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return DateTime|null The expiration date
     */
    public function getExpires(): ?DateTime
    {
        return $this->expires;
    }

    /**
     * @param DateTime|null $expires The expiration date
     *
     * @return $this
     */
    public function setExpires(DateTime $expires = null): self
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * @return array|null Additional optional info
     */
    public function getOptionalInfo(): ?array
    {
        return $this->optional_info;
    }

    /**
     * @param array|null $optional_info Additional optional info
     *
     * @return $this
     */
    public function setOptionalInfo(array $optional_info = null): self
    {
        $this->optional_info = $optional_info;

        return $this;
    }
}
