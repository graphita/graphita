<?php

namespace Graphita\Graphita;

use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Traits\AttributesHandlerTrait;
use LogicException;
use OutOfBoundsException;

class Graph
{
    private array $vertices = [];
    private array $edges = [];
    private int $edgeIdCounter = 0;

    use AttributesHandlerTrait;

    /**
     * Construct a new Graph.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * Get all vertices in the graph.
     *
     * @return array<Vertex>
     */
    public function getVertices(): array
    {
        return $this->vertices;
    }

    /**
     * Get a specific vertex by its ID.
     *
     * @param string $id
     * @return Vertex
     * @throws OutOfBoundsException
     */
    public function getVertex(string $id): Vertex
    {
        if (!$this->hasVertex($id)) {
            throw new OutOfBoundsException("Vertex [{$id}] does not exist in the graph.");
        }

        return $this->vertices[$id];
    }

    /**
     * Check if a vertex exists in the graph.
     *
     * @param string $id
     * @return bool
     */
    public function hasVertex(string $id): bool
    {
        return isset($this->vertices[$id]);
    }

    /**
     * Get all edges in the graph.
     *
     * @return array<AbstractEdge>
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * Get a specific edge by its ID.
     *
     * @param string $id
     * @return AbstractEdge
     * @throws OutOfBoundsException
     */
    public function getEdge(string $id): AbstractEdge
    {
        if (!$this->hasEdge($id)) {
            throw new OutOfBoundsException("Edge [{$id}] does not exist in the graph.");
        }

        return $this->edges[$id];
    }

    /**
     * Check if an edge exists in the graph.
     *
     * @param string $id
     * @return bool
     */
    public function hasEdge(string $id): bool
    {
        return isset($this->edges[$id]);
    }

    /**
     * Count the total number of vertices.
     *
     * @return int
     */
    public function countVertices(): int
    {
        return count($this->vertices);
    }

    /**
     * Count the total number of edges.
     *
     * @return int
     */
    public function countEdges(): int
    {
        return count($this->edges);
    }

    /**
     * Create and add a new vertex to the graph.
     *
     * @param string $id
     * @param array $attributes
     * @return Vertex
     * @throws LogicException
     */
    public function createVertex(string $id, array $attributes = []): Vertex
    {
        if ($this->hasVertex($id)) {
            throw new LogicException("Vertex [{$id}] already exists!");
        }

        $vertex = new Vertex($id, $attributes);
        $this->vertices[$id] = $vertex;

        return $vertex;
    }

    /**
     * Create a directed edge between two vertices.
     *
     * @param string $sourceId
     * @param string $destinationId
     * @param array $attributes
     * @return DirectedEdge
     * @throws OutOfBoundsException
     */
    public function createDirectedEdge(string $sourceId, string $destinationId, array $attributes = []): DirectedEdge
    {
        if (!$this->hasVertex($sourceId) || !$this->hasVertex($destinationId)) {
            throw new OutOfBoundsException('Source and Destination Vertices must exist in the graph.');
        }

        $edgeId = 'e_' . (++$this->edgeIdCounter);
        $edge = new DirectedEdge($edgeId, $sourceId, $destinationId, $attributes);

        $this->edges[$edgeId] = $edge;
        $this->vertices[$sourceId]->addOutgoingEdgeId($edgeId);
        $this->vertices[$destinationId]->addIncomingEdgeId($edgeId);

        return $edge;
    }

    /**
     * Create an undirected edge between two vertices.
     *
     * @param string $nodeAId
     * @param string $nodeBId
     * @param array $attributes
     * @return UndirectedEdge
     * @throws OutOfBoundsException
     */
    public function createUndirectedEdge(string $nodeAId, string $nodeBId, array $attributes = []): UndirectedEdge
    {
        if (!$this->hasVertex($nodeAId) || !$this->hasVertex($nodeBId)) {
            throw new OutOfBoundsException('Both Vertices must exist in the graph.');
        }

        $edgeId = 'e_' . (++$this->edgeIdCounter);
        $edge = new UndirectedEdge($edgeId, $nodeAId, $nodeBId, $attributes);

        $this->edges[$edgeId] = $edge;
        $this->vertices[$nodeAId]->addUndirectedEdgeId($edgeId);
        $this->vertices[$nodeBId]->addUndirectedEdgeId($edgeId);

        return $edge;
    }

