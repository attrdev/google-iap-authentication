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

    public function validateAssertion(string $idToken, bool $stripPrefix): object
    {
        $auth = new \Google\Auth\AccessToken();
        $info = $auth->verify($idToken, [
            'certsLocation' => \Google\Auth\AccessToken::IAP_CERT_URL,
            'throwException' => true,
        ]);

        $audience = $this->getAudience();
        if ($audience != $info['aud'] ?? '') {
            throw new \Exception(sprintf(
                'Audience %s did not match expected %s',
                $info['aud'],
                $audience
            ));
        }

        if ($info['iss'] != self::ISSUER) {
            throw new \Exception(sprintf(
                'Issuer %s did not match expected %s',
                $info['iss'],
                self::ISSUER
            ));
        }

        $emailAddress = $info['email'];
        $userId = $info['sub'];

        if ($stripPrefix) {
            $emailAddress = str_replace('accounts.google.com:', '', $emailAddress);
            $userId = str_replace('accounts.google.com:', '', $userId);
        }

        return (object) [
            'emailAddress' => $emailAddress,
            'userId' => $userId
        ];
    }

}
