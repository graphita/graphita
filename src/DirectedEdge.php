<?php

namespace Graphita\Graphita;

use Graphita\Graphita\Abstracts\AbstractEdge;

class DirectedEdge extends AbstractEdge
{
    /**
     * @param Vertex $source
     * @param Vertex $destination
     * @param Graph $graph
     * @param array $attributes
     */
    public function __construct(Vertex &$source, Vertex &$destination, Graph &$graph, array $attributes = array())
    {
        parent::__construct($source, $destination, $graph, $attributes);
    }

    /**
     * @return Vertex
     */
    public function getSource(): Vertex
    {
        $vertices = $this->getVertices();
        $verticesId = array_keys($vertices);

        return $vertices[$verticesId[0]];
    }

    /**
     * @return Vertex
     */
    public function getDestination(): Vertex
    {
        $vertices = $this->getVertices();
        $verticesId = array_keys($vertices);

        return $vertices[$verticesId[1]];
    }
}