    /**
     * Remove an edge from the graph.
     *
     * @param string $id
     * @return bool
     */
    public function removeEdge(string $id): bool
    {
        if (!$this->hasEdge($id)) {
            return false;
        }

        $edge = $this->edges[$id];

        foreach ($edge->getEndpointIds() as $vertexId) {
            if (isset($this->vertices[$vertexId])) {
                $this->vertices[$vertexId]->removeEdgeId($id);
            }
        }

        unset($this->edges[$id]);
        return true;
    }

    /**
     * Remove a vertex and all its associated edges from the graph.
     *
     * @param string $id
     * @return bool
     */
    public function removeVertex(string $id): bool
    {
        if (!$this->hasVertex($id)) {
            return false;
        }

        $vertex = $this->vertices[$id];

        foreach ($vertex->getAllEdgeIds() as $edgeId) {
            $this->removeEdge($edgeId);
        }

        unset($this->vertices[$id]);
        return true;
    }

    /**
     * Get all incoming edges from a specific source vertex.
     *
     * @param string $vertexId
     * @param string $sourceVertexId
     * @return array<AbstractEdge>
     * @throws OutOfBoundsException
     */
    public function getIncomingEdgesFrom(string $vertexId, string $sourceVertexId): array
    {
        $vertex = $this->getVertex($vertexId);
        $this->getVertex($sourceVertexId);
        $edges = [];

        foreach ($vertex->getIncomingEdgeIds() as $edgeId) {
            $edge = $this->getEdge($edgeId);
            if ($edge instanceof DirectedEdge && $edge->getSourceId() === $sourceVertexId) {
                $edges[$edgeId] = $edge;
            }
        }

        foreach ($vertex->getUndirectedEdgeIds() as $edgeId) {
            $edge = $this->getEdge($edgeId);
            if ($edge instanceof UndirectedEdge && $edge->hasVertexId($sourceVertexId)) {
                $edges[$edgeId] = $edge;
            }
        }

        return $edges;
    }

    /**
     * Get all outgoing edges to a specific destination vertex.
     *
     * @param string $vertexId
     * @param string $destinationVertexId
     * @return array<AbstractEdge>
     * @throws OutOfBoundsException
     */
    public function getOutgoingEdgesTo(string $vertexId, string $destinationVertexId): array
    {
        $vertex = $this->getVertex($vertexId);
        $this->getVertex($destinationVertexId);
        $edges = [];

        foreach ($vertex->getOutgoingEdgeIds() as $edgeId) {
            $edge = $this->getEdge($edgeId);
            if ($edge instanceof DirectedEdge && $edge->getDestinationId() === $destinationVertexId) {
                $edges[$edgeId] = $edge;
            }
        }

        foreach ($vertex->getUndirectedEdgeIds() as $edgeId) {
            $edge = $this->getEdge($edgeId);
            if ($edge instanceof UndirectedEdge && $edge->hasVertexId($destinationVertexId)) {
                $edges[$edgeId] = $edge;
            }
        }

        return $edges;
    }

    /**
     * Get all neighboring vertices for a specific vertex ID.
     *
     * @param string $vertexId
     * @return array<Vertex>
     * @throws OutOfBoundsException
     */
    public function getNeighbors(string $vertexId): array
    {
        $vertex = $this->getVertex($vertexId);
        $neighbors = [];

        foreach ($vertex->getAllEdgeIds() as $edgeId) {
            $edge = $this->getEdge($edgeId);
            foreach ($edge->getEndpointIds() as $endpointId) {
                if ($endpointId !== $vertexId) {
                    $neighbors[$endpointId] = $this->getVertex($endpointId);
                }
            }
        }

        return $neighbors;
    }

