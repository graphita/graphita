<?php

namespace Graphita\Graphita\Traits;

trait AttributesHandlerTrait
{
    /**
     * @var array
     */
    private array $attributes = array();

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function getAttribute($key, $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function setAttribute($key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @param array $attributes
     * @return void
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes + $this->attributes;
    }

    /**
     * @param $key
     * @return void
     */
    public function removeAttribute($key): void
    {
        unset($this->attributes[$key]);
    }
}