<?php

namespace Graphita\Graphita;

use Graphita\Graphita\Traits\AttributesHandlerTrait;

class Vertex
{
    private string $id;
    private array $incomingEdgeIds = [];
    private array $outgoingEdgeIds = [];
    private array $undirectedEdgeIds = [];

    use AttributesHandlerTrait;

    /**
     * Construct a new Vertex.
     *
     * @param string $id
     * @param array $attributes
     */
    public function __construct(string $id, array $attributes = [])
    {
        $this->id = $id;
        $this->setAttributes($attributes);
    }

    /**
     * Get the unique identifier of the vertex.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Add an incoming edge ID.
     *
     * @param string $edgeId
     * @return void
     */
    public function addIncomingEdgeId(string $edgeId): void
    {
        $this->incomingEdgeIds[$edgeId] = $edgeId;
    }

    /**
     * Add an outgoing edge ID.
     *
     * @param string $edgeId
     * @return void
     */
    public function addOutgoingEdgeId(string $edgeId): void
    {
        $this->outgoingEdgeIds[$edgeId] = $edgeId;
    }

    /**
     * Add an undirected edge ID.
     *
     * @param string $edgeId
     * @return void
     */
    public function addUndirectedEdgeId(string $edgeId): void
    {
        $this->undirectedEdgeIds[$edgeId] = $edgeId;
    }

    /**
     * Remove an edge ID from all internal lists.
     *
     * @param string $edgeId
     * @return void
     */
    public function removeEdgeId(string $edgeId): void
    {
        unset(
            $this->incomingEdgeIds[$edgeId],
            $this->outgoingEdgeIds[$edgeId],
            $this->undirectedEdgeIds[$edgeId]
        );
    }

    /**
     * Get all incoming edge IDs.
     *
     * @return array<string>
     */
    public function getIncomingEdgeIds(): array
    {
        return array_values($this->incomingEdgeIds);
    }

    /**
     * Get all outgoing edge IDs.
     *
     * @return array<string>
     */
    public function getOutgoingEdgeIds(): array
    {
        return array_values($this->outgoingEdgeIds);
    }

    /**
     * Get all undirected edge IDs.
     *
     * @return array<string>
     */
    public function getUndirectedEdgeIds(): array
    {
        return array_values($this->undirectedEdgeIds);
    }

    /**
     * Get all connected edge IDs.
     *
     * @return array<string>
     */
    public function getAllEdgeIds(): array
    {
        return array_values(array_merge(
            $this->incomingEdgeIds,
            $this->outgoingEdgeIds,
            $this->undirectedEdgeIds
        ));
    }

    /**
     * Check if the vertex has a specific edge ID.
     *
     * @param string $edgeId
     * @return bool
     */
    public function hasEdgeId(string $edgeId): bool
    {
        return isset($this->incomingEdgeIds[$edgeId])
            || isset($this->outgoingEdgeIds[$edgeId])
            || isset($this->undirectedEdgeIds[$edgeId]);
    }

    /**
     * Count the total number of connected edges.
     *
     * @return int
     */
    public function countEdges(): int
    {
        return count($this->incomingEdgeIds) + count($this->outgoingEdgeIds) + count($this->undirectedEdgeIds);
    }
}