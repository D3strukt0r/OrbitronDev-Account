<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends EncryptableFieldEntity implements UserInterface, \Serializable
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
     * @ORM\Column(type="string", unique=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":0})
     */
    protected $email_verified = false;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created_on;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $created_ip;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $last_online_at;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $last_ip;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $developer_status = false;

    /**
     * @var null|int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $preferred_payment_method;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="UserPaymentMethods", mappedBy="user", cascade={"persist", "remove"},
     *                                                   orphanRemoval=true)
     */
    protected $paymentMethods;

    /**
     * @var \App\Entity\UserProfiles
     * @ORM\OneToOne(targetEntity="UserProfiles", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $profile;

    /**
     * @var \App\Entity\UserSubscription
     * @ORM\OneToOne(targetEntity="UserSubscription", mappedBy="user", cascade={"persist", "remove"},
     *                                                orphanRemoval=true)
     */
    protected $subscription;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myFriends")
     */
    private $friendsWithMe;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="user_friends",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     * )
     */
    private $myFriends;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $online = false;

    public function __construct()
    {
        $this->paymentMethods = new ArrayCollection();
        $this->friendsWithMe = new ArrayCollection();
        $this->myFriends = new ArrayCollection();

        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     * @throws \Exception
     */
    public function setPassword($password)
    {
        $newPassword = $this->encryptField($password);

        if (is_bool($newPassword) && $newPassword === false) {
            throw new \Exception('[Account] A hashed password could not be generated');
        }

        $this->password = $newPassword;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function verifyPassword($password)
    {
        return $this->verifyEncryptedFieldValue($this->getPassword(), $password);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailVerified()
    {
        return $this->email_verified;
    }

    /**
     * @param bool $emailVerified
     *
     * @return $this
     */
    public function setEmailVerified($emailVerified)
    {
        $this->email_verified = $emailVerified;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * @param \DateTime $createdOn
     *
     * @return $this
     */
    public function setCreatedOn(\DateTime $createdOn)
    {
        $this->created_on = $createdOn;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedIp()
    {
        return $this->created_ip;
    }

    /**
     * @param string $createdIp
     *
     * @return $this
     */
    public function setCreatedIp($createdIp)
    {
        $this->created_ip = $createdIp;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastOnlineAt()
    {
        return $this->last_online_at;
    }

    /**
     * @param \DateTime $lastOnlineAt
     *
     * @return $this
     */
    public function setLastOnlineAt(\DateTime $lastOnlineAt)
    {
        $this->last_online_at = $lastOnlineAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastIp()
    {
        return $this->last_ip;
    }

    /**
     * @param string $lastIp
     *
     * @return $this
     */
    public function setLastIp($lastIp)
    {
        $this->last_ip = $lastIp;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDeveloperStatus()
    {
        return $this->developer_status;
    }

    /**
     * @param bool $developerStatus
     *
     * @return $this
     */
    public function setDeveloperStatus($developerStatus)
    {
        $this->developer_status = $developerStatus;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getPreferredPaymentMethod()
    {
        return $this->preferred_payment_method;
    }

    /**
     * @param null|int $preferredPaymentMethod
     *
     * @return $this
     */
    public function setPreferredPaymentMethod($preferredPaymentMethod)
    {
        $this->preferred_payment_method = $preferredPaymentMethod;

        return $this;
    }

    /**
     * @return \App\Entity\UserPaymentMethods[]
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods->toArray();
    }

    /**
     * @param \App\Entity\UserPaymentMethods $paymentMethod
     *
     * @return $this
     */
    public function addPaymentMethod(UserPaymentMethods $paymentMethod)
    {
        $this->paymentMethods->add($paymentMethod);
        $paymentMethod->setUser($this);

        return $this;
    }

    /**
     * @param \App\Entity\UserPaymentMethods $paymentMethod
     *
     * @return $this
     */
    public function removePaymentMethod(UserPaymentMethods $paymentMethod)
    {
        if ($this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods->removeElement($paymentMethod);
        }

        return $this;
    }

    /**
     * @return \App\Entity\UserProfiles
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param \App\Entity\UserProfiles $profile
     *
     * @return $this
     */
    public function setProfile(UserProfiles $profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return \App\Entity\UserSubscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param \App\Entity\UserSubscription $subscription
     *
     * @return $this
     */
    public function setSubscription(UserSubscription $subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return \App\Entity\User[]
     */
    public function getFriends()
    {
        return $this->myFriends->toArray();
    }

    /**
     * @param \App\Entity\User $user
     */
    public function addFriend(User $user)
    {
        if ($this->myFriends->contains($user)) {
            return;
        }
        $this->myFriends->add($user);
        $user->addFriend($this);
    }

    /**
     * @param \App\Entity\User $user
     */
    public function removeFriend(User $user)
    {
        if (!$this->myFriends->contains($user)) {
            return;
        }
        $this->myFriends->removeElement($user);
        $user->removeFriend($this);
    }

    /**
     * @return bool
     */
    public function isOnline()
    {
        return $this->online;
    }

    /**
     * @param bool $online
     *
     * @return $this
     */
    public function setOnline(bool $online)
    {
        $this->online = $online;

        return $this;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ]);
    }

    /**
     * @see \Serializable::unserialize()
     *
     * @param $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'user_id'                  => $this->id,
            'username'                 => $this->username,
            'password'                 => $this->password,
            'email'                    => $this->email,
            'email_verified'           => $this->email_verified,
            'created_on'               => $this->created_on,
            'created_ip'               => $this->created_ip,
            'last_online_at'           => $this->last_online_at,
            'last_ip'                  => $this->last_ip,
            'developer_status'         => $this->developer_status,
            'preferred_payment_method' => $this->preferred_payment_method,
            'payment_methods'          => $this->paymentMethods->toArray(),
            'profile'                  => $this->profile->toArray(),
            'subscription'             => $this->subscription->toArray(),
            'scope'                    => null,
        ];
    }
}
