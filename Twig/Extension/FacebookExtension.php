<?php

namespace EWZ\Bundle\AuthBundle\Twig\Extension;

use EWZ\Bundle\AuthBundle\Templating\Helper\FacebookHelper;

class FacebookExtension extends \Twig_Extension
{
    protected $helper;

    public function __construct(FacebookHelper $helper)
    {
        $this->helper = $helper;
    }

    public function getFunctions()
    {
        return array(
            'ewz_auth_facebook_initialize' => new \Twig_Function_Method($this, 'initialize', array('is_safe' => array('html'))),
        );
    }

    public function initialize($parameters = array(), $name = 'EWZAuthBundle::facebook.html.twig')
    {
        return $this->$helper->initialize($parameters, $name);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ewz_auth.facebook';
    }
}
