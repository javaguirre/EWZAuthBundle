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
    public function __construct(\Facebook $facebook)
    {
        $this->facebook     = $facebook;
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
        return $this->facebook->getLoginUrl($parameters + array(
            'redirect_uri' => $next,
            'cancel_url'   => $cancel,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getLogoutUrl($next, array $parameters = array())
    {
        return $this->facebook->getLogoutUrl($parameters + array(
            'next' => $next,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getProfile()
    {
        try {
            if ($accessToken = $this->facebook->getAccessToken()) {
                $this->facebook->setAccessToken($accessToken);
                $me = $this->facebook->api('/me');

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
     * {@inheritDoc}
     */
    public function getProfileFromApi()
    {
        // validate response
        if (!$this->request->query->has('access_token')) {
            throw new Exception('Bad request parameters.');
        }

        try {
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
        } catch (\FacebookApiException $e) {
        }

        return false;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ewz_auth.facebook';
    }

    public function setAccessToken($accessToken) {
        $this->facebook->setAccessToken($accessToken);
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, $params=array()) {
        return $this->api($path, 'GET', $params);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, $params=array()) {
        return $this->api($path, 'POST', $params);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $params=array()) {
        return $this->api($path, 'PUT', $params);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, $params=array()) {
        return $this->api($path, 'DELETE', $params);
    }

    /**
     * {@inheritDoc}
     */
    public function api($path, $method='GET', $params=array()) {

        try {
            // $this->facebook->setFileUploadSupport(true);
            $result = $this->facebook->api($path, $method, $params);

            if (isset($result['data'])) {
                return $result['data'];
            }
            return $result;
        } catch (\FacebookApiException $e) {
            echo $e;
        }

        return false;
    }
}
