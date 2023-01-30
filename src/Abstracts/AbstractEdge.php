<?php

namespace Graphita\Graphita\Abstracts;

use Graphita\Graphita\Graph;
use Graphita\Graphita\Traits\AttributesHandlerTrait;
use Graphita\Graphita\Vertex;

abstract class AbstractEdge
{
    /**
     * @var string
     */
    private string $id;
    /**
     * @var Graph
     */
    private Graph $graph;

    /**
     * @var Vertex
     */
    private Vertex $source;

    /**
     * @var Vertex
     */
    private Vertex $destination;

    use AttributesHandlerTrait;

    /**
     * @param Vertex $source
     * @param Vertex $destination
     * @param Graph $graph
     * @param array $attributes
     */
    public function __construct(Vertex &$source, Vertex &$destination, Graph &$graph, array $attributes = array())
    {
        $this->id = $source->getId() . '-' . $destination->getId();
        $this->source = $source;
        $this->destination = $destination;
        $this->graph = $graph;
        $this->setAttributes($attributes);
    }

    /**
     * @return string
     */
    public function getId(): string
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
}