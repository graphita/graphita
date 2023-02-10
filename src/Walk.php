<?php

namespace Graphita\Graphita;

use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\DirectedEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\UndirectedEdge;
use Graphita\Graphita\Vertex;
use Exception;
use InvalidArgumentException;

class Walk
{
    /**
     * @var Graph
     */
    private Graph $graph;

    /**
     * @var array
     */
    private array $vertices = array();

    /**
     * @var array
     */
    private array $edges = array();

    /**
     * @var bool
     */
    private bool $repeatVertices = true;

    /**
     * @var bool
     */
    private bool $repeatEdges = true;

    /**
     * @var bool
     */
    private bool $isLoop = false;

    /**
     * @var float|int
     */
    private float|int $totalWeight = 0;

    /**
     * @param Graph $graph
     * @throws Exception
     */
    public function __construct(Graph &$graph)
    {
        $this->graph = $graph;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return 'Graph Information:' . json_encode($this->graph->getAttributes()) . PHP_EOL .
            'Vertices:' . json_encode($this->getVertices()) . PHP_EOL .
            'Edges:' . json_encode($this->getEdges());
    }

    /**
     * @return Graph
     */
    public function getGraph(): Graph
    {
        return $this->graph;
    }

    /**
     * @return bool
     */
    public function canRepeatVertices(): bool
    {
        return $this->repeatVertices;
    }

    /**
     * @return bool
     */
    public function canRepeatEdges(): bool
    {
        return $this->repeatEdges;
    }

    /**
     * @return bool
     */
    public function isLoop(): bool
    {
        return $this->isLoop;
    }

    /**
     * @return float|int
     */
    public function getTotalWeight(): float|int
    {
        return $this->totalWeight;
    }

    /**
     * @return void
     */
    private function calculateTotalWeight(): void
    {
        $this->totalWeight = array_reduce($this->getEdges(), function( $totalWeight, AbstractEdge $edge ){
            $totalWeight += $edge->getWeight();
            return $totalWeight;
        }, 0);
    }

    /**
     * @return array
     */
    public function getVertices(): array
    {
        return $this->vertices;
    }

    /**
     * @return int
     */
    public function countVertices(): int
    {
        return count($this->vertices);
    }

    /**
     * @param array $vertices
     * @return void
     * @throws Exception
     */
    public function addVertices(array $vertices): void
    {
        $vertices = array_merge($this->getVertices(), $vertices);
        $this->vertices = [];
        $this->edges = [];
        $verticesIds = [];
        foreach ($vertices as $vertexKey => $vertex) {
            if (!$vertex instanceof Vertex) {
                throw new InvalidArgumentException('Vertices must be array of Vertex !');
            }
            if ($vertex->getGraph() !== $this->getGraph()) {
                throw new InvalidArgumentException('Vertices must be in a same Graph !');
            }
            if (
                !$this->canRepeatVertices() &&
                in_array($vertex->getId(), $verticesIds)
            ) {
                throw new InvalidArgumentException('Vertices must be unique !');
            }
            $verticesIds[] = $vertex->getId();

            $prevVertex = $vertices[$vertexKey - 1] ?? false;
            if ($prevVertex) {
                if (
                    !$prevVertex->hasOutgoingNeighbors($vertex->getId())
                ) {
                    throw new InvalidArgumentException('Invalid steps ! Vertex ' . $prevVertex->getId() . ' does not have Neighbor Vertex ' . $vertex->getId() . ' !');
                }
                $outgoingEdges = $prevVertex->getOutgoingEdgesTo($vertex);
                if (count($outgoingEdges) > 1) {
                    throw new InvalidArgumentException('Unknown steps ! There are more than one Edges from ' . $prevVertex->getId() . ' to ' . $vertex->getId() . ' !');
                }
                $this->edges[] = $outgoingEdges[array_key_first($outgoingEdges)];
            }
            $this->vertices[] = $vertex;
        }
        if (
            $this->isLoop() &&
            $this->vertices[array_key_first($this->vertices)] !== $this->vertices[array_key_last($this->vertices)]
        ) {
            throw new Exception('First Vertex & Last Vertex must be same in Loop !');
        }
        $this->calculateTotalWeight();
    }

    /**
     * @param \Graphita\Graphita\Vertex $nextVertex
     * @return void
     * @throws Exception
     */
    public function addVertex(Vertex $nextVertex): void
    {
        $this->addVertices([$nextVertex]);
    }

    /**
     * @return array
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @return int
     */
    public function countEdges(): int
    {
        return count($this->edges);
    }

    /**
     * @param array $edges
     * @return void
     * @throws Exception
     */
    public function addEdges(array $edges): void
    {
        $edges = array_merge($this->getEdges(), $edges);
        $this->vertices = [];
        $this->edges = [];
        $edgesIds = [];
        foreach ($edges as $edgeKey => $edge) {
            if (!$edge instanceof AbstractEdge) {
                throw new InvalidArgumentException('Edges must be array of AbstractEdge !');
            }
            if ($edge->getGraph() !== $this->getGraph()) {
                throw new InvalidArgumentException('Edges must be in a same Graph !');
            }
            if (
                !$this->canRepeatEdges() &&
                in_array($edge->getId(), $edgesIds)
            ) {
                throw new InvalidArgumentException('Edges must be unique !');
            }
            $edgesIds[] = $edge->getId();

            $prevEdge = $edges[$edgeKey - 1] ?? false;
            if ($prevEdge) {
                $outgoingVertices = array_diff($edge->getVertices(), $prevEdge->getVertices());
                if (count($outgoingVertices) > 1) {
                    throw new InvalidArgumentException('Invalid steps ! There is no common Vertex between Edge ' . $prevEdge->getId() . ' and ' . $edge->getId() . ' !');
                } else if (count($outgoingVertices) == 0) {
                    $outgoingVertices = [$this->vertices[count($this->vertices) - 2]];
                }
                $this->edges[] = $edge;
                $this->vertices[] = $outgoingVertices[array_key_last($outgoingVertices)];
            } else {
                $vertices = $edge->getVertices();
                $this->vertices[] = $vertices[array_key_first($vertices)];
                $this->edges[] = $edge;
                $this->vertices[] = $vertices[array_key_last($vertices)];
            }
        }
        if (
            $this->isLoop() &&
            $this->vertices[array_key_first($this->vertices)] !== $this->vertices[array_key_last($this->vertices)]
        ) {
            throw new Exception('First Vertex & Last Vertex must be same in Loop !');
        }
        $this->calculateTotalWeight();
    }

    /**
     * @param AbstractEdge $nextEdge
     * @return void
     * @throws Exception
     */
    public function addEdge(AbstractEdge $nextEdge): void
    {
        $this->addEdges([$nextEdge]);
    }
}