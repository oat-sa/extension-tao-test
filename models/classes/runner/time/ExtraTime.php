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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoTests\models\runner\time;

/**
 * Interface ExtraTime
 *
 * Describes the API needed to manage extra time added to a timer.
 * 
 * @package oat\taoTests\models\runner\time
 */
interface ExtraTime
{
    /**
     * Gets the added extra time
     * @return float
     */
    public function getExtraTime();
    
    /**
     * Sets the added extra time
     * @param float $time
     * @return ExtraTime
     */
    public function setExtraTime($time);

    /**
     * Gets the amount of already consumed extra time. If tags are provided, only take care of the related time. 
     * @param string|array $tags A tag or a list of tags to filter
     * @return float
     */
    public function getConsumedExtraTime($tags = null);
    
    /**
     * Gets the amount of remaining extra time
     * @return float
     */
    public function getRemainingExtraTime();
}
