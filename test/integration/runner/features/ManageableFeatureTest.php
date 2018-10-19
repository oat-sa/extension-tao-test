<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoTests\test\integration\runner\features;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\oatbox\log\LoggerService;
use oat\taoTests\models\runner\features\ManageableFeature;
use oat\taoTests\models\runner\plugins\TestPlugin;
use oat\taoTests\models\runner\plugins\TestPluginService;

class ManageableFeatureTest extends GenerisPhpUnitTestRunner
{

    protected $defaultData = [
        ManageableFeature::OPTION_ID => 'feature id',
        ManageableFeature::OPTION_ACTIVE => true,
        ManageableFeature::OPTION_DESCRIPTION => 'feature desc',
        ManageableFeature::OPTION_ENABLED_BY_DEFAULT => true,
        ManageableFeature::OPTION_PLUGIN_IDS => ['foo', 'bar'],
        ManageableFeature::OPTION_LABEL => 'label',
    ];

    public function testIsActive()
    {
        $feature = new ManageableFeature($this->defaultData);

        $this->assertTrue($feature->isActive());
    }

    public function testSetActive()
    {
        $feature = new ManageableFeature($this->defaultData);

        $this->assertTrue($feature->isActive());
        $feature->setActive(false);
        $this->assertFalse($feature->isActive());
    }

    public function testGetLabel()
    {
        $feature = new ManageableFeature($this->defaultData);
        $this->assertEquals('label', $feature->getLabel());
    }

    public function testGetDescription()
    {
        $feature = new ManageableFeature($this->defaultData);
        $this->assertEquals('feature desc', $feature->getDescription());
    }

    public function testGetId()
    {
        $feature = new ManageableFeature($this->defaultData);
        $this->assertEquals('feature id', $feature->getId());
    }

    /**
     * @dataProvider testConstructExceptionProvider
     * @expectedException \common_exception_InconsistentData
     */
    public function testConstructException($data)
    {
        new ManageableFeature($data);
    }

    public function testToPhpCode()
    {
        $testPluginMock = $this->getMockBuilder(TestPlugin::class)
            ->setMethods(['getId', 'isActive'])
            ->disableOriginalConstructor()
            ->getMock();
        $testPluginMock->expects($this->once())
            ->method('getId')
            ->willReturn('PLUGIN_ID');
        $testPluginMock->expects($this->once())
            ->method('isActive')
            ->willReturn(true);

        $testPluginServiceMock = $this->getMockBuilder(TestPluginService::class)
            ->setMethods(['getAllPlugins'])
            ->disableOriginalConstructor()
            ->getMock();
        $testPluginServiceMock->expects($this->once())
            ->method('getAllPlugins')
            ->willReturn([$testPluginMock]);
        $loggerMock = $this->prophesize(LoggerService::class)->reveal();

        $serviceLocatorMock = $this->getServiceLocatorMock([
            TestPluginService::SERVICE_ID => $testPluginServiceMock,
            LoggerService::SERVICE_ID => $loggerMock
        ]);

        $feature = new ManageableFeature($this->defaultData);
        $feature->setServiceLocator($serviceLocatorMock);
        $code = $feature->__toPhpCode();
        eval('$unserializedFeature ='.$code.';');
        $unserializedFeature->setServiceLocator($serviceLocatorMock);
        $this->assertEquals($feature, $unserializedFeature);
    }

    public function testConstructExceptionProvider()
    {
        return [
            [[
                ManageableFeature::OPTION_ACTIVE => true,
                ManageableFeature::OPTION_DESCRIPTION => 'feature desc',
                ManageableFeature::OPTION_ENABLED_BY_DEFAULT => true,
                ManageableFeature::OPTION_PLUGIN_IDS => ['foo', 'bar'],
                ManageableFeature::OPTION_LABEL => 'label',
            ]],
            [[
                ManageableFeature::OPTION_ID => 'feature id',
                ManageableFeature::OPTION_DESCRIPTION => 'feature desc',
                ManageableFeature::OPTION_ENABLED_BY_DEFAULT => true,
                ManageableFeature::OPTION_PLUGIN_IDS => ['foo', 'bar'],
                ManageableFeature::OPTION_LABEL => 'label',
            ]],
            [[
                ManageableFeature::OPTION_ID => 'feature id',
                ManageableFeature::OPTION_ACTIVE => true,
                ManageableFeature::OPTION_ENABLED_BY_DEFAULT => true,
                ManageableFeature::OPTION_PLUGIN_IDS => ['foo', 'bar'],
                ManageableFeature::OPTION_LABEL => 'label',
            ]],
            [[
                ManageableFeature::OPTION_ID => 'feature id',
                ManageableFeature::OPTION_ACTIVE => true,
                ManageableFeature::OPTION_DESCRIPTION => 'feature desc',
                ManageableFeature::OPTION_PLUGIN_IDS => ['foo', 'bar'],
                ManageableFeature::OPTION_LABEL => 'label',
            ]],
            [[
                ManageableFeature::OPTION_ID => 'feature id',
                ManageableFeature::OPTION_ACTIVE => true,
                ManageableFeature::OPTION_DESCRIPTION => 'feature desc',
                ManageableFeature::OPTION_ENABLED_BY_DEFAULT => true,
                ManageableFeature::OPTION_LABEL => 'label',
            ]],
            [[
                ManageableFeature::OPTION_ID => 'feature id',
                ManageableFeature::OPTION_ACTIVE => true,
                ManageableFeature::OPTION_DESCRIPTION => 'feature desc',
                ManageableFeature::OPTION_ENABLED_BY_DEFAULT => true,
                ManageableFeature::OPTION_PLUGIN_IDS => ['foo', 'bar'],
            ]],
        ];
    }
}
