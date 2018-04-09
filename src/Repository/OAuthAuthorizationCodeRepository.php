<?php

namespace App\Repository;

use App\Entity\OAuthAuthorizationCode;
use App\Entity\OAuthClient;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use OAuth2\Storage\AuthorizationCodeInterface;

class OAuthAuthorizationCodeRepository extends EntityRepository implements AuthorizationCodeInterface
{
    public function getAuthorizationCode($code)
    {
        $authCode = $this->findOneBy(['code' => $code]);
        if ($authCode) {
            $authCode = $authCode->toArray();
            $authCode['expires'] = $authCode['expires']->getTimestamp();
        }

        return $authCode;
    }

    public function setAuthorizationCode($code, $clientIdentifier, $userEmail, $redirectUri, $expires, $scope = null)
    {
        /** @var \App\Entity\OAuthClient $client */
        $client = $this->_em->getRepository(OAuthClient::class)->findOneBy(['client_identifier' => $clientIdentifier]);

        /** @var \App\Entity\User $user */
        $user = $this->_em->getRepository(User::class)->findOneBy(['id' => $userEmail]);

        $authCode = OAuthAuthorizationCode::fromArray([
            'code' => $code,
            'client' => $client,
            'user' => $user,
            'redirect_uri' => $redirectUri,
            'expires' => (new \DateTime())->setTimestamp($expires),
            'scope' => $scope,
        ]);
        $this->_em->persist($authCode);
        $this->_em->flush();
    }

    public function expireAuthorizationCode($code)
    {
        $authCode = $this->findOneBy(['code' => $code]);
        if (null === $authCode) {
            return false;
        }

        $this->_em->remove($authCode);
        $this->_em->flush();

        return true;
    }
}
