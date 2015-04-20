<?php

namespace EWZ\Bundle\AuthBundle\Service;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;


class Facebook
{
    const FB_NEXT = 'facebook_next';

    protected $appId;

    protected $secret;

    protected $token;

    protected $session;

    public function __construct($appId, $secret, $session)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->session = $session;

        $this->token = null;

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

    public function getLoginUrl($next)
    {
        $this->session->set(self::FB_NEXT, $next);
        $helper = $this->getRedirectHelper($next);

        return $helper->getLoginUrl();
    }

    public function getLogoutUrl($next, $session = null)
    {
        $helper = $this->getRedirectHelper($next);

        if (!$session) {
            $session = $this->token;
        }

        return $helper->getLogoutUrl($session, $next);
    }

    public function getAccessToken()
    {
        $next = $this->session->get(self::FB_NEXT, null);
        $helper = $this->getRedirectHelper($next);

        $this->token = $helper->getSessionFromRedirect();

        return $this->token;
    }

    public function setAccessToken($token)
    {
        $this->token = $token;
    }

    public function api($url)
    {
        try {
            $request = new FacebookRequest($this->token, 'GET', $url);
            $result = $request->execute()
                ->getGraphObject(GraphUser::className());
        } catch (FacebookRequestException $e) {
            // The Graph API returned an error
        } catch (\Exception $e) {
            // Some other error occurred
        }

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