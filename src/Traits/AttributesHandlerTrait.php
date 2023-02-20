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
     * @return mixed|null
     */
    public function getAttribute($key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * @param $key
     * @param $value
     * @return AttributesHandlerTrait
     */
    public function setAttribute($key, $value): static
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * @param array $attributes
     * @return AttributesHandlerTrait
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes + $this->attributes;
        return $this;
    }

    /**
     * @param $key
     * @return AttributesHandlerTrait
     */
    public function removeAttribute($key): static
    {
        unset($this->attributes[$key]);
        return $this;
    }
}