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

class Event extends AbstractEvent
{

    /**
     * @param $data
     *
     * @return $this
     */
    function setData($data): AbstractEvent
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param  array  $params
     *
     * @return $this
     */
    public function setParams(array $params): AbstractEvent
    {
        $this->params = $params;

        return $this;
    }

}
