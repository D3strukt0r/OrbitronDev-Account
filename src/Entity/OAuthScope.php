<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OAuthScope
 * @ORM\Entity
 * @ORM\Table(name="oauth_scopes")
 */
class OAuthScope
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
     * @ORM\Column(type="string", length=80, unique=true)
     */
    protected $scope;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $is_default = false;

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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get is_default
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * Set is_default
     *
     * @param bool $is_default
     *
     * @return $this
     */
    public function setDefault($is_default)
    {
        $this->is_default = $is_default;

        return $this;
    }

    public function toArray()
    {
        return [
            'id'         => $this->id,
            'scope'      => $this->scope,
            'name'       => $this->name,
            'is_default' => $this->is_default,
        ];
    }

    public static function fromArray($params)
    {
        $token = new self();
        foreach ($params as $property => $value) {
            $token->$property = $value;
        }

        return $token;
    }
}
