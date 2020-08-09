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
use Psr\Container\ContainerInterface;
use Tiny\EventManager\AbstractEvent;
use Tiny\EventManager\EventManager;
use ReflectionClass;
use ReflectionException;

class EventManagerTest extends TestCase
{

    /**
     * @throws ReflectionException
     */
    public function testSubscribeMethod()
    {
        $eventManager = new EventManager(
            $this->createMock(
                ContainerInterface::class
            )
        );

        $eventManager->subscribe(
            'test',
            'TestClass',
            200
        );

        $eventManager->subscribe(
            'test',
            'TestClass2',
            100
        );

        $eventManager->subscribe(
            'test',
            'TestClass3',
            200
        );

        $eventManager->subscribe(
            'test2',
            'TestClass4',
            -100
        );

        // check the built structure
        $reflection = new ReflectionClass($eventManager);
        $reflectionSubscribers = $reflection->getProperty('subscribers');
        $reflectionSubscribers->setAccessible(true);

        $this->assertEquals(
            [
                'test'  => [
                    'sorted'      => false,
                    'subscribers' => [
                        200 => ['TestClass', 'TestClass3'],
                        100 => ['TestClass2']
                    ]
                ],
                'test2' => [
                    'sorted'      => false,
                    'subscribers' => [
                        -100 => ['TestClass4']
                    ]
                ]
            ], $reflectionSubscribers->getValue($eventManager)
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testUnsubscribeMethod()
    {
        $eventManager = new EventManager(
            $this->createMock(
                ContainerInterface::class
            )
        );

        $eventManager->subscribe(
            'test',
            'TestClass',
            200
        );

        $eventManager->subscribe(
            'test',
            'TestClass2',
            100
        );

        $eventManager->subscribe(
            'test',
            'TestClass3',
            200
        );

        $eventManager->subscribe(
            'test2',
            'TestClass4',
            -100
        );

        $eventManager->unsubscribe('test', 'TestClass');
        $eventManager->unsubscribe('test', 'TestClass2');
        $eventManager->unsubscribe('test2', 'TestClass4');

        // check the built structure
        $reflection = new ReflectionClass($eventManager);
        $reflectionSubscribers = $reflection->getProperty('subscribers');
        $reflectionSubscribers->setAccessible(true);

        $this->assertEquals(
            [
                'test' => [
                    'sorted'      => false,
                    'subscribers' => [
                        200 => ['TestClass3']
                    ]
                ]
            ], $reflectionSubscribers->getValue($eventManager)
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testUnsubscribeMethodUsingAllDeletedListeners()
    {
        $eventManager = new EventManager(
            $this->createMock(
                ContainerInterface::class
            )
        );

        $eventManager->subscribe(
            'test',
            'TestClass',
            200
        );

        $eventManager->subscribe(
            'test2',
            'TestClass2',
            100
        );

        $eventManager->subscribe(
            'test3',
            'TestClass3',
            200
        );

        $eventManager->unsubscribe('test', 'TestClass');
        $eventManager->unsubscribe('test2', 'TestClass2');
        $eventManager->unsubscribe('test3', 'TestClass3');

        // check the built structure
        $reflection = new ReflectionClass($eventManager);
        $reflectionSubscribers = $reflection->getProperty('subscribers');
        $reflectionSubscribers->setAccessible(true);

        $this->assertEquals(
            [], $reflectionSubscribers->getValue($eventManager)
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testTriggerMethod()
    {
        // register an event
        $eventMock = $this->createMock(
            AbstractEvent::class
        );
        $eventMock->expects($this->exactly(3))
            ->method('getData');


        // register a container
        $container = $this->createMock(
            ContainerInterface::class
        );
        $container->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValue(
                new class { // return anonymous class object
                    public function __invoke(AbstractEvent $event)
                    {
                        $event->getData();
                    }

                }
            ));

        $eventManager = new EventManager($container);

        $eventManager->subscribe(
            'test',
            'TestClass',
            200
        );

        $eventManager->subscribe(
            'test',
            'TestClass2',
            100
        );

        $eventManager->subscribe(
            'test',
            'TestClass3',
            200
        );

        $eventManager->subscribe(
            'test2',
            'TestClass4',
            200
        );

        $eventManager->trigger('test', $eventMock);

        // check the built structure
        $reflection = new ReflectionClass($eventManager);
        $reflectionSubscribers = $reflection->getProperty('subscribers');
        $reflectionSubscribers->setAccessible(true);

        $this->assertEquals(
            [
                'test' => [
                    'sorted'      => true,
                    'subscribers' => [
                        100 => ['TestClass2'],
                        200 => ['TestClass', 'TestClass3']
                    ]
                ],
                'test2' => [
                    'sorted'      => false,
                    'subscribers' => [
                        200 => ['TestClass4']
                    ]
                ]
            ], $reflectionSubscribers->getValue($eventManager)
        );
    }

}
