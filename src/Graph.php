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
     * @return string
     */
    public function __toString()
    {
        return 'Graph Information:' . json_encode($this->getAttributes()) . PHP_EOL .
            'Vertices:' . implode(',', array_map(function ($vertex) {
                return $vertex->getId() . ':' . json_encode($vertex->getAttributes());
            }, $this->getVertices())) . PHP_EOL .
            'Edges:' . implode(',', array_map(function ($edge) {
                return $edge->getId() . ':' . json_encode($edge->getAttributes());
            }, $this->getEdges()));
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
        array_map(function ($edge) {
            $this->removeEdge($edge->getId());
        }, $this->vertices[$id]->getEdges());
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
     * @param Vertex $sourceVertex
     * @param Vertex $destinationVertex
     * @param array $attributes
     * @return UndirectedEdge
     * @throws Exception
     */
    public function createUndirectedEdge(
        Vertex $sourceVertex,
        Vertex $destinationVertex,
        array  $attributes = array()
    ): UndirectedEdge
    {
        if ($sourceVertex->getGraph() !== $this || $destinationVertex->getGraph() !== $this)
            throw new Exception('Vertex must be in graph !');
        $edge = new UndirectedEdge($sourceVertex, $destinationVertex, $this, $attributes);
        $sourceVertex->addEdge($edge);
        $destinationVertex->addEdge($edge);
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
        array_map(function (Vertex $vertex) use ($id) {
            if ($vertex->hasEdge($id)) {
                $vertex->removeEdge($id);
            }
        }, $vertices);
        unset($this->edges[$id]);
        return true;
    }
}