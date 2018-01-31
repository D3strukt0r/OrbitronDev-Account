<?php

namespace App\Helper;

use App\Entity\OAuthClient;
use App\Entity\OAuthScope;
use App\Entity\SubscriptionType;
use App\Entity\User;
use App\Entity\UserProfiles;
use App\Entity\UserSubscription;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class AccountHelper
{
    public static $settings = [
        'username'     => [
            'min_length'    => 3,
            'max_length'    => 50,
            'blocked'       => ['admin', 'administrator', 'mod', 'moderator', 'guest', 'undefined'],
            'blocked_parts' => ['mod', 'system', 'admin'],
            'pattern'       => '/^[a-z0-9_]+$/i', // Accepted: a-z, A-Z, 1-9 and _
        ],
        'password'     => [
            'min_length' => 7,
            'max_length' => 100,
            'salt'       => 'random',
        ],
        'email'        => [
            'pattern' => '/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i',
        ],
        'subscription' => [
            'default' => 1,
        ],
        'login'        => [
            'session_email'    => 'USER_EM',
            'session_password' => 'USER_PW',
            'cookie_name'      => 'account',
            'cookie_expire'    => '+1 month',
            'cookie_path'      => '/',
            'cookie_domain'    => 'orbitrondev.org',
        ],
    ];

    /**
     * Add a new user. Username, Email, and password is required twice. Returns
     * the user id.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
     * @param \Symfony\Component\HttpFoundation\Request  $request
     * @param string                                     $username
     * @param string                                     $password
     * @param string                                     $passwordVerify
     * @param string                                     $email
     *
     * @return int|string
     */
    public static function addUser(ObjectManager $entityManager, Request $request, $username, $password, $passwordVerify, $email)
    {
        // Check username
        if (strlen($username) == 0) {
            return 'username:insert_username';
        } elseif (strlen($username) < self::$settings['username']['min_length']) {
            return 'username:username_short';
        } elseif (strlen($username) > self::$settings['username']['max_length']) {
            return 'username:username_long';
        } elseif (self::usernameExists($entityManager, $username)) {
            return 'username:user_exists';
        } elseif (self::usernameBlocked($username)) {
            return 'username:blocked_name';
        } elseif (!self::usernameValid($username)) {
            return 'username:not_valid_name';
        } // Check E-Mail
        elseif (strlen($email) == 0) {
            return 'email:insert_email';
        } elseif (!self::emailValid($email)) {
            return 'email:email_not_valid';
        } // Check password
        elseif (strlen($password) == 0) {
            return 'password:insert_password';
        } elseif (strlen($password) < self::$settings['password']['min_length']) {
            return 'password:password_too_short';
        } elseif ($password != $passwordVerify) {
            return 'password_verify:passwords_do_not_match';
        }

        // Add user to database
        $user = new User();
        $user
            ->setUsername($username)
            ->setPassword($password)
            ->setEmail($email)
            ->setEmailVerified(false)
            ->setCreatedOn(new \DateTime())
            ->setLastOnlineAt(new \DateTime())
            ->setCreatedIp($request->getClientIp())
            ->setLastIp($request->getClientIp())
            ->setDeveloperStatus(false);

        $userProfile = new UserProfiles();
        $userProfile->setUser($user);
        $user->setProfile($userProfile);

        /** @var SubscriptionType $defaultSubscription */
        $defaultSubscription = $entityManager->find(SubscriptionType::class, self::$settings['subscription']['default']);

        $userSubscription = new UserSubscription();
        $userSubscription
            ->setUser($user)
            ->setSubscription($defaultSubscription)
            ->setActivatedAt(new \DateTime())
            ->setExpiresAt(new \DateTime());
        $user->setSubscription($userSubscription);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \App\Entity\User                           $user
     */
    public static function removeUser(ObjectManager $em, User $user)
    {
        $em->remove($user);
        $em->flush();
    }

    /**
     * Checks whether the username or email exists in the database. Returns
     * true when the username or email exist once in the database.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param string                                     $usernameOrEmail
     *
     * @return \App\Entity\User|bool
     */
    public static function userExists(ObjectManager $em, $usernameOrEmail)
    {
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(array('username' => $usernameOrEmail));
        if (is_null($user)) {
            /** @var User $user */
            $user = $em->getRepository(User::class)->findOneBy(array('email' => $usernameOrEmail));
            if (is_null($user)) {
                return false;
            }
        }
        return $user;
    }

    /**
     * Checks whether the username is already existing in the database. Returns
     * true when the username is already existing once in the database.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param string                                     $username
     *
     * @return bool
     */
    public static function usernameExists(ObjectManager $em, $username)
    {
        $user = $em->getRepository(User::class)->findOneBy(array('username' => $username));

        if (is_null($user)) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the username is blocked or has any blocked parts in it.
     * Returns true when the name is blocked or has a blocked part. Returns
     * false if ok.
     *
     * @param string $username
     *
     * @return bool
     */
    public static function usernameBlocked($username)
    {
        foreach (self::$settings['username']['blocked'] as $bl) {
            if (strtolower($username) == strtolower($bl)) {
                return true;
            }
        }

        foreach (self::$settings['username']['blocked_parts'] as $bl) {
            if (strpos(strtolower($username), strtolower($bl)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether the username corresponds the desired pattern. Returns
     * true when the string matches the pattern.
     *
     * @param string $username
     *
     * @return int
     */
    public static function usernameValid($username)
    {
        return preg_match(self::$settings['username']['pattern'], $username);
    }

    /**
     * Checks whether the email is already existing in the database. Returns
     * true when the email is already existing once in the database.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param string                                     $email
     *
     * @return bool
     */
    public static function emailExists(ObjectManager $em, $email)
    {
        $user = $em->getRepository(User::class)->findOneBy(array('email' => $email));

        if (is_null($user)) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the email corresponds the desired pattern. Returns
     * true when the string matches the pattern.
     *
     * @param string $email
     *
     * @return int
     */
    public static function emailValid($email)
    {
        return preg_match(self::$settings['email']['pattern'], $email);
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param string                                     $clientName
     * @param string                                     $clientSecret
     * @param string                                     $redirectUri
     * @param array                                      $scopes
     * @param int                                        $userId
     *
     * @return string
     */
    public static function addApp(ObjectManager $em, $clientName, $clientSecret, $redirectUri, $scopes, $userId)
    {
        /** @var \App\Entity\User $user */
        $user = $em->find(User::class, $userId);
        $addClient = new OAuthClient();
        $addClient
            ->setClientIdentifier($clientName)
            ->setClientSecret($clientSecret)
            ->setRedirectUri($redirectUri)
            ->setScopes($scopes)
            ->setUsers($user->getId());

        $em->persist($addClient);
        $em->flush();

        return $addClient->getId();
    }

    public static function addDefaultSubscriptionTypes(ObjectManager $em)
    {
        $basicSubscription = new SubscriptionType();
        $basicSubscription
            ->setTitle('Basic')
            ->setPrice('0')
            ->setPermissions(array());

        $premiumSubscription = new SubscriptionType();
        $premiumSubscription
            ->setTitle('Premium')
            ->setPrice('10')
            ->setPermissions(array('web_service', 'support'));

        $enterpriseSubscription = new SubscriptionType();
        $enterpriseSubscription
            ->setTitle('Enterprise')
            ->setPrice('30')
            ->setPermissions(array('web_service', 'web_service_multiple', 'support'));

        $em->persist($basicSubscription);
        $em->persist($premiumSubscription);
        $em->persist($enterpriseSubscription);
        $em->flush();
    }

    public static function addDefaultScopes(ObjectManager $em)
    {
        $scope1 = new OAuthScope();
        $scope1
            ->setScope('user_info')
            ->setName('User info\'s')
            ->setDefault(true);

        $em->persist($scope1);
        $em->flush();
    }
}
