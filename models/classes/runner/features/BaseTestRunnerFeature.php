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

/**
 * Class BaseTestRunnerFeature
 * @package oat\taoTests\models\runner\features
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class BaseTestRunnerFeature extends TestRunnerFeature
{
    /**
     * BaseTestRunnerFeature constructor.
     * @param string $id
     * @param array $pluginIds
     * @param boolean $isEnabledByDefault
     */
    public function __construct($id, $pluginIds, $isEnabledByDefault)
    {
        $this->id = $id;
        $this->pluginsIds = $pluginIds;
        $this->isEnabledByDefault = $isEnabledByDefault;
    }

    /**
     * User-friendly localized label for the feature
     * @return string
     */
    public function getLabel()
    {
        return $this->getId();
    }

    /**
     * User-friendly localized description for the feature
     * @return mixed
     */
    public function getDescription()
    {
        return $this->getId();
    }

    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\PhpSerializable::__toPhpCode()
     */
    public function __toPhpCode()
    {
        return 'new '.get_class($this) . '(' . PHP_EOL .
            '    ' . \common_Utils::toPHPVariableString($this->getId()) . ',' . PHP_EOL .
            '    ' . \common_Utils::toPHPVariableString($this->getPluginsIds()) . ',' . PHP_EOL .
            '    ' . \common_Utils::toPHPVariableString($this->isEnabledByDefault())  . PHP_EOL
        . ')';
    }
}
