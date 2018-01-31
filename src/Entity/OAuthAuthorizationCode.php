<?php

namespace App\Entity;

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
     * @var \App\Entity\OAuthClient
     * @ORM\ManyToOne(targetEntity="OAuthClient")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="client_identifier", nullable=false, onDelete="CASCADE")
     */
    protected $client;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var \DateTime
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

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
            'code'      => $this->code,
            'client_id' => $this->client->getId(),
            'user_id'   => $this->user->getId(),
            'expires'   => $this->expires,
            'scope'     => $this->scope,
        ];
    }

    /**
     * @param array $params
     *
     * @return self
     */
    public static function fromArray($params)
    {
        $code = new self();
        foreach ($params as $property => $value) {
            $code->$property = $value;
        }

        return $code;
    }
}
