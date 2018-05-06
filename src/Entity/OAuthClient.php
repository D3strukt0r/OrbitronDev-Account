<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OAuthClientRepository")
 * @ORM\Table(name="oauth_clients")
 */
class OAuthClient extends EncryptableFieldEntity
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=50, unique=true)
     */
    protected $client_identifier;

    /**
     * @var string
     * @ORM\Column(type="string", length=80)
     */
    protected $client_secret;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected $redirect_uri = '';

    /**
     * @var string
     * @ORM\Column(type="text", options={"default": ""})
     */
    protected $scope = '';

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default": -1})
     */
    protected $user_id = -1;

    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->getClientIdentifier();
    }

    /**
     * Get client_identifier.
     *
     * @return string
     */
    public function getClientIdentifier(): string
    {
        return $this->client_identifier;
    }

    /**
     * Set client_identifier.
     *
     * @param string $clientIdentifier
     *
     * @return $this
     */
    public function setClientIdentifier(string $clientIdentifier): self
    {
        $this->client_identifier = $clientIdentifier;

        return $this;
    }

    /**
     * Get client_secret.
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->client_secret;
    }

    /**
     * Set client_secret.
     *
     * @param string $clientSecret
     * @param bool   $encrypt
     *
     * @throws \Exception
     *
     * @return \App\Entity\OAuthClient
     */
    public function setClientSecret(string $clientSecret, bool $encrypt = false): self
    {
        if ($encrypt) {
            $newSecret = $this->encryptField($clientSecret);

            if (false === $newSecret) {
                throw new \Exception('[Account][OAuth2] A hashed secret could not be generated');
            }

            $this->client_secret = $newSecret;
        } else {
            $this->client_secret = $clientSecret;
        }

        return $this;
    }

    /**
     * Verify client's secret.
     *
     * @param string $clientSecret
     * @param bool   $encrypt
     *
     * @return bool
     */
    public function verifyClientSecret(string $clientSecret, bool $encrypt = false): bool
    {
        if ($encrypt) {
            return $this->verifyEncryptedFieldValue($this->getClientSecret(), $clientSecret);
        }

        return $this->getClientSecret() === $clientSecret;
    }

    /**
     * Get redirect_uri.
     *
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirect_uri;
    }

    /**
     * Set redirect_uri.
     *
     * @param string $redirectUri
     *
     * @return $this
     */
    public function setRedirectUri(string $redirectUri): self
    {
        $this->redirect_uri = $redirectUri;

        return $this;
    }

    /**
     * Get scopes.
     *
     * @return array
     */
    public function getScopes(): array
    {
        $scopes = explode(' ', $this->scope);

        return $scopes;
    }

    /**
     * Set scopes.
     *
     * @param array $scopes
     *
     * @return $this
     */
    public function setScopes(array $scopes): self
    {
        $this->scope = implode(' ', $scopes);

        return $this;
    }

    /**
     * @param string $scope
     *
     * @return $this
     */
    public function addScope(string $scope): self
    {
        $scopes = $this->getScopes();
        $scopes[] = $scope;
        $this->setScopes($scopes);

        return $this;
    }

    /**
     * @param string $scope
     *
     * @return $this
     */
    public function removeScope(string $scope): self
    {
        $scopes = $this->getScopes();
        if (in_array($scope, $scopes, true)) {
            $key = array_search($scope, $scopes, true);
            unset($scopes[$key]);
        }

        return $this;
    }

    /**
     * Get users (in charge).
     *
     * @return int
     */
    public function getUsers(): int
    {
        return $this->user_id;
    }

    /**
     * Set users (in charge).
     *
     * @param int $users
     *
     * @return $this
     */
    public function setUsers(int $users): self
    {
        $this->user_id = $users;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'client_id' => $this->client_identifier,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'scope' => $this->scope,
            'user_id' => $this->user_id,
        ];
    }
}
