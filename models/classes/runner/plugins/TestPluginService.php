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
 * Copyright (c) 2016-2017 (original work) Open Assessment Technologies SA;
 */
namespace oat\taoTests\models\runner\plugins;

use oat\tao\model\plugins\AbstractPluginService;
use oat\tao\model\plugins\PluginModule;

/**
 * Manage test plugins
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
class TestPluginService extends AbstractPluginService
{
    const SERVICE_ID = 'taoTests/TestPlugin';

    /**
     * @deprecated
     */
    const CONFIG_ID = self::SERVICE_ID;

    /**
     * TestPluginService constructor.
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setRegistry(PluginRegistry::getRegistry());
    }

    /**
     * Creates a plugin object from data array
     * @param $data
     * @return PluginModule
     */
    protected function createFromArray($data)
    {
        return TestPlugin::fromArray($data);
    }
}
