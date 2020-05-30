<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
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
     * @ORM\Column(type="string", unique=true, length=191)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, length=191)
     */
    protected $email;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $email_verified = false;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created_on;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $created_ip;

    /**
     * @var DateTime
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
     * @ORM\Column(type="boolean", options={"default": false})
     */
    protected $developer_status = false;

    /**
     * @var null|int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $preferred_payment_method;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="UserPaymentMethods", mappedBy="user", cascade={"persist", "remove"},
     *                                                   orphanRemoval=true)
     */
    protected $paymentMethods;

    /**
     * @var UserProfiles
     * @ORM\OneToOne(targetEntity="UserProfiles", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $profile;

    /**
     * @var UserSubscription
     * @ORM\OneToOne(targetEntity="UserSubscription", mappedBy="user", cascade={"persist", "remove"},
     *                                                orphanRemoval=true)
     */
    protected $subscription;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myFriends")
     */
    private $friendsWithMe;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="user_friends",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     * )
     */
    private $myFriends;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $online = false;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $roles;

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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     * @throws Exception
     */
    public function setPassword(string $password): self
    {
        $newPassword = $this->encryptField($password);

        if (false === $newPassword) {
            throw new Exception('[Account] A hashed password could not be generated');
        }

        $this->password = $newPassword;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return $this->verifyEncryptedFieldValue($this->getPassword(), $password);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified;
    }

    /**
     * @param bool $emailVerified
     *
     * @return $this
     */
    public function setEmailVerified(bool $emailVerified): self
    {
        $this->email_verified = $emailVerified;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn(): DateTime
    {
        return $this->created_on;
    }

    /**
     * @param DateTime $createdOn
     *
     * @return $this
     */
    public function setCreatedOn(DateTime $createdOn): self
    {
        $this->created_on = $createdOn;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedIp(): string
    {
        return $this->created_ip;
    }

    /**
     * @param string $createdIp
     *
     * @return $this
     */
    public function setCreatedIp($createdIp): self
    {
        $this->created_ip = $createdIp;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastOnlineAt(): DateTime
    {
        return $this->last_online_at;
    }

    /**
     * @param DateTime $lastOnlineAt
     *
     * @return $this
     */
    public function setLastOnlineAt(DateTime $lastOnlineAt): self
    {
        $this->last_online_at = $lastOnlineAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastIp(): string
    {
        return $this->last_ip;
    }

    /**
     * @param string $lastIp
     *
     * @return $this
     */
    public function setLastIp(string $lastIp): self
    {
        $this->last_ip = $lastIp;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDeveloperStatus(): bool
    {
        return $this->developer_status;
    }

    /**
     * @param bool $developerStatus
     *
     * @return $this
     */
    public function setDeveloperStatus(bool $developerStatus): self
    {
        $this->developer_status = $developerStatus;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPreferredPaymentMethod(): ?int
    {
        return $this->preferred_payment_method;
    }

    /**
     * @param int|null $preferredPaymentMethod
     *
     * @return $this
     */
    public function setPreferredPaymentMethod(int $preferredPaymentMethod = null): self
    {
        $this->preferred_payment_method = $preferredPaymentMethod;

        return $this;
    }

    /**
     * @return UserPaymentMethods[]
     */
    public function getPaymentMethods(): array
    {
        return $this->paymentMethods->toArray();
    }

    /**
     * @param UserPaymentMethods $paymentMethod
     *
     * @return $this
     */
    public function addPaymentMethod(UserPaymentMethods $paymentMethod): self
    {
        $this->paymentMethods->add($paymentMethod);
        $paymentMethod->setUser($this);

        return $this;
    }

    /**
     * @param UserPaymentMethods $paymentMethod
     *
     * @return $this
     */
    public function removePaymentMethod(UserPaymentMethods $paymentMethod): self
    {
        if ($this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods->removeElement($paymentMethod);
        }

        return $this;
    }

    /**
     * @return UserProfiles
     */
    public function getProfile(): UserProfiles
    {
        return $this->profile;
    }

    /**
     * @param UserProfiles $profile
     *
     * @return $this
     */
    public function setProfile(UserProfiles $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return UserSubscription
     */
    public function getSubscription(): UserSubscription
    {
        return $this->subscription;
    }

    /**
     * @param UserSubscription $subscription
     *
     * @return $this
     */
    public function setSubscription(UserSubscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return User[]
     */
    public function getFriends(): array
    {
        return $this->myFriends->toArray();
    }

    /**
     * @param User $user
     */
    public function addFriend(self $user): void
    {
        if ($this->myFriends->contains($user)) {
            return;
        }
        $this->myFriends->add($user);
        $user->addFriend($this);
    }

    /**
     * @param User $user
     */
    public function removeFriend(self $user): void
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
    public function isOnline(): bool
    {
        return $this->online;
    }

    /**
     * @param bool $online
     *
     * @return $this
     */
    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    /**
     * @return string
     * @see \Serializable::serialize()
     *
     */
    public function serialize(): string
    {
        return serialize(
            [
                $this->id,
                $this->username,
                $this->password,
                // see section on salt below
                // $this->salt,
            ]
        );
    }

    /**
     * @param $serialized
     *
     * @see \Serializable::unserialize()
     *
     */
    public function unserialize($serialized)
    {
        [
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ] = unserialize($serialized);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'email' => $this->email,
            'email_verified' => $this->email_verified,
            'created_on' => $this->created_on,
            'created_ip' => $this->created_ip,
            'last_online_at' => $this->last_online_at,
            'last_ip' => $this->last_ip,
            'developer_status' => $this->developer_status,
            'preferred_payment_method' => $this->preferred_payment_method,
            'payment_methods' => $this->paymentMethods->toArray(),
            'profile' => $this->profile->toArray(),
            'subscription' => $this->subscription->toArray(),
            'scope' => null,
        ];
    }
}
