<?php

namespace EWZ\Bundle\AuthBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FacebookExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'initialize' => new \Twig_Function_Method($this, 'initialize'),
        );
    }

    public function initialize($parameters = array(), $name = 'EWZAuthBundle::facebook.html.twig')
    {
        return $this->container->get('templating.helper.auth.facebook')->initialize($parameters, $name);
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
