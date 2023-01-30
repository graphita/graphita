<?php

namespace Graphita\Graphita;

use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Traits\AttributesHandlerTrait;

class Vertex
{
    /**
     * @var mixed
     */
    private mixed $id;
    /**
     * @var array
     */
    private array $edges = array();

    /**
     * @var Graph
     */
    private Graph $graph;

    use AttributesHandlerTrait;

    /**
     * @param $id
     * @param Graph $graph
     * @param array $attributes
     */
    public function __construct($id, Graph &$graph, array $attributes = array())
    {
        $this->id = $id;
        $this->graph = $graph;
        $this->setAttributes($attributes);
    }

    /**
     * @return mixed|null
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @return Graph
     */
    public function getGraph(): Graph
    {
        return $this->graph;
    }

    /**
     * @return array
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @param AbstractEdge $edge
     * @return void
     * @throws \Exception
     */
    public function addEdge(AbstractEdge &$edge): void
    {
        if ($edge->getGraph() !== $this->getGraph())
            throw new \Exception('Edge & Vertex have to be within the same graph');
        $this->edges[$edge->getId()] = $edge;
    }
}