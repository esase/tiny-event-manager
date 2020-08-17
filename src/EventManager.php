<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\EventManager;

use Psr\Container\ContainerInterface;

class EventManager
{

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var array
     */
    private array $subscribers = [];

    /**
     * EventManager constructor.
     *
     * @param  ContainerInterface  $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param  string  $eventName
     *
     * @return bool
     */
    public function isEventHasSubscribers(string $eventName): bool
    {
        return isset($this->subscribers[$eventName]);
    }

    /**
     * @param  string  $eventName
     * @param  string  $listenerClassName
     * @param  int     $priority
     */
    public function subscribe(
        string $eventName,
        string $listenerClassName,
        int $priority = 100
    ) {
        if (!isset($this->subscribers[$eventName])) {
            $this->subscribers[$eventName] = [];
        }

        // whenever we subscribe a new listener we have to reset the sorted flag
        $this->subscribers[$eventName]['sorted'] = false;
        $this->subscribers[$eventName]['subscribers'][$priority][]
            = $listenerClassName;
    }

    /**
     * @param  string  $eventName
     * @param  string  $listenerClassName
     */
    public function unsubscribe(
        string $eventName,
        string $listenerClassName
    ) {
        $subscribers = $this->subscribers[$eventName]['subscribers'] ?? [];

        if ($subscribers) {
            foreach ($subscribers as $priority => $listeners) {
                $listeners = $this->removeListener(
                    $listeners,
                    $listenerClassName
                );

                // delete empty listeners
                if (!count($listeners)) {
                    unset($this->subscribers[$eventName]['subscribers'][$priority]);

                    continue;
                }

                // update the list of listeners
                $this->subscribers[$eventName]['subscribers'][$priority]
                    = $listeners;
            }

            // delete the empty event
            if (!count($this->subscribers[$eventName]['subscribers'])) {
                unset($this->subscribers[$eventName]);
            }
        }
    }

    /**
     * @param  string         $eventName
     * @param  AbstractEvent  $event
     */
    public function trigger(
        string $eventName,
        AbstractEvent $event
    ) {
        if (!empty($this->subscribers[$eventName])) {
            // sort subscribers
            if (!$this->subscribers[$eventName]['sorted']) {
                ksort($this->subscribers[$eventName]['subscribers']);
                $this->subscribers[$eventName]['sorted'] = true;
            }

            // notify listeners
            foreach ($this->subscribers[$eventName]['subscribers'] as $listeners)
            {
                foreach ($listeners as $listener) {
                    $listenerObject = $this->container->get($listener);
                    $listenerObject($event); // call the "__invoke" method
                }
            }
        }
    }

    /**
     * @param  array   $listeners
     * @param  string  $listenerClassName
     *
     * @return array
     */
    private function removeListener(
        array $listeners,
        string $listenerClassName
    ): array {
        return array_values(
            array_filter(
                $listeners, function ($className) use ($listenerClassName) {
                return $className !== $listenerClassName;
            }
            )
        );
    }

}
