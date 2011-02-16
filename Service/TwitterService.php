<?php

namespace EWZ\AuthBundle\Service;

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
        $this->twitter = $twitter;
    }

    /**
     * Gets a Login URL for use with redirects.
     *
     * @param string $next       The URL to go to after a successful login
     * @param string $cancel     The URL to go to after the user cancels
     * @param array  $parameters Provide custom parameters
     *
     * @return string The URL for the login flow
     */
    public function getLoginUrl($next, $cancel, array $parameters = array())
    {
        $this->cleanSession();

        $requestToken = $this->twitter->getRequestToken($next);

        $this->session->set('auth/twitter/request/auth_token', $requestToken['auth_token']);
        $this->session->set('auth/twitter/request/auth_token_secret', $requestToken['auth_token_secret']);

        if ($this->twitter->http_code === 200) {
            return $this->twitter->getAuthorizeURL($requestToken['auth_token']);
        }

        return $cancel;
    }

    /**
     * Gets a Logout URL suitable for use with redirects.
     *
     * @param string $next       The URL to go to after a successful logout
     * @param array  $parameters Provide custom parameters
     *
     * @return string The URL for the logout flow
     */
    public function getLogoutUrl($next, array $parameters = array())
    {
        $this->cleanSession();

        return $next;
    }

    /**
     * Gets the profile from the session.
     *
     * @return array The profile if available
     */
    public function getProfile()
    {
        try {
            // validate response
            if (!$this->request->query->has('auth_token') || !$this->request->query->has('auth_verifier')) {
                throw new Exception('Bad request parameters.');
            }

            // validate auth_token
            if ($this->session->get('auth/twitter/request/auth_token') !== $this->request->query->get('auth_token')) {
                $this->session->remove('auth/twitter/request/auth_token');
                $this->session->remove('auth/twitter/request/auth_token_secret');

                throw new Exception('Invalid auth_token.');
            }

            // set temporary credentials
            $this->twitter->setTokens(
                $this->session->get('auth/twitter/request/auth_token'),
                $this->session->get('auth/twitter/request/auth_token_secret')
            );

            // get token credentials
            $accessToken = $this->twitter->getAccessToken($this->request->query->get('auth_verifier'));

            if ($this->twitter->http_code !== 200) {
                throw new Exception('Failed trying to get the access token.');
            }

            // save the access tokens
            $this->session->set('auth/twitter/access/auth_token', $accessToken['auth_token']);
            $this->session->set('auth/twitter/access/auth_token_secret', $accessToken['auth_token_secret']);

            // no longer needed
            $this->session->remove('auth/twitter/request/auth_token');
            $this->session->remove('auth/twitter/request/auth_token_secret');

            // set token credentials
            $this->twitter->setTokens(
                $this->session->get('auth/twitter/access/auth_token'),
                $this->session->get('auth/twitter/access/auth_token_secret')
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
                'token'  => $accessToken['auth_token'],
                'secret' => $accessToken['auth_token_secret'],
            );
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * Gets the profile friends.
     *
     * @param string $user_id The user id
     * @param string $token   An identifier token
     *
     * @return array The profile friends if available
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
        $this->session->remove('auth/twitter/request/auth_token');
        $this->session->remove('auth/twitter/request/auth_token_secret');
        $this->session->remove('auth/twitter/access/auth_token');
        $this->session->remove('auth/twitter/access/auth_token_secret');
    }

    /**
     * Returns the canonical name of this service.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'auth.twitter';
    }
}
