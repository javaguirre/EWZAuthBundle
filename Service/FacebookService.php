<?php

namespace EWZ\AuthBundle\Service;

/**
 * FacebookService is a service that manages Facebook API.
 */
class FacebookService extends Service
{
    protected $facebook;

    /**
     * Constructor.
     *
     * @param Facebook $facebook A Facebook instance
     */
    public function __construct(\Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Gets a Login URL for use with redirects. By default, full page redirect is
     * assumed. If you are using the generated URL with a window.open() call in
     * JavaScript, you can pass in display=popup as part of the $params.
     *
     * The parameters:
     * - req_perms: comma separated list of requested extended perms
     * - display: can be "page" (default, full page) or "popup"
     *
     * @param string $next       The URL to go to after a successful login
     * @param string $cancel     The URL to go to after the user cancels
     * @param array  $parameters Provide custom parameters
     *
     * @return string The URL for the login flow
     */
    public function getLoginUrl($next, $cancel, array $parameters = array())
    {
        return $this->facebook->getLoginUrl($parameters + array(
            'next'       => $next,
            'cancel_url' => $cancel,
        ));
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
        return $this->facebook->getLogoutUrl($parameters + array(
            'next' => $next,
        ));
    }

    /**
     * Gets the profile from the session.
     *
     * @return array The profile if available
     */
    public function getProfile()
    {
        try {
            if ($this->facebook->getSession()) {
                $accessToken = $this->facebook->getAccessToken();

                $me = $this->facebook->api('/me?access_token='.$accessToken);

                return array(
                    'id'     => $me['id'],
                    'name'   => $me['name'],
                    'url'    => $me['link'],
                    'extra'  => $me,
                    'token'  => $accessToken,
                    'secret' => null,
                );
            }
        } catch (\FacebookApiException $e) {
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
            $friends = $this->facebook->api('/me/friends?access_token='.$token);

            if (isset($friends['data'])) {
                return $friends['data'];
            }
        } catch (\FacebookApiException $e) {
        }

        return false;
    }

    /**
     * Returns the canonical name of this service.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'auth.facebook';
    }
}
