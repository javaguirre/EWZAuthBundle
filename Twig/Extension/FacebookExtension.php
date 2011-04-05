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
            'initialize' => new \Twig_Function_Method($this, 'initialize'),
        );
    }

    public function initialize($parameters = array(), $name = 'EWZAuthBundle::facebook.html.twig')
    {
        return $this->helper->initialize($parameters, $name);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'auth.facebook';
    }
}
