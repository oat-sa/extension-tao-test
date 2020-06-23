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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

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
    /**
     * Puts an increase to the map
     * @param string $sourceId
     * @param string $type
     * @param int $seconds
     * @return TimerAdjustmentMapInterface
     */
    public function increase(string $sourceId, string $type, int $seconds): TimerAdjustmentMapInterface;

    /**
     * Puts an decrease to the map
     * @param string $sourceId
     * @param string $type
     * @param int $seconds
     * @return TimerAdjustmentMapInterface
     */
    public function decrease(string $sourceId, string $type, int $seconds): TimerAdjustmentMapInterface;

    /**
     * Gets the calculated total adjustments of all types stored for provided source ID in seconds.
     * @param string $sourceId
     * @return int
     */
    public function get(string $sourceId): int;

    /**
     * Gets the calculated adjustments for provided source ID and adjustment type in seconds.
     *
     * @param string $sourceId
     * @param string $type
     * @return int
     */
    public function getByType(string $sourceId, string $type): int;
}
