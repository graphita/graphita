<?php

namespace Graphita\Graphita\Abstracts;

use Graphita\Graphita\DirectedEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\UndirectedEdge;
use Graphita\Graphita\Vertex;
use Exception;

abstract class AbstractWalk
{
    /**
     * @var Graph
     */
    private Graph $graph;

    /**
     * @var array
     */
    private array $steps = array();

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
     * @param Graph $graph
     * @param array $steps
     * @throws Exception
     */
    public function __construct(Graph &$graph, array $steps)
    {
        $this->graph = $graph;
        $this->addSteps($steps);
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
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * @return int
     */
    public function countSteps(): int
    {
        return count($this->steps);
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
     * @param Vertex|AbstractEdge $step
     * @return void
     * @throws Exception
     */
    public function addStep(Vertex|AbstractEdge $step): void
    {
        $this->addSteps([$step]);
    }

    /**
     * @param array $steps
     * @return void
     * @throws Exception
     */
    public function addSteps(array $steps): void
    {
        $this->checkSteps($steps);
        foreach ($steps as $step) {
            $this->steps[] = $step;
            if ($step instanceof Vertex) {
                $this->vertices[] = $step;
            } else if ($step instanceof AbstractEdge) {
                $this->edges[] = $step;
            }
        }
    }

    /**
     * @param array $steps
     * @return void
     * @throws Exception
     */
    public function checkSteps(array $steps): void
    {
        $steps = array_merge($this->steps, $steps);
        $vertices = array_filter($steps, function ($step, $stepIndex) {
            return $stepIndex % 2 == 0;
        }, ARRAY_FILTER_USE_BOTH);
        $edges = array_filter($steps, function ($step, $stepIndex) {
            return $stepIndex % 2 == 1;
        }, ARRAY_FILTER_USE_BOTH);

        $verticesIds = [];
        foreach ($vertices as $vertex) {
            if (!$vertex instanceof Vertex) {
                throw new Exception('Invalid steps !');
            }
            if ($vertex->getGraph() !== $this->getGraph()) {
                throw new Exception('Vertices must be in a same Graph !');
            }
            if (
                !$this->canRepeatVertices() &&
                array_key_exists($vertex->getId(), $verticesIds)
            ) {
                throw new Exception('Vertices must be unique !');
            }
            $verticesIds[] = $vertex->getId();
        }

        $edgesIds = [];
        foreach ($edges as $edge) {
            if (!$edge instanceof AbstractEdge) {
                throw new Exception('Invalid steps !');
            }
            if ($edge->getGraph() !== $this->getGraph()) {
                throw new Exception('Edges must be in a same Graph !');
            }
            if (
                !$this->canRepeatEdges() &&
                array_key_exists($edge->getId(), $edgesIds)
            ) {
                throw new Exception('Edges must be unique !');
            }
            $edgesIds[] = $edge->getId();
        }

        if (count($vertices) != count($edges) + 1) {
            throw new Exception('Invalid steps !');
        }

        foreach ($steps as $step) {
            if ($step instanceof Vertex) {
                $nextEdge = next($steps);
                if ($nextEdge) {
                    if (
                        !$step->hasOutgoingEdges($nextEdge->getId()) ||
                        ($nextEdge instanceof UndirectedEdge && !$nextEdge->hasVertex($step->getId())) ||
                        ($nextEdge instanceof DirectedEdge && $nextEdge->getSource() !== $step)
                    ) {
                        throw new Exception('Invalid steps !');
                    }
                }
            } else if ($step instanceof AbstractEdge) {
                $nextVertex = next($steps);
                if(
                    !$nextVertex->hasIncomingEdge($step->getId()) ||
                    ($step instanceof UndirectedEdge && !$step->hasVertex($nextVertex->getId())) ||
                    ($step instanceof DirectedEdge && $step->getDestination() !== $nextVertex)
                ){
                    throw new Exception('Invalid steps !');
                }
            }
        }

        if (
            $this->isLoop() &&
            $vertices[array_key_first($vertices)] !== $vertices[array_key_last($vertices)]
        ) {
            throw new Exception('First Vertex & Last Vertex must be same in Loop !');
        }
    }
}