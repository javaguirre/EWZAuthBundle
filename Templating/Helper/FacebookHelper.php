<?php

namespace EWZ\AuthBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;

class FacebookHelper extends Helper
{
    protected $templating;
    protected $appId;
    protected $cookie;

    public function __construct(EngineInterface $templating, $appId, $cookie = false)
    {
        $this->templating = $templating;
        $this->appId      = $appId;
        $this->cookie     = $cookie;
    }

    /**
     * Returns the HTML necessary for initializing the JavaScript SDK.
     *
     * The default template includes the following parameters:
     *
     *  - appId
     *  - xfbml
     *  - session
     *  - status
     *  - cookie
     *  - logging
     *  - culture
     *
     * @param array  $parameters An array of parameters for the initialization template
     * @param string $name       A template name
     *
     * @return string An HTML string
     */
    public function initialize($parameters = array(), $name = 'AuthBundle::facebook.html.php')
    {
        return $this->templating->render($name, $parameters + array(
            'appId'   => $this->appId,
            'cookie'  => $this->cookie,
            'xfbml'   => false,
            'session' => null,
            'status'  => false,
            'logging' => false,
            'culture' => 'en_US',
        ));
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'auth.facebook';
    }
}
