<?php

namespace EWZ\Bundle\AuthBundle\Service;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRedirectLoginHelper;


class Facebook
{
    const SESSION_INVALID_CODE = 190;
    const FB_NEXT = 'facebook_next';

    protected $appId;

    protected $secret;

    protected $token;

    // Symfony session
    protected $sfSession;

    // Facebook session
    protected $fbSession;

    public function __construct($appId, $secret, $sfSession)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->sfSession = $sfSession;

        FacebookSession::setDefaultApplication(
            $this->appId,
            $this->secret
        );
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function getAppSecret()
    {
        return $this->secret;
    }

    public function getLoginUrl($next, $scope)
    {
        $this->sfSession->set(self::FB_NEXT, $next);
        $helper = $this->getRedirectHelper($next);

        return $helper->getLoginUrl($scope);
    }

    public function getLogoutUrl($next)
    {
        $helper = $this->getRedirectHelper($next);

        return $helper->getLogoutUrl($this->getSession(), $next);
    }

    public function getSession($token = null)
    {
        $next = $this->sfSession->get(self::FB_NEXT, null);

        if ($token) {
            $this->fbSession = new FacebookSession($token);
        } elseif ($next && !$this->fbSession) {
            $helper = $this->getRedirectHelper($next);
            $this->fbSession = $helper->getSessionFromRedirect();
        }

        return $this->fbSession;
    }

    public function getAccessToken()
    {
        if (!$this->fbSession) {
            return null;
        }

        return $this->fbSession->getToken();
    }

    public function setAccessToken($token)
    {
        $this->token = $token;
    }

    public function api($url, $method = 'GET', $payload = null)
    {
        $request = new FacebookRequest(
            $this->getSession(),
            $method,
            $url
        );

        $result = $request->execute()->getGraphObject();

        return $result->asArray();
    }

    private function getRedirectHelper($next = null)
    {
        $helper = new FacebookRedirectLoginHelper(
            $next,
            $this->getAppId(),
            $this->getAppSecret()
        );

        return $helper;
    }
}