<?php

namespace EWZ\Bundle\AuthBundle\Tests\Templating\Helper;

use EWZ\Bundle\AuthBundle\Templating\Helper\FacebookHelper

class FacebookHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers EWZ\Bundle\AuthBundle\Templating\Helper\FacebookHelper::initialize
     */
    public function testInitialize()
    {
        $expected = new \stdClass();

        $templating = $this->getMockBuilder('Symfony\\Component\\Templating\\Engine')
            ->disableOriginalConstructor()
            ->getMock();
        $templating
            ->expects($this->once())
            ->method('render')
            ->with('AuthBundle::initialize.php', array(
                'appId'   => 123,
                'cookie'  => false,
                'culture' => 'en_US',
                'logging' => true,
                'session' => null,
                'status'  => false,
                'xfbml'   => false,
            ))
            ->will($this->returnValue($expected));

        $helper = new FacebookHelper($templating, '123');
        $this->assertSame($expected, $helper->initialize());
    }
}
