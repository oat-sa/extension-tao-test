<?php declare(strict_types=1);
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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoTests\models\runner\time;


/**
 * Interface TimerAdjustmentMapInterface
 *
 * Describes an API for timer adjustment storage and access.
 *
 * @package oat\taoTests\models\classes\runner\time
 */
interface TimerAdjustmentMapInterface
{
    public const ACTION_INCREASE = 'increase';
    public const ACTION_DECREASE = 'decrease';

    /**
     * Puts an entry to the map
     * @param string $sourceId
     * @param string $action
     * @param int $seconds
     * @return TimerAdjustmentMapInterface
     */
    public function put(string $sourceId, string $action, int $seconds): TimerAdjustmentMapInterface;

    /**
     * Gets the calculated adjustment in seconds
     * @param string $sourceId
     * @return int
     */
    public function get(string $sourceId): int;

    /**
     * Removes an entry specified by $sourceId
     * @param string $sourceId
     * @return TimerAdjustmentMapInterface
     */
    public function remove(string $sourceId): TimerAdjustmentMapInterface;

    /**
     * Clears the map of all entries
     * @return TimerAdjustmentMapInterface
     */
    public function clear(): TimerAdjustmentMapInterface;
}