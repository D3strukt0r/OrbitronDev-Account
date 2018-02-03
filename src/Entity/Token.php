<?php

namespace App\Entity;

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
     * @ORM\Column(type="string", unique=true)
     */
    protected $token;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $job;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $expires;

    /**
     * @var null|array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $optional_info;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getJob(): string
    {
        return $this->job;
    }

    /**
     * @param string $job
     *
     * @return $this
     */
    public function setJob(string $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpires(): ?\DateTime
    {
        return $this->expires;
    }

    /**
     * @param \DateTime|null $expires
     *
     * @return $this
     */
    public function setExpires(\DateTime $expires = null): self
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getOptionalInfo(): ?array
    {
        return $this->optional_info;
    }

    /**
     * @param array|null $optional_info
     *
     * @return $this
     */
    public function setOptionalInfo(array $optional_info = null): self
    {
        $this->optional_info = $optional_info;

        return $this;
    }
}
