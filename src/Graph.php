<?php

namespace Graphita\Graphita;

use Exception;
use Graphita\Graphita\Traits\AttributesHandlerTrait;

class Graph
{
    /**
     * @var array
     */
    private array $vertices = array();


    /**
     * @var array
     */
    private array $edges = array();


    use AttributesHandlerTrait;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->setAttributes($attributes);
    }

    /**
     * @return array
     */
    public function getVertices(): array
    {
        return $this->vertices;
    }

    /**
     * @param $id
     * @return Vertex|null
     */
    public function getVertex($id): ?Vertex
    {
        return $this->vertices[$id] ?? null;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasVertex($id): bool
    {
        return array_key_exists($id, $this->vertices);
    }

    /**
     * @return int
     */
    public function countVertices(): int
    {
        return count($this->vertices);
    }

    /**
     * @param $id
     * @param array $attributes
     * @return Vertex
     * @throws Exception
     */
    public function createVertex($id, array $attributes = array()): Vertex
    {
        if ($this->hasVertex($id))
            throw new Exception('Vertex exist !');

        $vertex = new Vertex($id, $this, $attributes);
        $this->vertices[$vertex->getId()] = $vertex;

        return $vertex;
    }

    /**
     * @param $id
     * @return bool
     */
    public function removeVertex($id): bool
    {
        if (!$this->hasVertex($id))
            return false;

        $edges = $this->vertices[$id]->getEdges();
        array_walk($edges, function ($edge) {
            $this->removeEdge($edge->getId());
        });

        unset($this->vertices[$id]);
        return true;
    }

    /**
     * @return array
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasEdge($id): bool
    {
        return array_key_exists($id, $this->edges);
    }

    /**
     * @return int
     */
    public function countEdges(): int
    {
        return count($this->edges);
    }

    /**
     * @param Vertex $vertexA
     * @param Vertex $vertexB
     * @param array $attributes
     * @return UndirectedEdge
     * @throws Exception
     */
    public function createUndirectedEdge(
        Vertex $vertexA,
        Vertex $vertexB,
        array  $attributes = array()
    ): UndirectedEdge
    {
        if ($vertexA->getGraph() !== $this || $vertexB->getGraph() !== $this)
            throw new Exception('Vertex must be in graph !');

        $edge = new UndirectedEdge($vertexA, $vertexB, $this, $attributes);
        $vertexA->addEdge($edge);
        $vertexB->addEdge($edge);
        $this->edges[$edge->getId()] = $edge;

        return $edge;
    }

    /**
     * @param Vertex $sourceVertex
     * @param Vertex $destinationVertex
     * @param array $attributes
     * @return DirectedEdge
     * @throws Exception
     */
    public function createDirectedEdge(
        Vertex $sourceVertex,
        Vertex $destinationVertex,
        array  $attributes = array()
    ): DirectedEdge
    {
        if ($sourceVertex->getGraph() !== $this || $destinationVertex->getGraph() !== $this)
            throw new Exception('Vertex must be in graph !');

        $edge = new DirectedEdge($sourceVertex, $destinationVertex, $this, $attributes);
        $sourceVertex->addEdge($edge);
        $destinationVertex->addEdge($edge);
        $this->edges[$edge->getId()] = $edge;

        return $edge;
    }

    /**
     * @param $id
     * @return bool
     */
    public function removeEdge($id): bool
    {
        if (!$this->hasEdge($id))
            return false;

        $vertices = $this->edges[$id]->getVertices();
        array_walk($vertices, function (Vertex $vertex) use ($id) {
            if ($vertex->hasEdge($id)) {
                $vertex->removeEdge($id);
            }
        });
        unset($this->edges[$id]);

        return true;
    }
}