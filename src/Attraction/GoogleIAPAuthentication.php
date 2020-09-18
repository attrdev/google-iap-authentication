<?php

namespace Attraction;

class GoogleIAPAuthentication
{

    const ISSUER = 'https://cloud.google.com/iap';

    private $projectId, $projectNumber;

    public function __construct(string $projectId, int $projectNumber)
    {
        $this->projectId = $projectId;
        $this->projectNumber = $projectNumber;
    }

    private function getAudience()
    {
        return sprintf(
            '/projects/%s/apps/%s',
            $this->projectNumber,
            $this->projectId
        );
    }

    public function validateAssertion(string $idToken)
    {

        $auth = new \Google\Auth\AccessToken();
        $info = $auth->verify($idToken, [
            'certsLocation' => \Google\Auth\AccessToken::IAP_CERT_URL
        ]);

        if (!$info || $info['iss'] != self::ISSUER || $info['aud'] != $this->getAudience()) {
            return false;
        }

        $emailAddress = $info['email'] ?? false;
        $id = $info['sub'] ?? false;

        if (!$emailAddress || !$id) {
            return false;
        }

        return (object) [
            'emailAddress' => $emailAddress,
            'id' => $id
        ];
    }
}
