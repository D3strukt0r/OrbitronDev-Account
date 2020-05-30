<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OAuthAuthorizationCodeRepository")
 * @ORM\Table(name="oauth_authorization_codes")
 */
class OAuthAuthorizationCode
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
     * @ORM\Column(type="string", length=40, unique=true)
     */
    protected $code;

    /**
     * @var OAuthClient
     * @ORM\ManyToOne(targetEntity="OAuthClient")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="client_identifier", nullable=false, onDelete="CASCADE")
     */
    protected $client;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $expires;

    /**
     * @var string
     * @ORM\Column(type="string", length=200)
     */
    protected $redirect_uri;

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
     * Get code.
     *
     * @return string The code
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set code.
     *
     * @param string $code The code
     *
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

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
     * Get redirect_uri.
     *
     * @return string The redirect URI
     */
    public function getRedirectUri(): string
    {
        return $this->redirect_uri;
    }

    /**
     * Set redirect_uri.
     *
     * @param string $redirectUri The redirect URI
     *
     * @return $this
     */
    public function setRedirectUri(string $redirectUri): self
    {
        $this->redirect_uri = $redirectUri;

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
     * @param OAuthClient|null $client The OAuth client
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
            'code' => $this->code,
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
        $code = new self();
        foreach ($params as $property => $value) {
            $code->{$property} = $value;
        }

        return $code;
    }
}
