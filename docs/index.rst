.. _index-event-manager-label:

Event manager
=============

The simple realization of the **subject/observer** pattern which allows to attach and detach listeners to named events.
Generally speaking it a good way to communicate with all your components across the project and do not communicate with they directly.

Installation
------------

Run the following to install this library:


.. code-block:: bash

    $ composer require esase/tiny-event-manager


Simple event
------------

This kind of event is used to notify your subscribers about an action or pass a value through the chain of subscriber in order to modify it.

--------------
Simple example
--------------

.. code-block:: php

    <?php

        use Tiny\EventManager\Event;
        use Tiny\EventManager\EventManager;
        use Tiny\ServiceManager\ServiceManager;

        $event = new Event([
            'id' => 1,
            'user_name' => 'tester',
            'email' => 'tester@gmail.com'
        ]);

        $serviceManager = new EventManager(
            new ServiceManager()
        );

        // notify subscribers about the action
        $serviceManager->trigger('user.created.event', $event);

        ...

        // we expect to get either the initial data or modified one
        print_r($event->getData());


Collection event
----------------

The collection event is used to fetch some list of data from it's subscribers. List of configs e.g:

------------------
Collection example
------------------

.. code-block:: php

    <?php

        use Tiny\EventManager\Event;
        use Tiny\EventManager\EventManager;
        use Tiny\ServiceManager\ServiceManager;

        $event = new EventCollection();

        $serviceManager = new EventManager(
            new ServiceManager()
        );

        $serviceManager->trigger('core.config_list.event', $event);

        ...

        // we expect to get an array of configs (not a single value) received from subscribers
        print_r($event->getData());

Event manager
-------------

The event manager provides a few helpful methods for `registering`, `removing subscribers` and `trigger events` as well.

-------------------------------
Subscribe / unsubscribe example
-------------------------------

.. code-block:: php

    <?php

        use Tiny\EventManager\Event;
        use Tiny\EventManager\EventManager;
        use Tiny\ServiceManager\ServiceManager;

        $serviceManager = new EventManager(
            // you may pass any other "PSR"'s compatible container here
            new ServiceManager()
        );

        // subscribe to some events
        // we need to pass a listener class name  and priority (optional)
        $serviceManager->subscribe('user.created.event', TestUserCreatedEvent::class, 100);
        $serviceManager->subscribe('user.deleted.event', TestUserDeletedEvent::class, 100);

        // we don't want to listen the "user.deleted.event" event any more
        $serviceManager->unsubscribe('user.deleted.event', TestUserDeletedEvent::class);

The **priority** is used to manage the exact order of listeners execution, if you need you that your listener will be executed first you need to specify a lower priority than other listeners do

----------------
Complete example
----------------

.. code-block:: php

    <?php

        use Tiny\EventManager\Event;
        use Tiny\EventManager\EventManager;
        use Tiny\ServiceManager\ServiceManager;

        $serviceManager = new EventManager(
            new ServiceManager()
        );

        // we need to know when new users are created
        $serviceManager->subscribe('user.created.event', TestUserCreatedEvent::class);

        // notify subscribers about a new user
        $event = new Event([
            'id' => 1,
            'user_name' => 'tester',
            'email' => 'tester@gmail.com'
        ]);

        $serviceManager->trigger('user.created.event', $event);

        // the event's handler
        class TestUserCreatedEvent
        {
            public function  __invoke(Event $event)
            {
                // prints: [ 'id' => 1, 'user_name' => 'tester','email' => 'tester@gmail.com']
                print_r($event->getData());

                // we even may change the event's data
                $event->setData(array_merge(
                    $event->getData(),
                    [
                        'creation_time' => time() // add a new property
                    ]
                ));

                // we also my stop the full chain of the listeners (other listeners will not be invoked)
                $event->setStopped();

                // or do other logic ...

            }
        }

        // prints: [ 'id' => 1, 'user_name' => 'tester','email' => 'tester@gmail.com', 'creation_time' => '...']
        print_r($event->getData());

**PS:** In our case all event's handlers should be registered as services in the :ref:`ServiceManager <index-service-manager-label>` otherwise
they will not be invoked.