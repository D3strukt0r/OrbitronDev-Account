<?php

namespace App\Entity;

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
     * @var \App\Entity\OAuthClient
     * @ORM\ManyToOne(targetEntity="OAuthClient")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="client_identifier", onDelete="CASCADE")
     */
    protected $client;

    /**
     * @var \App\Entity\User
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
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $expires;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $scope;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get client_id
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->client->getId();
    }

    /**
     * Get user_identifier
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user->getId();
    }

    /**
     * Get expires
     *
     * @return \DateTime
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Set expires
     *
     * @param \DateTime $expires
     *
     * @return $this
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set scope
     *
     * @param string $scope
     *
     * @return $this
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get client
     *
     * @return \App\Entity\OAuthClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set client
     *
     * @param \App\Entity\OAuthClient $client
     *
     * @return $this
     */
    public function setClient(OAuthClient $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param \App\Entity\User $user
     *
     * @return $this
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'token'     => $this->token,
            'client_id' => $this->client->getId(),
            'user_id'   => $this->user->getId(),
            'expires'   => $this->expires,
            'scope'     => $this->scope,
        ];
    }

    /**
     * @param $params
     *
     * @return \App\Entity\OAuthAccessToken
     */
    public static function fromArray($params)
    {
        $token = new self();
        foreach ($params as $property => $value) {
            $token->$property = $value;
        }

        return $token;
    }
}
