<?php

namespace App\Repository;

use App\Entity\OAuthClient;
use App\Entity\OAuthRefreshToken;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use OAuth2\Storage\RefreshTokenInterface;

class OAuthRefreshTokenRepository extends EntityRepository implements RefreshTokenInterface
{
    public function getRefreshToken($refreshToken)
    {
        $refreshToken = $this->findOneBy(['refresh_token' => $refreshToken]);
        if ($refreshToken) {
            $refreshToken = $refreshToken->toArray();
            $refreshToken['expires'] = $refreshToken['expires']->getTimestamp();
        }

        return $refreshToken;
    }

    public function setRefreshToken($refreshToken, $clientIdentifier, $userEmail, $expires, $scope = null)
    {
        /** @var OAuthClient $client */
        $client = $this->_em->getRepository(OAuthClient::class)->findOneBy(['client_identifier' => $clientIdentifier]);

        /** @var User $user */
        $user = $this->_em->getRepository(User::class)->findOneBy(['id' => $userEmail]);

        $refreshToken = OAuthRefreshToken::fromArray(
            [
                'refresh_token' => $refreshToken,
                'client' => $client,
                'user' => $user,
                'expires' => (new \DateTime())->setTimestamp($expires),
                'scope' => $scope,
            ]
        );
        $this->_em->persist($refreshToken);
        $this->_em->flush();
    }

    public function unsetRefreshToken($refreshToken)
    {
        $refreshToken = $this->findOneBy(['refresh_token' => $refreshToken]);
        if (null === $refreshToken) {
            return false;
        }

        $this->_em->remove($refreshToken);
        $this->_em->flush();

        return true;
    }
}
