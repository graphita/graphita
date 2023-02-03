<?php

namespace Graphita\Graphita\Abstracts;

use Graphita\Graphita\Graph;
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
     * @param array $vertices
     * @throws Exception
     */
    public function __construct(Graph &$graph, array $vertices)
    {
        $this->graph = $graph;
        $this->addSteps($vertices);
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
     * @param Vertex $vertex
     * @return void
     * @throws Exception
     */
    public function addStep(Vertex $vertex): void
    {
        $this->addSteps([$vertex]);
    }

    /**
     * @param array $vertices
     * @return void
     * @throws Exception
     */
    public function addSteps(array $vertices): void
    {
        $this->checkSteps($vertices);
        foreach ($vertices as $vertex) {
            $this->vertices[] = $vertex;
            $nextVertex = next($vertices);
            if ($nextVertex) {
                $nextEdge = $vertex->getOutgoingEdges()[$nextVertex->getId()];
                $this->edges[] = $nextEdge;
            }
        }
    }

    /**
     * @param array $vertices
     * @throws Exception
     */
    public function checkSteps(array $vertices): void
    {
        $vertices = array_merge($this->vertices, $vertices);
        $verticesIds = [];
        $edgesIds = [];
        foreach ($vertices as $vertex) {
            if ($vertex->getGraph() !== $this->getGraph())
                throw new Exception('Vertices must be in a same Graph !');
            if (
                !$this->canRepeatVertices() &&
                array_key_exists($vertex->getId(), $verticesIds)
            ) {
                throw new Exception('Vertices must be unique !');
            }
            $verticesIds[] = $vertex->getId();
            $nextVertex = next($vertices);
            if ($nextVertex) {
                if ($vertex->hasOutgoingNeighbors($nextVertex->getId()))
                    throw new Exception('Vertices must be neighbors !');
                $nextEdge = $vertex->getOutgoingEdges()[$nextVertex->getId()];
                if (
                    !$this->canRepeatEdges() &&
                    array_key_exists($nextEdge->getId(), $edgesIds)
                ) {
                    throw new Exception('Edges must be unique !');
                }
                $edgesIds[] = $nextEdge->getId();
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