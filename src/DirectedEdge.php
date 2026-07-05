<?php

namespace Graphita\Graphita;

use Graphita\Graphita\Abstracts\AbstractEdge;

class DirectedEdge extends AbstractEdge
{
    private string $sourceId;
    private string $destinationId;

    /**
     * Construct a new Directed Edge.
     *
     * @param string $id
     * @param string $sourceId
     * @param string $destinationId
     * @param array $attributes
     */
    public function __construct(string $id, string $sourceId, string $destinationId, array $attributes = [])
    {
        $this->id = $id;
        $this->sourceId = $sourceId;
        $this->destinationId = $destinationId;
        $this->setAttributes($attributes);
    }

    /**
     * Get the ID of the source vertex.
     *
     * @return string
     */
    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    /**
     * Get the ID of the destination vertex.
     *
     * @return string
     */
    public function getDestinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * Get the endpoint IDs of this edge.
     *
     * @return array<string>
     */
    public function getEndpointIds(): array
    {
        return [$this->sourceId, $this->destinationId];
    }

    /**
     * Check if a specific vertex ID is part of this edge.
     *
     * @param string $id
     * @return bool
     */
    public function hasVertexId(string $id): bool
    {
        return $this->sourceId === $id || $this->destinationId === $id;
    }
}