<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OAuthRefreshTokenRepository")
 * @ORM\Table(name="oauth_refresh_tokens")
 */
class OAuthRefreshToken
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var OAuthClient
     * @ORM\ManyToOne(targetEntity="OAuthClient")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="client_identifier", onDelete="CASCADE")
     */
    protected $client;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=40, unique=true)
     */
    protected $refresh_token;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $expires;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $scope;

    /**
     * Get id.
     *
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get refresh_token.
     *
     * @return string The refresh token
     */
    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    /**
     * Set refresh_token.
     *
     * @param string $refresh_token The refresh token
     *
     * @return $this
     */
    public function setRefreshToken(string $refresh_token): self
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    /**
     * Get client_id.
     *
     * @return string The client ID
     */
    public function getClientId(): string
    {
        return $this->client->getId();
    }

    /**
     * Get user_identifier.
     *
     * @return string The user ID
     */
    public function getUserId(): string
    {
        return $this->user->getId();
    }

    /**
     * Get expires.
     *
     * @return DateTime The expiration date
     */
    public function getExpires(): DateTime
    {
        return $this->expires;
    }

    /**
     * Set expires.
     *
     * @param DateTime $expires The expiration date
     *
     * @return $this
     */
    public function setExpires(DateTime $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get scope.
     *
     * @return string The scopes
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * Set scope.
     *
     * @param string $scope The scopes
     *
     * @return $this
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get client.
     *
     * @return OAuthClient The OAuth client
     */
    public function getClient(): OAuthClient
    {
        return $this->client;
    }

    /**
     * Set client.
     *
     * @param OAuthClient $client The OAuth client
     *
     * @return $this
     */
    public function setClient(OAuthClient $client = null): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User The user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set user.
     *
     * @param User|null $user The user
     *
     * @return $this
     */
    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return array An array of all the attributes in the object
     */
    public function toArray(): array
    {
        return [
            'refresh_token' => $this->refresh_token,
            'client_id' => $this->client->getId(),
            'user_id' => $this->user->getId(),
            'expires' => $this->expires,
            'scope' => $this->scope,
        ];
    }

    /**
     * @param array $params All the attributes
     *
     * @return self An object initialized from the array's information
     */
    public static function fromArray(array $params): self
    {
        $token = new self();
        foreach ($params as $property => $value) {
            $token->{$property} = $value;
        }

        return $token;
    }
}
