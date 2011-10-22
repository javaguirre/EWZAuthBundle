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
            'ewz_auth_facebook_initialize' => new \Twig_Function_Method($this, 'initialize', array('is_safe' => array('html'))),
        );
    }

    public function initialize(array $parameters = array(), $name = null)
    {
        return $this->container->get('ewz_auth.facebook_helper')->initialize($parameters, $name ?: 'EWZAuthBundle::facebook.html.twig');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ewz_auth.facebook';
    }
}
