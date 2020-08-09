<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\EventManager;

class EventCollection extends AbstractEvent
{

    /**
     * EventCollection constructor.
     *
     * @param  null   $id
     * @param  null   $data
     * @param  array  $params
     */
    public function __construct(
        $id = null,
        $data = null,
        array $params = []
    ) {
        parent::__construct(
            $id,
            $data,
            $params
        );

        if (!is_array($this->data)) {
            $this->data = [];
        }

        if (!is_array($this->id)) {
            $this->id = [];
        }
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id): AbstractEvent
    {
        $this->id[] = $id;

        return $this;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    function setData($data): AbstractEvent
    {
        $this->data[] = $data;

        return $this;
    }

    /**
     * @param  array  $params
     *
     * @return $this
     */
    public function setParams(array $params): AbstractEvent
    {
        $this->params[] = $params;

        return $this;
    }

}
