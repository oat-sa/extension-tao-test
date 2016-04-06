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

namespace oat\taoTests\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTests\models\runner\time\TimePoint;

class TimerPointTest extends TaoPhpUnitTestRunner
{
    /**
     * tests initialization
     */
    public function setUp()
    {
        parent::setUp();
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     * @dataProvider testSerializeProvider
     * @param $tags
     * @param $timestamp
     * @param $type
     * @param $target
     */
    public function testSerialize($tags, $timestamp, $type, $target)
    {
        $timePoint = new TimePoint($tags, $timestamp, $type, $target);
        $timePointUnserialized = new TimePoint();
        $data = $timePoint->serialize();
        $timePointUnserialized->unserialize($data);
        $this->assertEquals($timePoint->getTimestamp(), $timePointUnserialized->getTimestamp());
        $this->assertEquals($timePoint->getTarget(), $timePointUnserialized->getTarget());
        $this->assertEquals($timePoint->getTags(), $timePointUnserialized->getTags());
        $this->assertEquals($timePoint->getType(), $timePointUnserialized->getType());
    }

    /**
     * @dataProvider testSerializeProvider
     * @param $tags
     * @param $timestamp
     * @param $type
     * @param $target
     */
    public function testUnserialize($tags, $timestamp, $type, $target)
    {
        $timePoint = new TimePoint($tags, $timestamp, $type, $target);
        $timePointUnserialized = new TimePoint();
        $data = $timePoint->serialize();
        $timePointUnserialized->unserialize($data);
        $this->assertEquals($timePointUnserialized->getTimestamp(), $timePoint->getTimestamp());
        $this->assertEquals($timePointUnserialized->getTarget(), $timePoint->getTarget());
        $this->assertEquals($timePointUnserialized->getTags(), $timePoint->getTags());
        $this->assertEquals($timePointUnserialized->getType(), $timePoint->getType());
    }

    /**
     * Test TimePoint::addTag() method
     */
    public function testAddTag()
    {
        $timePoint = new TimePoint();
        $timePoint->addTag('tag1');
        $this->assertEquals($timePoint->getTags(), ['tag1']);

        $timePoint->addTag('tag2');
        $this->assertEquals($timePoint->getTags(), ['tag1', 'tag2']);
    }

    /**
     * Test TimePoint::removeTag() method
     */
    public function testRemoveTag()
    {
        $timePoint = new TimePoint();
        $timePoint->setTags(['tag1', 'tag2', 'tag3']);
        $this->assertEquals($timePoint->getTags(), ['tag1', 'tag2', 'tag3']);

        $timePoint->removeTag('tag2');
        $this->assertEquals($timePoint->getTags(), ['tag1', 'tag3']);

        $timePoint->removeTag('tag1');
        $this->assertEquals($timePoint->getTags(), ['tag3']);

        $timePoint->removeTag('tag3');
        $this->assertEquals($timePoint->getTags(), []);
    }

    /**
     * Test TimePoint::setTags() method
     */
    public function testSetTags()
    {
        $timePoint = new TimePoint();
        $this->assertEquals($timePoint->getTags(), []);

        $timePoint->setTags(['tag1', 'tag2', 'tag3']);
        $this->assertEquals($timePoint->getTags(), ['tag1', 'tag2', 'tag3']);

        $timePoint->setTags(['tag4', 'tag5', 'tag6']);
        $this->assertEquals($timePoint->getTags(), ['tag4', 'tag5', 'tag6']);

        $timePoint->setTags('stringTag');
        $this->assertEquals($timePoint->getTags(), ['stringTag']);
    }

    /**
     * Test TimePoint::getRef() method
     */
    public function testGetRef()
    {
        $timePoint = new TimePoint(['a', 'b', 'c']);
        $timePoint2 = new TimePoint(['c', 'b', 'a']);
        $timePoint3 = new TimePoint(['a', 'b', 'x']);
        $this->assertEquals($timePoint->getRef(), $timePoint2->getRef());
        $this->assertNotEquals($timePoint->getRef(), $timePoint3->getRef());
    }

    /**
     * Test TimePoint::testGetTag() method
     */
    public function testGetTag()
    {
        $timePoint = new TimePoint(['a', 'b', 'c']);
        $this->assertEquals('a', $timePoint->getTag(0));
        $this->assertEquals('b', $timePoint->getTag(1));
        $this->assertEquals('c', $timePoint->getTag(2));
    }

    /**
     * Test TimePoint::match() method
     */
    public function testMatch()
    {
        $timePoint = new TimePoint(['a', 'b', 'c'], null, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT);

        $this->assertTrue($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_ALL, TimePoint::TARGET_ALL));
        $this->assertTrue($timePoint->match(['b', 'c'], TimePoint::TYPE_ALL, TimePoint::TARGET_ALL));
        $this->assertTrue($timePoint->match(null, TimePoint::TYPE_ALL, TimePoint::TARGET_ALL));
        $this->assertTrue($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_START, TimePoint::TARGET_CLIENT));
        $this->assertTrue($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_START));
        $this->assertTrue($timePoint->match(['a', 'b', 'c']));

        $this->assertFalse($timePoint->match(['b', 'c', 'd'], TimePoint::TYPE_ALL, TimePoint::TARGET_ALL));
        $this->assertFalse($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_END, TimePoint::TARGET_ALL));
        $this->assertFalse($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_START, TimePoint::TARGET_SERVER));
        $this->assertFalse($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_END, TimePoint::TARGET_SERVER));
    }

    /**
     * Test TimePoint::compare() method
     * @dataProvider testCompareProvider
     */
    public function testCompare(TimePoint $firstPoint, TimePoint $secondPoint, $expectedResult)
    {
        $this->assertEquals($expectedResult, $firstPoint->compare($secondPoint));
    }

    /**
     * @return array
     */
    public function testSerializeProvider()
    {
        return [
            [
                'abc',
                1459335349,
                TimePoint::TYPE_START,
                TimePoint::TARGET_SERVER,
            ],
            [
                ['a', 'b', 'c'],
                1459335572,
                TimePoint::TYPE_START,
                TimePoint::TARGET_SERVER,
            ],
            [
                null,
                null,
                null,
                null,
            ],
        ];
    }

    /**
     * @return array
     */
    public function testCompareProvider()
    {
        return [
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335311, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                -110000,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335289, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                110000,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335300, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
                -1,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                1,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                1,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                -1,
            ],
        ];
    }
}