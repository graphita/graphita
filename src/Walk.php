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
     * @var float|int
     */
    private float|int $totalWeight = 0;

    const REPEAT_VERTICES = true;

    const REPEAT_EDGES = true;

    const IS_LOOP = false;

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
            'Vertices:' . implode(',', array_map(function ($vertex) {
                return $vertex->getId() . ':' . json_encode($vertex->getAttributes());
            }, $this->getVertices())) . PHP_EOL .
            'Edges:' . implode(',', array_map(function ($edge) {
                return $edge->getId() . ':' . json_encode($edge->getAttributes());
            }, $this->getEdges())) . PHP_EOL .
            'Total Weight:' . $this->getTotalWeight();
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
        return static::REPEAT_VERTICES;
    }

    /**
     * @return bool
     */
    public function canRepeatEdges(): bool
    {
        return static::REPEAT_EDGES;
    }

    /**
     * @return bool
     */
    public function isLoop(): bool
    {
        return static::IS_LOOP;
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
    public function calculateTotalWeight(): void
    {
        $this->totalWeight = array_reduce($this->getEdges(), function ($totalWeight, AbstractEdge $edge) {
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
        foreach ($vertices as $vertexKey => $vertex) {
            if (!$vertex instanceof Vertex) {
                throw new InvalidArgumentException('Vertices must be array of Vertex !');
            }
            $this->pushVertex($vertex);

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

                $outgoingEdge = $outgoingEdges[array_key_first($outgoingEdges)];
                $this->pushEdge($outgoingEdge);
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
     * @param \Graphita\Graphita\Vertex $nextVertex
     * @return void
     * @throws Exception
     */
    public function addVertex(Vertex $nextVertex): void
    {
        $this->addVertices([$nextVertex]);
    }

    /**
     * @param \Graphita\Graphita\Vertex|array $nextVertex
     * @return void
     */
    private function pushVertex(Vertex|array $nextVertex): void
    {
        if (is_array($nextVertex)) {
            foreach ($nextVertex as $vertex) {
                $this->pushVertex($vertex);
            }
        } else {
            $verticesIds = array_map(function (Vertex $vertex) {
                return $vertex->getId();
            }, $this->getVertices());

            if ($nextVertex->getGraph() !== $this->getGraph()) {
                throw new InvalidArgumentException('Vertices must be in a same Graph !');
            }
            if (
                !$this->canRepeatVertices() &&
                in_array($nextVertex->getId(), $verticesIds) &&
                !(
                    $this->isLoop() &&
                    count(array_keys($verticesIds, $nextVertex->getId())) == 1 &&
                    $verticesIds[0] == $nextVertex->getId()
                )
            ) {
                throw new InvalidArgumentException('Vertices must be unique !');
            }
            $this->vertices[] = $nextVertex;
        }
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
        foreach ($edges as $edgeKey => $edge) {
            if (!$edge instanceof AbstractEdge) {
                throw new InvalidArgumentException('Edges must be array of AbstractEdge !');
            }
            $this->pushEdge($edge);

            $prevEdge = $edges[$edgeKey - 1] ?? false;
            if ($prevEdge) {
                $outgoingVertices = array_diff($edge->getVertices(), $prevEdge->getVertices());
                if (count($outgoingVertices) > 1) {
                    throw new InvalidArgumentException('Invalid steps ! There is no common Vertex between Edge ' . $prevEdge->getId() . ' and ' . $edge->getId() . ' !');
                } else if (count($outgoingVertices) == 0) {
                    $outgoingVertices = array_diff($edge->getVertices(), [$this->vertices[array_key_last($this->vertices)]]);
                }
                $this->pushVertex($outgoingVertices[array_key_last($outgoingVertices)]);
            } else {
                $vertices = $edge->getVertices();
                $nextEdge = $edges[$edgeKey + 1] ?? false;
                if ($nextEdge) {
                    $this->pushVertex(array_diff($vertices, $nextEdge->getVertices()));
                    $this->pushVertex(array_intersect($vertices, $nextEdge->getVertices()));
                } else {
                    $this->pushVertex($vertices[array_key_first($vertices)]);
                    $this->pushVertex($vertices[array_key_last($vertices)]);
                }
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

    /**
     * @param AbstractEdge|array $nextEdge
     * @return void
     */
    private function pushEdge(AbstractEdge|array $nextEdge): void
    {
        if (is_array($nextEdge)) {
            foreach ($nextEdge as $edge) {
                $this->pushEdge($edge);
            }
        } else {
            $edgesIds = array_map(function (AbstractEdge $edge) {
                return $edge->getId();
            }, $this->getEdges());

            if ($nextEdge->getGraph() !== $this->getGraph()) {
                throw new InvalidArgumentException('Edges must be in a same Graph !');
            }
            if (
                !$this->canRepeatEdges() &&
                in_array($nextEdge->getId(), $edgesIds)
            ) {
                throw new InvalidArgumentException('Edges must be unique !');
            }
            $this->edges[] = $nextEdge;
        }
    }
}