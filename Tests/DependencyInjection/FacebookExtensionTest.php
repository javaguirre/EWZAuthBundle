<?php

namespace Bundle\OAuthBundle\Tests\DependencyInjection;

use Bundle\OAuthBundle\DependencyInjection\FacebookExtension;

class FacebookExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Bundle\OAuthBundle\DependencyInjection\OAuthExtension::configLoad
     */
    public function testApiLoadLoadsDefaults()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('oauth.facebook')
            ->will($this->returnValue(false));

        $extension = $this->getMockBuilder('Bundle\\OAuthBundle\\DependencyInjection\\OAuthExtension')
            ->setMethods(array('loadDefaults'))
            ->getMock();
        $extension
            ->expects($this->once())
            ->method('loadDefaults')
            ->with($container);

        $extension->configLoad(array(), $container);
    }

    /**
     * @covers Bundle\OAuthBundle\DependencyInjection\OAuthExtension::configLoad
     */
    public function testConfigLoadDoesNotReloadDefaults()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('oauth.facebook')
            ->will($this->returnValue(true));

        $extension = $this->getMockBuilder('Bundle\\OAuthBundle\\DependencyInjection\\OAuthExtension')
            ->setMethods(array('loadDefaults'))
            ->getMock();
        $extension
            ->expects($this->never())
            ->method('loadDefaults');

        $extension->configLoad(array(), $container);
    }

    /**
     * @covers Bundle\OAuthBundle\DependencyInjection\OAuthExtension::configLoad
     * @dataProvider parameterNames
     */
    public function testConfigLoadSetParameters($name)
    {
        $value = 'foo';

        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('oauth.facebook')
            ->will($this->returnValue(true));
        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with('oauth.facebook.'.$name, $value);

        $extension = new OAuthExtension();
        $extension->configLoad(array($name => $value), $container);
    }

    public function parameterNames()
    {
        return array(
            array('class'),
            array('app_id'),
            array('secret'),
            array('cookie'),
            //array('domain'),
            //array('logging'),
            //array('culture'),
        );
    }
}
