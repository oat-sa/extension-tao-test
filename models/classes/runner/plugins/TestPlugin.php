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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\taoTests\models\runner\plugins;

use \JsonSerializable;

/**
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class TestPlugin implements JsonSerializable
{

    private $id;

    private $module;

    private $bundle;

    private $position;

    private $description = '';

    private $name = '';

    private $active = true;

    private $category;

    private $tags = [];



    public function __construct ($id, $module, $category, $data = [] )
    {
        $this->id          = (string)  $id;
        $this->module      = (string)  $module;
        $this->category    = (string)  $category;

        if(isset($data['bundle'])) {
            $this->bundle  = (string)  $data['bundle'];
        }
        if(isset($data['position'])) {
            $this->position  = $data['position'];
        }
        if(isset($data['description'])) {
            $this->description  = (string) $data['description'];
        }
        if(isset($data['name'])) {
            $this->name  = (string) $data['name'];
        }
        if(isset($data['active'])) {
            $this->active = (boolean) $data['active'];
        }
        if(isset($data['tags'])) {
            $this->tags = (boolean) $data['tags'];
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getBundle()
    {
        return $this->bundle;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = (boolean) $active;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function hasTag($tag)
    {
        return in_array($this->tags, $tag);
    }

    public function jsonSerialize()
    {
        return [
            'id'          => $this->id,
            'module'      => $this->module,
            'bundle'      => $this->bundle,
            'position'    => $this->position,
            'name'        => $this->name,
            'description' => $this->description,
            'category'    => $this->category,
            'active'      => $this->active,
            'tags'        => $this->tags
        ];
    }

    public static function fromArray( array $data ) 
    {
        return new self($data['id'], $data['module'], $data['category'], $data);
    }
}
