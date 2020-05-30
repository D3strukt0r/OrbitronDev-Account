<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_subscriptions")
 */
class UserSubscription
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", inversedBy="subscription")
     * @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var SubscriptionType
     * @ORM\ManyToOne(targetEntity="SubscriptionType")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id", nullable=false)
     */
    protected $subscription;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $activated_at;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $expires_at;

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
     * @return SubscriptionType The subscription type
     */
    public function getSubscription(): SubscriptionType
    {
        return $this->subscription;
    }

    /**
     * @param SubscriptionType $subscription The subscription type
     *
     * @return $this
     */
    public function setSubscription(SubscriptionType $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return DateTime When the subscription was activated
     */
    public function getActivatedAt(): DateTime
    {
        return $this->activated_at;
    }

    /**
     * @param DateTime $activatedAt When the subscription was activated
     *
     * @return $this
     */
    public function setActivatedAt(DateTime $activatedAt): self
    {
        $this->activated_at = $activatedAt;

        return $this;
    }

    /**
     * @return DateTime|null When the subscription expires
     */
    public function getExpiresAt(): ?DateTime
    {
        return $this->expires_at;
    }

    /**
     * @param DateTime|null $expiresAt When the subscription expires
     *
     * @return $this
     */
    public function setExpiresAt(DateTime $expiresAt = null): self
    {
        $this->expires_at = $expiresAt;

        return $this;
    }

    /**
     * @return int|null How many days are left in his subscription, or null if not subscribed
     */
    public function getRemainingDays(): ?int
    {
        if (null === $this->getExpiresAt()) {
            return null;
        }

        $difference = $this->getExpiresAt()->getTimestamp() - time();
        if ($difference <= 0) {
            return 0;
        }

        return (int) ceil($difference / 86400);
    }

    /**
     * @return bool Whether the user has a running subscription
     */
    public function hasSubscription(): bool
    {
        if (null === ($days = $this->getRemainingDays()) || $days > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return array All the user info in one array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->id,
            'subscription' => $this->subscription->toArray(),
            'activated_at' => $this->activated_at,
            'expires_at' => $this->expires_at,
        ];
    }
}
