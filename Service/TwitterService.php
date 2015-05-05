<?php

namespace EWZ\Bundle\AuthBundle\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;

/**
 * TwitterService is a service that manages Twitter API.
 */
class TwitterService extends Service
{
    const SESSION_REQUEST_TOKEN = 'auth/twitter/request/oauth_token';
    const SESSION_REQUEST_SECRET = 'auth/twitter/request/oauth_token_secret';
    const SESSION_ACCESS_TOKEN = 'auth/twitter/access/oauth_token';
    const SESSION_ACCESS_SECRET = 'auth/twitter/access/oauth_token_secret';

    protected $twitter;

    /**
     * Constructor.
     *
     * @param Api $facebook A Facebook instance
     */
    public function __construct(TwitterOAuth $twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * {@inheritDoc}
     */
    public function getLoginUrl($next, $cancel, array $parameters = array())
    {
        $this->cleanSession();

        $requestToken = $this->twitter->oauth(
            'oauth/request_token',
            array('oauth_callback' => $next)
        );

        $this->session->set(
            self::SESSION_REQUEST_TOKEN,
            $requestToken['oauth_token']
        );
        $this->session->set(
            self::SESSION_REQUEST_SECRET,
            $requestToken['oauth_token_secret']
        );

        if ($this->twitter->getLastHttpCode() == 200) {
            return $this->twitter->url(
                'oauth/authorize',
                array('oauth_token' => $requestToken['oauth_token'])
            );
        }

        return $cancel;
    }

    /**
     * {@inheritDoc}
     */
    public function getLogoutUrl($next, array $parameters = array())
    {
        $this->cleanSession();

        return $next;
    }

    /**
     * {@inheritDoc}
     */
    public function getProfile()
    {
        // validate response
        if (!$this->request->query->has('oauth_token') || !$this->request->query->has('oauth_verifier')) {
            throw new \Exception('Bad request parameters.');
        }

        // validate oauth_token
        if ($this->session->get(self::SESSION_REQUEST_TOKEN) !== $this->request->query->get('oauth_token')) {
            $this->session->remove(self::SESSION_REQUEST_TOKEN);
            $this->session->remove(self::SESSION_REQUEST_SECRET);

            throw new \Exception('Invalid oauth_token.');
        }

        $this->setTokens(
            $this->session->get(self::SESSION_REQUEST_TOKEN),
            $this->session->get(self::SESSION_REQUEST_SECRET)
        );

        // get token credentials
        $accessToken = $this->twitter->oauth(
            'oauth/access_token',
            array('oauth_verifier' => $this->request->query->get('oauth_verifier'))
        );

        if ($this->twitter->getLastHttpCode() !== 200) {
            throw new \Exception('Failed trying to get the access token.');
        }

        // save the access tokens
        $this->session->set(self::SESSION_ACCESS_TOKEN, $accessToken['oauth_token']);
        $this->session->set(self::SESSION_ACCESS_SECRET, $accessToken['oauth_token_secret']);

        // no longer needed
        $this->session->remove(self::SESSION_REQUEST_TOKEN);
        $this->session->remove(self::SESSION_REQUEST_SECRET);

        // set token credentials
        $this->setTokens(
            $this->session->get(self::SESSION_ACCESS_TOKEN),
            $this->session->get(self::SESSION_ACCESS_SECRET)
        );

        return $this->getProfileArray($accessToken);
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileFromApi()
    {
        // validate response
        if (!$this->request->query->has('oauth_token') || !$this->request->query->has('oauth_token_secret')) {
            throw new \Exception('Bad request parameters.');
        }

        // set token credentials
        $accessToken = $this->setTokens(
            $this->request->query->get('oauth_token'),
            $this->request->query->get('oauth_token_secret')
        );

        return $this->getProfileArray($accessToken);
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileFromToken($token, $secret = null)
    {
        if (!$token || !$secret) {
            throw new \Exception('Bad tokens in Twitter.getProfileFromToken');
        }

        $accessToken = $this->setTokens($token, $secret);

        return $this->getProfileArray($accessToken);
    }

    /**
     * {@inheritDoc}
     */
    public function getFriends($userId = null, $token = null)
    {
        // get account credentials
        $result = $this->twitter->get('friends/list.json');

        $friendsCount = 0;
        $friends = array();

        foreach ($result as $friend) {
            if (!isset($friend['id'])) {
                break;
            }

            $friends[] = array(
                'id'   => $friend['id'],
                'name' => $friend['name'],
            );
        }

        if (count($friends)) {
            $friendsCount = count($friends);
        }

        return $friendsCount;
    }

    /**
     * Removes the service session values.
     */
    protected function cleanSession()
    {
        $this->session->remove(self::SESSION_REQUEST_TOKEN);
        $this->session->remove(self::SESSION_REQUEST_SECRET);
        $this->session->remove(self::SESSION_ACCESS_TOKEN);
        $this->session->remove(self::SESSION_ACCESS_SECRET);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ewz_auth.twitter';
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, $params=array())
    {
        return $this->twitter->get($path, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, $params=array())
    {
        return $this->twitter->post($path, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $params=array())
    {
        return $this->twitter->put($path, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, $params=array())
    {
        return $this->twitter->delete($path, $params);
    }

    /**
     * Set twitter tokens
     * @param string $oauthToken
     * @param string $oauthTokenSecret
     */
    public function setTokens($oauthToken, $oauthTokenSecret)
    {
        $this->twitter->setOauthToken($oauthToken, $oauthTokenSecret);

        return array(
            'oauth_token'        => $oauthToken,
            'oauth_token_secret' => $oauthTokenSecret
        );
    }

    private function getProfileArray($accessToken)
    {
        // get account credentials
        $twitterInfo = $this->twitter->get(
            'account/verify_credentials'
        );

        if ($this->twitter->getLastHttpCode() !== 200) {
            throw new \Exception('Failed getting user credientials.');
        }

        return array(
            'id'     => $twitterInfo->id,
            'name'   => $twitterInfo->name,
            'url'    => 'http://www.twitter.com/' . $twitterInfo->screen_name,
            'extra'  => get_object_vars($twitterInfo),
            'token'  => $accessToken['oauth_token'],
            'secret' => $accessToken['oauth_token_secret'],
        );
    }
}
