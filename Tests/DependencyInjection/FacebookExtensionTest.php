<?php

namespace EWZ\AuthBundle\Tests\DependencyInjection;

use EWZ\AuthBundle\DependencyInjection\FacebookExtension;

class FacebookExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers EWZ\AuthBundle\DependencyInjection\EWZAuthExtension::load
     */
    public function testApiLoadLoadsDefaults()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('auth.facebook')
            ->will($this->returnValue(false));

        $extension = $this->getMockBuilder('Bundle\\AuthBundle\\DependencyInjection\\EWZAuthExtension')
            ->setMethods(array('loadDefaults'))
            ->getMock();
        $extension
            ->expects($this->once())
            ->method('loadDefaults')
            ->with($container);

        $extension->load(array(), $container);
    }

    /**
     * @covers EWZ\AuthBundle\DependencyInjection\EWZAuthExtension::load
     */
    public function testConfigLoadDoesNotReloadDefaults()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('auth.facebook')
            ->will($this->returnValue(true));

        $extension = $this->getMockBuilder('Bundle\\AuthBundle\\DependencyInjection\\EWZAuthExtension')
            ->setMethods(array('loadDefaults'))
            ->getMock();
        $extension
            ->expects($this->never())
            ->method('loadDefaults');

        $extension->load(array(), $container);
    }

    /**
     * @covers EWZ\AuthBundle\DependencyInjection\EWZAuthExtension::load
     * @dataProvider parameterNames
     */
    public function testConfigLoadSetParameters($name)
    {
        $value = 'foo';

        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('auth.facebook')
            ->will($this->returnValue(true));
        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with('auth.facebook.'.$name, $value);

        $extension = new EWZAuthExtension();
        $extension->load(array($name => $value), $container);
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
