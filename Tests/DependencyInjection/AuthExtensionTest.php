<?php

namespace EWZ\Bundle\AuthBundle\Tests\DependencyInjection;

use EWZ\Bundle\AuthBundle\DependencyInjection\EWZAuthExtension;

class AuthExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers EWZ\Bundle\AuthBundle\DependencyInjection\EWZAuthExtension::load
     */
    public function testApiLoadLoadsDefaults()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('ewz_auth.facebook')
            ->will($this->returnValue(false));

        $extension = $this->getMockBuilder('EWZ\\Bundle\\AuthBundle\\DependencyInjection\\EWZAuthExtension')
            ->setMethods(array('loadDefaults'))
            ->getMock();
        $extension
            ->expects($this->once())
            ->method('loadDefaults')
            ->with($container);

        $extension->load(array(), $container);
    }

    /**
     * @covers EWZ\Bundle\AuthBundle\DependencyInjection\EWZAuthExtension::load
     */
    public function testConfigLoadDoesNotReloadDefaults()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('ewz_auth.facebook')
            ->will($this->returnValue(true));

        $extension = $this->getMockBuilder('EWZ\\Bundle\\AuthBundle\\DependencyInjection\\EWZAuthExtension')
            ->setMethods(array('loadDefaults'))
            ->getMock();
        $extension
            ->expects($this->never())
            ->method('loadDefaults');

        $extension->load(array(), $container);
    }

    /**
     * @covers EWZ\Bundle\AuthBundle\DependencyInjection\EWZAuthExtension::load
     * @dataProvider parameterNames
     */
    public function testConfigLoadSetParameters($name)
    {
        $value = 'foo';

        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('ewz_auth.facebook')
            ->will($this->returnValue(true));
        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with('ewz_auth.facebook.'.$name, $value);

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
