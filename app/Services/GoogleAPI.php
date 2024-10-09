<?php


namespace App\Services;


use Google\Client as Google;

class GoogleAPI
{
    public const GAUTH_CODE = 'gauth-code';

    /**
     * @var Google
     */
    private Google $google;
    /**
     * @var \Google_Service_Oauth2|null
     */
    private ?\Google_Service_Oauth2 $oauth2 = null;

    /**
     * GoogleAPI constructor.
     * @param Google $google
     */
    public function __construct(Google $google)
    {
        $this->google = $google;

        $this->google->addScope([
            \Google_Service_Oauth2::USERINFO_PROFILE,
            \Google_Service_Oauth2::USERINFO_EMAIL
        ]);
        $this->google->setRedirectUri(route('gauth.postback'));
        $this->google->setApprovalPrompt('force');

    }

    /**
     * @param string $code
     * @return array
     * @throws \Exception
     */
    public function getToken(string $code): array
    {
        $token = $this->google->fetchAccessTokenWithAuthCode($code);

        if ($token && array_key_exists('access_token', $token)) {
            return $token;
        }

        throw new \Exception(array_key_exists('error', $token) ? sprintf('Request failed. Error: "%s"', $token['error']) : 'Request failed.');
    }

    public function createAuthUrl(): string
    {
        return $this->google->createAuthUrl();
    }

    /**
     * @return \Google_Service_Oauth2_Userinfo
     * @throws \Exception
     */
    public function getUserInfo(): \Google_Service_Oauth2_Userinfo
    {
        return $this->getOauth2()->userinfo->get();
    }

    /**
     * @return \Google_Service_Oauth2
     * @throws \Exception
     */
    private function getOauth2(): \Google_Service_Oauth2
    {
        if ($this->oauth2) {
            return $this->oauth2;
        }

        if (!array_key_exists('access_token', $this->google->getAccessToken())) {
            throw new \Exception('Please authorize first!');
        }

        $this->oauth2 = new \Google_Service_Oauth2($this->google);

        return $this->oauth2;
    }
}
