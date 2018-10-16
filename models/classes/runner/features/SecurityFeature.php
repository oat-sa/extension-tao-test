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

namespace oat\taoTests\models\runner\features;

use oat\taoTests\models\runner\plugins\TestPluginService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class SecurityFeature
 * @package oat\taoTests\models\runner\features
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 * @deprecated
 */
class SecurityFeature extends TestRunnerFeature implements ServiceLocatorAwareInterface
{

    const FEATURE_ID = 'security';

    use ServiceLocatorAwareTrait;

    public function __construct()
    {
        $this->id = self::FEATURE_ID;
        $this->isEnabledByDefault = true;
    }

    /**
     * @return string[]
     */
    public function getPluginsIds()
    {
        if ($this->pluginsIds === null) {
            $testPluginService = $this->getServiceLocator()->get(TestPluginService::SERVICE_ID);
            $this->pluginsIds = [];
            foreach ($testPluginService->getAllPlugins() as $plugin) {
                if ($plugin->getCategory() === 'security') {
                    $this->pluginsIds[] = $plugin->getId();
                }
            }
        }
        return $this->pluginsIds;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return __('Security plugins');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return __('Set of plugins with \'security\' category');
    }
}
