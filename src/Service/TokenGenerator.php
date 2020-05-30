<?php

namespace App\Service;

use App\Entity\Token;
use DateTime;
use Doctrine\Persistence\ObjectManager;

class TokenGenerator
{
    /**
     * @var bool
     */
    private $isGenerated = false;

    /**
     * @var Token|null
     */
    private $token;

    /**
     * @var ObjectManager|null
     */
    private $em;

    /**
     * @var bool
     */
    private $useOpenSsl;

    /**
     * TokenGenerator constructor.
     *
     * @param ObjectManager $entityManager The entity manager
     * @param string|null   $token         The token
     */
    public function __construct(ObjectManager $entityManager, $token = null)
    {
        $this->em = $entityManager;

        if (null !== $token && is_string($token)) {
            /** @var Token $token */
            $token = $this->em->getRepository(Token::class)->findOneBy(['token' => $token]);

            if (null !== $token) {
                $this->isGenerated = true;
                $this->token = $token;
            }
        }

        // determine whether to use OpenSSL
        if (defined('PHP_WINDOWS_VERSION_BUILD') && version_compare(PHP_VERSION, '5.3.4', '<')) {
            $this->useOpenSsl = false;
        } elseif (!function_exists('openssl_random_pseudo_bytes')) {
            $this->useOpenSsl = false;
        } else {
            $this->useOpenSsl = true;
        }
    }

    /**
     * @param string        $job         The job
     * @param DateTime|null $validUntil  Date until valid
     * @param array|null    $information Additional information
     *
     * @return string
     */
    public function generateToken($job, $validUntil = null, array $information = null)
    {
        if (!$this->isGenerated) {
            $token = rtrim(strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '=');

            $newToken = new Token();
            $newToken
                ->setToken($token)
                ->setJob($job)
                ->setExpires($validUntil)
                ->setOptionalInfo($information)
            ;

            $this->em->persist($newToken);
            $this->em->flush();

            $this->token = $newToken;

            return $token;
        }

        return null;
    }

    /**
     * @param array $options
     *
     * @return string
     */
    public static function createRandomToken($options = [])
    {
        $defaultOptions = [
            'use_openssl' => false,
        ];
        $options = array_merge($defaultOptions, $options);

        if ($options['use_openssl']) {
            $nbBytes = 32;
            $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);
            if (false !== $bytes && true === $strong) {
                return $bytes;
            }
            throw new \UnexpectedValueException('OpenSSL did not produce a secure random number.');
        }

        return hash('sha256', uniqid(mt_rand(), true));
    }

    /**
     * @return bool|string|null
     */
    public function getJob()
    {
        if ($this->isGenerated) {
            if (null === $this->token) {
                return false;
            }
            if ($this->token->getExpires()->getTimestamp() < time()) {
                return 'expired';
            }

            return $this->token->getJob();
        }

        return null;
    }

    /**
     * @return array|bool|string|null
     */
    public function getInformation()
    {
        if ($this->isGenerated) {
            if (null === $this->token) {
                return false;
            }
            if ($this->token->getExpires()->getTimestamp() < time()) {
                return 'expired';
            }

            return $this->token->getOptionalInfo();
        }

        return null;
    }

    /**
     * @return bool|null
     */
    public function remove()
    {
        if ($this->isGenerated) {
            if (null === $this->token) {
                return false;
            }

            $this->em->remove($this->token);
            $this->em->flush();

            $this->isGenerated = false;
        }

        return null;
    }

    /**
     * @return string
     */
    private function getRandomNumber()
    {
        $nbBytes = 32;
        // try OpenSSL
        if ($this->useOpenSsl) {
            $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);
            if (false !== $bytes && true === $strong) {
                return $bytes;
            }
            throw new \UnexpectedValueException('OpenSSL did not produce a secure random number.');
        }

        return hash('sha256', uniqid(mt_rand(), true), true);
    }
}