    /**
     * Get all incoming neighboring vertices.
     *
     * @param string $vertexId
     * @return array<Vertex>
     * @throws OutOfBoundsException
     */
    public function getIncomingNeighbors(string $vertexId): array
    {
        $vertex = $this->getVertex($vertexId);
        $neighbors = [];

        foreach ($vertex->getIncomingEdgeIds() as $edgeId) {
            $edge = $this->getEdge($edgeId);
            if ($edge instanceof DirectedEdge) {
                $sourceId = $edge->getSourceId();
                $neighbors[$sourceId] = $this->getVertex($sourceId);
            }
        }

        foreach ($vertex->getUndirectedEdgeIds() as $edgeId) {
            $edge = $this->getEdge($edgeId);
            foreach ($edge->getEndpointIds() as $endpointId) {
                if ($endpointId !== $vertexId) {
                    $neighbors[$endpointId] = $this->getVertex($endpointId);
                }
            }
        }

        return $neighbors;
    }

    /**
     * Get all outgoing neighboring vertices.
     *
     * @param string $vertexId
     * @return array<Vertex>
     * @throws OutOfBoundsException
     */
    public function getOutgoingNeighbors(string $vertexId): array
    {
        $vertex = $this->getVertex($vertexId);
        $neighbors = [];

        foreach ($vertex->getOutgoingEdgeIds() as $edgeId) {
            $edge = $this->getEdge($edgeId);
            if ($edge instanceof DirectedEdge) {
                $destId = $edge->getDestinationId();
                $neighbors[$destId] = $this->getVertex($destId);
            }
        }

        foreach ($vertex->getUndirectedEdgeIds() as $edgeId) {
            $edge = $this->getEdge($edgeId);
            foreach ($edge->getEndpointIds() as $endpointId) {
                if ($endpointId !== $vertexId) {
                    $neighbors[$endpointId] = $this->getVertex($endpointId);
                }
            }
        }

        return $neighbors;
    }

    /**
     * Check if two vertices are neighbors.
     *
     * @param string $vertexId
     * @param string $neighborId
     * @return bool
     * @throws OutOfBoundsException
     */
    public function hasNeighbor(string $vertexId, string $neighborId): bool
    {
        $neighbors = $this->getNeighbors($vertexId);
        return isset($neighbors[$neighborId]);
    }

    /**
     * Check if a specific vertex is an incoming neighbor.
     *
     * @param string $vertexId
     * @param string $neighborId
     * @return bool
     * @throws OutOfBoundsException
     */
    public function hasIncomingNeighbor(string $vertexId, string $neighborId): bool
    {
        $neighbors = $this->getIncomingNeighbors($vertexId);
        return isset($neighbors[$neighborId]);
    }

    /**
     * Check if a specific vertex is an outgoing neighbor.
     *
     * @param string $vertexId
     * @param string $neighborId
     * @return bool
     * @throws OutOfBoundsException
     */
    public function hasOutgoingNeighbor(string $vertexId, string $neighborId): bool
    {
        $neighbors = $this->getOutgoingNeighbors($vertexId);
        return isset($neighbors[$neighborId]);
    }

    /**
     * Count the total number of unique neighbors.
     *
     * @param string $vertexId
     * @return int
     * @throws OutOfBoundsException
     */
    public function countNeighbors(string $vertexId): int
    {
        return count($this->getNeighbors($vertexId));
    }

    /**
     * Count the total number of unique incoming neighbors.
     *
     * @param string $vertexId
     * @return int
     * @throws OutOfBoundsException
     */
    public function countIncomingNeighbors(string $vertexId): int
    {
        return count($this->getIncomingNeighbors($vertexId));
    }

    /**
     * Count the total number of unique outgoing neighbors.
     *
     * @param string $vertexId
     * @return int
     * @throws OutOfBoundsException
     */
    public function countOutgoingNeighbors(string $vertexId): int
    {
        return count($this->getOutgoingNeighbors($vertexId));
    }
}