<?php

namespace Graphita\Graphita;

class DirectedEdge extends Abstracts\AbstractEdge
{

    /**
     * @var Vertex
     */
    private Vertex $source;

    /**
     * @var Vertex
     */
    private Vertex $destination;

    public function __construct(Vertex &$source, Vertex &$destination, Graph &$graph, array $attributes = array())
    {
        parent::__construct($source, $destination, $graph, $attributes);
        $this->source = $source;
        $this->destination = $destination;
    }

    public function getSource(): Vertex
    {
        return $this->source;
    }

    public function getDestination(): Vertex
    {
        return $this->destination;
    }
}