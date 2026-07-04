<?php

namespace Graphita\Graphita\Abstracts;

use Graphita\Graphita\Traits\AttributesHandlerTrait;

abstract class AbstractEdge
{
    protected string $id;
    protected float $weight = 1.0;

    use AttributesHandlerTrait;

    /**
     * Get the unique identifier of the edge.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the weight of the edge.
     *
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * Set the weight of the edge.
     *
     * @param float $weight
     * @return self
     */
    public function setWeight(float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get the IDs of the vertices this edge connects.
     *
     * @return array<string>
     */
    abstract public function getEndpointIds(): array;

    /**
     * Check if a specific vertex ID is part of this edge.
     *
     * @param string $id
     * @return bool
     */
    abstract public function hasVertexId(string $id): bool;
}