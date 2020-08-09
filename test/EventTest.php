<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TinyTest\EventManager;

use PHPUnit\Framework\TestCase;
use Tiny\EventManager\Event;

class EventTest extends TestCase
{

    public function testSetters()
    {
        // create an initial event
        $event = new Event(1, 'initial value', []);

        // change the initial value
        $event->setId(2)
            ->setData('changed value')
            ->setParams(['test' => 'test'])
            ->setStopped(true);

        // we expect to see instead of initial data the changed ones
        $this->assertEquals($event->getId(), 2);
        $this->assertEquals($event->getData(), 'changed value');
        $this->assertEquals($event->getParams(), ['test' => 'test']);
        $this->assertTrue($event->isStopped());
    }

}
