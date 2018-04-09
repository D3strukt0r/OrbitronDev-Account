<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use OAuth2\Storage\UserCredentialsInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends EntityRepository implements UserCredentialsInterface, UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function checkUserCredentials($emailOrUsername, $password)
    {
        /** @var \App\Entity\User $user */
        $user = $this->findOneBy(['email' => $emailOrUsername]);
        if (null === $user) {
            /** @var \App\Entity\User $user */
            $user = $this->findOneBy(['username' => $emailOrUsername]);
        }
        if ($user) {
            return $user->verifyPassword($password);
        }

        return false;
    }

    /**
     * @param $email
     *
     * @return array the associated "user_id" and optional "scope" values
     *               This function MUST return FALSE if the requested user does not exist or is
     *               invalid. "scope" is a space-separated list of restricted scopes.
     * @code
     * return array(
     *     "user_id"  => USER_ID,    // REQUIRED user_id to be stored with the authorization code or access token
     *     "scope"    => SCOPE       // OPTIONAL space-separated list of restricted scopes
     * );
     * @endcode
     */
    public function getUserDetails($email)
    {
        $user = $this->findOneBy(['email' => $email]);
        if ($user) {
            $user = $user->toArray();
        }

        return $user;
    }

    /**
     * @param \App\Entity\User $user
     * @param \App\Entity\User $friend
     *
     * @return bool
     */
    public function friendShipExists($user, $friend)
    {
        foreach ($user->getFriends() as $friendItem) {
            if ($friendItem->getId() === $friend->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \App\Entity\User $user
     * @param bool             $onlineOnly
     *
     * @return int
     */
    public function getFriendCount($user, $onlineOnly = false)
    {
        $count = 0;
        foreach ($user->getFriends() as $friend) {
            if ($onlineOnly) {
                if ($friend->isOnline()) {
                    ++$count;
                }
            } else {
                ++$count;
            }
        }

        return $count;
    }
}
