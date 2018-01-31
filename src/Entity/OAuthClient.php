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
     * @ORM\Column(type="string", length=255, options={"default":""})
     */
    protected $redirect_uri = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default":""})
     */
    protected $scope = '';

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":-1})
     */
    protected $user_id = -1;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->getClientIdentifier();
    }

    /**
     * Get client_identifier
     *
     * @return string
     */
    public function getClientIdentifier()
    {
        return $this->client_identifier;
    }

    /**
     * Set client_identifier
     *
     * @param string $clientIdentifier
     *
     * @return $this
     */
    public function setClientIdentifier($clientIdentifier)
    {
        $this->client_identifier = $clientIdentifier;

        return $this;
    }

    /**
     * Get client_secret
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * Set client_secret
     *
     * @param string $clientSecret
     * @param bool   $encrypt
     *
     * @return \App\Entity\OAuthClient
     * @throws \Exception
     */
    public function setClientSecret($clientSecret, $encrypt = false)
    {
        if ($encrypt) {
            $newSecret = $this->encryptField($clientSecret);

            if (is_bool($newSecret) && $newSecret === false) {
                throw new \Exception('[Account][OAuth2] A hashed secret could not be generated');
            }

            $this->client_secret = $newSecret;
        } else {
            $this->client_secret = $clientSecret;
        }

        return $this;
    }

    /**
     * Verify client's secret
     *
     * @param string $clientSecret
     * @param bool   $encrypt
     *
     * @return bool
     */
    public function verifyClientSecret($clientSecret, $encrypt = false)
    {
        if ($encrypt) {
            return $this->verifyEncryptedFieldValue($this->getClientSecret(), $clientSecret);
        } else {
            return $this->getClientSecret() == $clientSecret;
        }
    }

    /**
     * Get redirect_uri
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    /**
     * Set redirect_uri
     *
     * @param string $redirectUri
     *
     * @return $this
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirect_uri = $redirectUri;

        return $this;
    }

    /**
     * Get scopes
     *
     * @return array
     */
    public function getScopes()
    {
        $scopes = explode(' ', $this->scope);

        return $scopes;
    }

    /**
     * Set scopes
     *
     * @param array $scopes
     *
     * @return $this
     */
    public function setScopes($scopes)
    {
        $this->scope = implode(' ', $scopes);

        return $this;
    }

    /**
     * @param string $scope
     *
     * @return $this
     */
    public function addScope($scope)
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
    public function removeScope($scope)
    {
        $scopes = $this->getScopes();
        if (in_array($scope, $scopes)) {
            $key = array_search($scope, $scopes);
            unset($scopes[$key]);
        }

        return $this;
    }

    /**
     * Get users (in charge)
     *
     * @return int
     */
    public function getUsers()
    {
        return $this->user_id;
    }

    /**
     * Set users (in charge)
     *
     * @param int $users
     *
     * @return $this
     */
    public function setUsers($users)
    {
        $this->user_id = $users;

        return $this;
    }

    public function toArray()
    {
        return [
            'client_id'     => $this->client_identifier,
            'client_secret' => $this->client_secret,
            'redirect_uri'  => $this->redirect_uri,
            'scope'         => $this->scope,
            'user_id'       => $this->user_id,
        ];
    }
}
