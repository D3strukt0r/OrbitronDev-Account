<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_payment_methods")
 */
class UserPaymentMethods
{
    public const PAYMENT_VISA = 'visa';
    public const PAYMENT_MASTERCARD = 'mastercard';
    public const PAYMENT_MAESTRO = 'maestro';
    public const PAYMENT_PAYPAL = 'paypal';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="paymentMethods")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $payment_type;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $data;

    /**
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User The user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user The user
     *
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string The type
     */
    public function getType(): string
    {
        return $this->payment_type;
    }

    /**
     * @param string $type The type
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->payment_type = $type;

        return $this;
    }

    /**
     * @return array The data
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data The data
     *
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array An array of all the attributes in the object
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'payment_type' => $this->payment_type,
            'data' => $this->data,
        ];
    }
}
