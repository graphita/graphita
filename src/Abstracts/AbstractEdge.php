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
     * @var float|int
     */
    private float|int $weight = 1;

    /**
     * @var array
     */
    private array $vertices = array();

    use AttributesHandlerTrait;

    /**
     * @param Vertex $a
     * @param Vertex $b
     * @param Graph $graph
     * @param array $attributes
     */
    public function __construct(Vertex &$a, Vertex &$b, Graph &$graph, array $attributes = array())
    {
        $this->id = uniqid($a->getId() . '-' . $b->getId() . '-');
        $this->vertices[$a->getId()] = $a;
        $this->vertices[$b->getId()] = $b;
        $this->graph = $graph;
        $this->setAttributes($attributes);
    }

    /**
     * @return string
     */
    function __toString()
    {
        return 'Edge Id:' . $this->getId() . PHP_EOL .
            'Information:' . json_encode($this->getAttributes()) . PHP_EOL .
            'Vertices:' . json_encode($this->getVertices());
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

    /**
     * @return array
     */
    public function getVertices(): array
    {
        return $this->vertices;
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
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param float|int $weight
     * @return AbstractEdge
     */
    public function setWeight(float|int $weight): static
    {
        $this->weight = $weight;
        return $this;
    }
}