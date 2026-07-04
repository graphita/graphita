<?php

namespace Graphita\Graphita;

use Graphita\Graphita\Abstracts\AbstractEdge;

class UndirectedEdge extends AbstractEdge
{
    private string $nodeAId;
    private string $nodeBId;

    /**
     * Construct a new Undirected Edge.
     *
     * @param string $id
     * @param string $nodeAId
     * @param string $nodeBId
     * @param array $attributes
     */
    public function __construct(string $id, string $nodeAId, string $nodeBId, array $attributes = [])
    {
        $this->id = $id;
        $this->nodeAId = $nodeAId;
        $this->nodeBId = $nodeBId;
        $this->setAttributes($attributes);
    }

    /**
     * Get the endpoint IDs of this edge.
     *
     * @return array<string>
     */
    public function getEndpointIds(): array
    {
        return [$this->nodeAId, $this->nodeBId];
    }

    /**
     * Check if a specific vertex ID is part of this edge.
     *
     * @param string $id
     * @return bool
     */
    public function hasVertexId(string $id): bool
    {
        return $this->nodeAId === $id || $this->nodeBId === $id;
    }
}