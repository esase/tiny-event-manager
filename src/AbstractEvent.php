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

abstract class AbstractEvent
{

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected array $params = [];

    /**
     * @var bool
     */
    protected bool $isStopped = false;

    /**
     * AbstractEvent constructor.
     *
     * @param  null   $data
     * @param  array  $params
     */
    public function __construct(
        $data = null,
        array $params = []
    ) {
        $this->data = $data;
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    abstract public function setData($data): self;

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param  array  $params
     *
     * @return $this
     */
    abstract  public function setParams(array $params): self;

    /**
     * @param  bool  $isStopped
     *
     * @return $this
     */
    public function setStopped(bool $isStopped): self
    {
        $this->isStopped = $isStopped;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStopped(): bool
    {
        return $this->isStopped;
    }

}
