<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OAuthAccessTokenRepository")
 * @ORM\Table(name="oauth_access_tokens")
 */
class OAuthAccessToken
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
    protected $token;

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
     * Get token.
     *
     * @return string The token
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set token.
     *
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
     * @return int The user ID
     */
    public function getUserId(): int
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
     * @return string The scope
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * Set scope.
     *
     * @param string $scope The scope
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
     * @param User $user The user
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
            'token' => $this->token,
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
