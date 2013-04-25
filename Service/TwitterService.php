<?php

namespace EWZ\Bundle\AuthBundle\Service;

use TwitterOAuth\Api;
use TwitterOAuth\OAuth\Exception;

/**
 * FacebookService is a service that manages Facebook API.
 */
class TwitterService extends Service
{
    protected $twitter;

    /**
     * Constructor.
     *
     * @param Api $facebook A Facebook instance
     */
    public function __construct(Api $twitter)
    {
        $this->twitter      = $twitter;
    }

    /**
     * {@inheritDoc}
     */
    public function getLoginUrl($next, $cancel, array $parameters = array())
    {
        $this->cleanSession();

        $requestToken = $this->twitter->getRequestToken($next);

        $this->session->set('auth/twitter/request/oauth_token', $requestToken['oauth_token']);
        $this->session->set('auth/twitter/request/oauth_token_secret', $requestToken['oauth_token_secret']);

        if ($this->twitter->http_code === 200) {
            return $this->twitter->getAuthorizeURL($requestToken['oauth_token']);
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
        try {
            // validate response
            if (!$this->request->query->has('oauth_token') || !$this->request->query->has('oauth_verifier')) {
                throw new Exception('Bad request parameters.');
            }

            // validate oauth_token
            if ($this->session->get('auth/twitter/request/oauth_token') !== $this->request->query->get('oauth_token')) {
                $this->session->remove('auth/twitter/request/oauth_token');
                $this->session->remove('auth/twitter/request/oauth_token_secret');

                throw new Exception('Invalid oauth_token.');
            }

            // set temporary credentials
            $this->twitter->setTokens(
                $this->session->get('auth/twitter/request/oauth_token'),
                $this->session->get('auth/twitter/request/oauth_token_secret')
            );

            // get token credentials
            $accessToken = $this->twitter->getAccessToken($this->request->query->get('oauth_verifier'));

            if ($this->twitter->http_code !== 200) {
                throw new Exception('Failed trying to get the access token.');
            }

            // save the access tokens
            $this->session->set('auth/twitter/access/oauth_token', $accessToken['oauth_token']);
            $this->session->set('auth/twitter/access/oauth_token_secret', $accessToken['oauth_token_secret']);

            // no longer needed
            $this->session->remove('auth/twitter/request/oauth_token');
            $this->session->remove('auth/twitter/request/oauth_token_secret');

            // set token credentials
            $this->twitter->setTokens(
                $this->session->get('auth/twitter/access/oauth_token'),
                $this->session->get('auth/twitter/access/oauth_token_secret')
            );

            // get account credentials
            $twitterInfo = $this->twitter->get('account/verify_credentials');

            if ($this->twitter->http_code !== 200) {
                throw new Exception('Failed getting user credientials.');
            }

            return array(
                'id'     => $twitterInfo->id,
                'name'   => $twitterInfo->name,
                'url'    => 'http://www.twitter.com/'.$twitterInfo->screen_name,
                'extra'  => get_object_vars($twitterInfo),
                'token'  => $accessToken['oauth_token'],
                'secret' => $accessToken['oauth_token_secret'],
            );
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileFromApi()
    {
        try {
            // validate response
            if (!$this->request->query->has('oauth_token') || !$this->request->query->has('oauth_token_secret')) {
                throw new Exception('Bad request parameters.');
            }

            // set token credentials
            $this->twitter->setTokens(
                $this->request->query->get('oauth_token'),
                $this->request->query->get('oauth_token_secret')
            );

            // get account credentials
            $twitterInfo = $this->twitter->get('account/verify_credentials');

            if ($this->twitter->http_code !== 200) {
                throw new Exception('Failed getting user credientials.');
            }

            return array(
                'id'     => $twitterInfo->id,
                'name'   => $twitterInfo->name,
                'url'    => 'http://www.twitter.com/'.$twitterInfo->screen_name,
                'extra'  => get_object_vars($twitterInfo),
                'token'  => $this->request->query->get('oauth_token'),
                'secret' => $this->request->query->get('oauth_token_secret')
            );
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getFriends($userId = null, $token = null)
    {
        try {
            // get account credentials
            $result = $this->twitter->get('statuses/friends/'.$userId);

            $friends = array();

            foreach ($result as $friend) {
                $friends[] = array(
                    'id'   => $friend['id'],
                    'name' => $friend['name'],
                );
            }

            if (count($friends)) {
                return $friends;
            }
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * Removes the service session values.
     */
    protected function cleanSession()
    {
        $this->session->remove('auth/twitter/request/oauth_token');
        $this->session->remove('auth/twitter/request/oauth_token_secret');
        $this->session->remove('auth/twitter/access/oauth_token');
        $this->session->remove('auth/twitter/access/oauth_token_secret');
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
    public function get($path, $params=array()) {
        return $this->twitter->get($path, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, $params=array()) {
        return $this->twitter->post($path, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $params=array()) {
        return $this->twitter->put($path, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, $params=array()) {
        return $this->twitter->delete($path, $params);
    }

    /**
     * Set twitter tokens
     * @param string $oauthToken
     * @param string $oauthTokenSecret
     */
    public function setTokens($oauthToken, $oauthTokenSecret) {
        $this->twitter->setTokens($oauthToken, $oauthTokenSecret);
    }
}
