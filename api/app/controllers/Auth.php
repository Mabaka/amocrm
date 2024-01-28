<?php
namespace Controllers;
use Exception;
use AmoCRM\OAuth2\Client\Provider\AmoCRM;
use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Token\AccessToken;

class Auth
{
    private string $token_file = 'token_info.json';

    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;

    public function __construct($clientId, $clientSecret, $redirectUri)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    public function getAuthToken()
    {
        $provider = new AmoCRM([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->redirectUri,
        ]);

        if (isset($_GET['referer'])) {
            $provider->setBaseDomain($_GET['referer']);
        }

        $accessToken = $this->getToken();

        if ($accessToken) {
            if ($accessToken->hasExpired()) {
                try {
                    $accessToken = $provider->getAccessToken(new RefreshToken(), [
                        'refresh_token' => $accessToken->getRefreshToken(),
                    ]);

                    $this->saveToken([
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $provider->getBaseDomain(),
                    ]);

                } catch (Exception $e) {
                    die((string) $e);
                }
            }
        } else {
            if (isset($_GET['code'])) {
                try {
                    $accessToken = $provider->getAccessToken(new AuthorizationCode(), [
                        'code' => $_GET['code'],
                    ]);

                    if (!$accessToken->hasExpired()) {
                        $this->saveToken([
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $provider->getBaseDomain(),
                        ]);
                    }
                } catch (Exception $e) {
                    die((string) $e);
                }
            }
        }
        $accessToken = $this->getToken();
        return $accessToken;

    }

    private function saveToken($accessToken)
    {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];
            file_put_contents($this->token_file, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    private function getToken()
    {
        if (file_exists($this->token_file)) {
            $accessToken = json_decode(file_get_contents($this->token_file), true);
            if (
                isset($accessToken)
                && isset($accessToken['accessToken'])
                && isset($accessToken['refreshToken'])
                && isset($accessToken['expires'])
                && isset($accessToken['baseDomain'])
            ) {
                return new AccessToken([
                    'access_token' => $accessToken['accessToken'],
                    'refresh_token' => $accessToken['refreshToken'],
                    'expires' => $accessToken['expires'],
                    'baseDomain' => $accessToken['baseDomain'],
                ]);
            } else {
                exit('Invalid access token ' . var_export($accessToken, true));
            }
        } else {
            return false;
        }

    }
}