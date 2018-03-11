<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class AccountHelper
{
    public static $settings = [
        'username'     => [
            'min_length'    => 3,
            'max_length'    => 50,
            'blocked'       => ['admin', 'administrator', 'mod', 'moderator', 'guest', 'undefined'],
            'blocked_parts' => ['mod', 'system', 'admin'],
            'pattern'       => '/^[a-zA-Z0-9_]+$/i', // Accepted: a-z, A-Z, 0-9 and _
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
     * @var \Doctrine\Common\Persistence\ObjectManager $em
     */
    private $em;

    /**
     * @var \Symfony\Component\HttpFoundation\Request $request
     */
    private $request;

    public function __construct(ObjectManager $manager, RequestStack $request)
    {
        $this->em = $manager;
        $this->request = $request->getCurrentRequest();
    }

    /**
     * Checks whether the username or email exists in the database. Returns
     * true when the username or email exist once in the database.
     *
     * @param string $usernameOrEmail
     *
     * @return \App\Entity\User|bool
     */
    public function userExists($usernameOrEmail)
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $usernameOrEmail]);
        if (is_null($user)) {
            /** @var User $user */
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $usernameOrEmail]);
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
     * @param string $username
     *
     * @return bool
     */
    public function usernameExists($username)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);

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
    public function usernameBlocked($username)
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
    public function usernameValid($username)
    {
        return preg_match(self::$settings['username']['pattern'], $username);
    }

    /**
     * Checks whether the email is already existing in the database. Returns
     * true when the email is already existing once in the database.
     *
     * @param string $email
     *
     * @return bool
     */
    public function emailExists($email)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

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
    public function emailValid($email)
    {
        return preg_match(self::$settings['email']['pattern'], $email);
    }
}
