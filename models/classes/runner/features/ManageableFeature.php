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
use oat\taoTests\models\runner\plugins\TestPlugin;

/**
 * Class ManageableFeature
 *
 * NOTE: Feature configuration stored in the config file and changing of it's configuration using interface requires
 *       synchronization of configs in case of multi server configuration.
 *
 * @package oat\taoTests\models\runner\features
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class ManageableFeature extends TestRunnerFeature
{

    /** @var string */
    protected $label;
    /** @var string */
    protected $description;

    const OPTION_ID = 'identifier';
    const OPTION_DESCRIPTION = 'description';
    const OPTION_ACTIVE = 'active';
    const OPTION_LABEL = 'label';
    const OPTION_ENABLED_BY_DEFAULT = 'enabledByDefault';
    const OPTION_PLUGIN_IDS = 'pluginIds';

    /**
     * ManageableFeature constructor.
     * @param array $options
     * @throws \common_exception_InconsistentData
     */
    public function __construct(array $options)
    {
        $missedOptions = array_diff_key([
            self::OPTION_ID => self::OPTION_ID,
            self::OPTION_DESCRIPTION => self::OPTION_DESCRIPTION,
            self::OPTION_LABEL => self::OPTION_LABEL,
            self::OPTION_ACTIVE => self::OPTION_ACTIVE,
            self::OPTION_ENABLED_BY_DEFAULT => self::OPTION_ENABLED_BY_DEFAULT,
            self::OPTION_PLUGIN_IDS => self::OPTION_PLUGIN_IDS,
        ], $options);
        if (!empty($missedOptions)) {
            throw new \common_exception_InconsistentData('Required options missed in ' . static::class . ': '
                . implode(',', array_keys($missedOptions)));
        }
        $this->id = $options[self::OPTION_ID];
        $this->label = $options[self::OPTION_LABEL];
        $this->description = $options[self::OPTION_DESCRIPTION];
        $this->active = $options[self::OPTION_ACTIVE];
        $this->isEnabledByDefault = $options[self::OPTION_ENABLED_BY_DEFAULT];
        $this->pluginsIds = $options[self::OPTION_PLUGIN_IDS];
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return TestPlugin[]
     */
    protected function getAllPlugins()
    {
        $pluginService = $this->getServiceLocator()->get(TestPluginService::SERVICE_ID);
        return $pluginService->getAllPlugins();
    }

    /**
     * @return string
     * @throws \common_exception_Error
     */
    public function __toPhpCode()
    {
        return 'new ' . get_class($this) . '([' . PHP_EOL
            . '    \'' . self::OPTION_ID . '\'=>' . \common_Utils::toPHPVariableString($this->getId()) . ',' . PHP_EOL
            . '    \'' . self::OPTION_DESCRIPTION . '\'=>__(' . \common_Utils::toPHPVariableString($this->getDescription()) . '),' . PHP_EOL
            . '    \'' . self::OPTION_LABEL . '\'=>__(' . \common_Utils::toPHPVariableString($this->getLabel()) . '),' . PHP_EOL
            . '    \'' . self::OPTION_ACTIVE . '\'=>' . \common_Utils::toPHPVariableString($this->isActive()) . ',' . PHP_EOL
            . '    \'' . self::OPTION_ENABLED_BY_DEFAULT . '\'=>' . \common_Utils::toPHPVariableString($this->isEnabledByDefault()) . ',' . PHP_EOL
            . '    \'' . self::OPTION_PLUGIN_IDS . '\'=>' . \common_Utils::toPHPVariableString($this->getPluginsIds()) . ',' . PHP_EOL
        . '])';
    }
}
