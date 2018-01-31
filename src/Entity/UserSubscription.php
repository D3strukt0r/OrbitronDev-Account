<?php

namespace App\Entity;

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
     * @var \App\Entity\User
     * @ORM\OneToOne(targetEntity="User", inversedBy="subscription")
     * @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var \App\Entity\SubscriptionType
     * @ORM\ManyToOne(targetEntity="SubscriptionType")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id", nullable=false)
     */
    protected $subscription;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $activated_at;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $expires_at;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \App\Entity\User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \App\Entity\SubscriptionType
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param \App\Entity\SubscriptionType $subscription
     *
     * @return $this
     */
    public function setSubscription(SubscriptionType $subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getActivatedAt()
    {
        return $this->activated_at;
    }

    /**
     * @param \DateTime $activatedAt
     *
     * @return $this
     */
    public function setActivatedAt(\DateTime $activatedAt)
    {
        $this->activated_at = $activatedAt;

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * @param null|\DateTime $expiresAt
     *
     * @return $this
     */
    public function setExpiresAt(\DateTime $expiresAt)
    {
        $this->expires_at = $expiresAt;

        return $this;
    }

    /**
     * @return float|int|null
     */
    public function getRemainingDays()
    {
        if (is_null($this->getExpiresAt())) {
            return null;
        }

        $difference = $this->getExpiresAt()->getTimestamp() - time();
        if ($difference <= 0) {
            return 0;
        }

        return ceil($difference / 86400);
    }

    /**
     * @return bool
     */
    public function hasSubscription()
    {
        if (is_null($days = $this->getRemainingDays()) || $days > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'user_id'      => $this->id,
            'subscription' => $this->subscription->toArray(),
            'activated_at' => $this->activated_at,
            'expires_at'   => $this->expires_at,
        ];
    }
}
