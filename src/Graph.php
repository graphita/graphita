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
     * @return bool
     */
    public function removeVertex($id): bool
    {
        if (!$this->hasVertex($id))
            return false;
        unset($this->vertices[$id]);
        return true;
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
     * @return array
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @param $sourceId
     * @param $destinationId
     * @param array $attributes
     * @return UndirectedEdge
     * @throws Exception
     */
    public function createUndirectedEdge($sourceId, $destinationId, array $attributes = array()): UndirectedEdge
    {
        if (!$this->hasVertex($sourceId) || !$this->hasVertex($destinationId))
            throw new Exception('Vertex not exist !');
        $sourceVertex = $this->getVertex($sourceId);
        $destinationVertex = $this->getVertex($destinationId);
        $edge = new UndirectedEdge($sourceVertex, $destinationVertex, $this, $attributes);
        $sourceVertex->addEdge($edge);
        $destinationVertex->addEdge($edge);
        $this->edges[$edge->getId()] = $edge;
        return $edge;
    }

    /**
     * @param $sourceId
     * @param $destinationId
     * @param array $attributes
     * @return DirectedEdge
     * @throws Exception
     */
    public function createDirectedEdge($sourceId, $destinationId, array $attributes = array()): DirectedEdge
    {
        if (!$this->hasVertex($sourceId) || !$this->hasVertex($destinationId))
            throw new Exception('Vertex not exist !');
        $sourceVertex = $this->getVertex($sourceId);
        $destinationVertex = $this->getVertex($destinationId);
        $edge = new DirectedEdge($sourceVertex, $destinationVertex, $this, $attributes);
        $sourceVertex->addEdge($edge);
        $destinationVertex->addEdge($edge);
        $this->edges[$edge->getId()] = $edge;
        return $edge;
    }
}