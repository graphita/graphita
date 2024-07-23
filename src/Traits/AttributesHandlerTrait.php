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
     * @return self
     */
    public function setAttribute($key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * @param array $attributes
     * @return self
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes + $this->attributes;
        return $this;
    }

    /**
     * @param $key
     * @return self
     */
    public function removeAttribute($key): self
    {
        unset($this->attributes[$key]);
        return $this;
    }
}