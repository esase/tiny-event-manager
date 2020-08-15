<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TinyTest\EventManager;

use PHPUnit\Framework\TestCase;
use Tiny\EventManager\EventCollection;

class EventCollectionTest extends TestCase
{

    public function testSetters()
    {
        // create an initial event
        $event = new EventCollection();

        $event->setData('value 1')
            ->setParams(['test1' => 'test1']);

        $event->setData('value 2')
            ->setParams(['test2' => 'test2']);

        // we expect to see instead of initial data the changed ones
        $this->assertEquals($event->getData(), [
            'value 1',
            'value 2'
        ]);
        $this->assertEquals($event->getParams(), [
            ['test1' => 'test1'],
            ['test2' => 'test2']
        ]);
    }

}
