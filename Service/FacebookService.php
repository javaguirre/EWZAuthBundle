<?php

namespace EWZ\Bundle\AuthBundle\Service;

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
    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * {@inheritDoc}
     *
     * Gets a Login URL for use with redirects. By default, full page redirect is
     * assumed. If you are using the generated URL with a window.open() call in
     * JavaScript, you can pass in display=popup as part of the $params.
     *
     * The parameters:
     * - scope: comma separated list of requested extended perms
     * - display: can be "page" (default, full page) or "popup"
     */
    public function getLoginUrl($next, $cancel, array $parameters = array())
    {
        return $this->facebook->getLoginUrl($next);
    }

    /**
     * {@inheritDoc}
     */
    public function getLogoutUrl($next, array $parameters = array())
    {
        return $this->facebook->getLogoutUrl($parameters['token'], $next);
    }

    /**
     * {@inheritDoc}
     */
    public function getProfile()
    {
        if ($accessToken = $this->facebook->getAccessToken()->getToken()) {
            $this->facebook->setAccessToken($accessToken);
            $me = $this->facebook->api('/me');
            $picture = $this->facebook->api(
                '/me/picture?type=large&redirect=false'
            );

            return array(
                'id'      => $me['id'],
                'name'    => isset($me['name']) ? $me['name'] : '',
                'url'     => isset($me['link']) ? $me['link'] : '',
                'extra'   => $me,
                'token'   => $accessToken,
                'secret'  => null,
                'picture' => $picture
            );
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileFromApi()
    {
        // validate response
        if (!$this->request->query->has('access_token')) {
            throw new \Exception('Bad request parameters.');
        }

        $this->facebook->setAccessToken($this->request->query->get('access_token'));
        $me = $this->facebook->api('/me');

        return array(
            'id'     => $me['id'],
            'name'   => $me['name'],
            'url'    => $me['link'],
            'extra'  => $me,
            'token'  => $this->request->query->get('access_token'),
            'secret' => null,
        );

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileFromToken($token, $secret = null)
    {
        // validate response
        if (!$token) {
            throw new \Exception('Bad request parameters.');
        }

        $this->facebook->setAccessToken($token);
        $me = $this->facebook->api('/me');
        $picture= $this->facebook->api('/me/picture?type=large&redirect=false');

        return array(
            'id'     => $me['id'],
            'name'   => $me['name'],
            'url'    => $me['link'],
            'extra'  => $me,
            'token'  => $token,
            'secret' => null,
            'picture' => $picture
        );

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getFriends($userId = null, $token = null)
    {
        $friends = false;
        $response = $this->facebook->api('/me/friends');

        if (isset($response['data'])) {
            $friends = $response['data'];
        }

        return $friends;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ewz_auth.facebook';
    }

    public function setAccessToken($accessToken)
    {
        $this->facebook->setAccessToken($accessToken);
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, $params=array())
    {
        return $this->facebook->api($path, 'GET', $params);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, $params=array())
    {
        return $this->facebook->api($path, 'POST', $params);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $params=array())
    {
        return $this->facebook->api($path, 'PUT', $params);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, $params=array())
    {
        return $this->facebook->api($path, 'DELETE', $params);
    }
}
