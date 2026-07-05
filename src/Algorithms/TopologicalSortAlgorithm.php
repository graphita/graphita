<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Graph;
use LogicException;

/**
 * Resolves dependencies by sorting a Directed Acyclic Graph (DAG) linearly.
 */
class TopologicalSortAlgorithm
{
    /**
     * @var Graph
     */
    private Graph $graph;

    /**
     * @var array<string>
     */
    private array $results = [];

    /**
     * Initialize the algorithm with a Graph instance.
     *
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * Get the associated Graph instance.
     *
     * @return Graph
     */
    public function getGraph(): Graph
    {
        return $this->graph;
    }

    /**
     * Get the sorted array of Vertex IDs.
     *
     * @return array<string>
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Execute Kahn's Algorithm to calculate the topological sort order of the graph.
     *
     * @return self
     * @throws LogicException
     */
    public function calculate(): self
    {
        $inDegrees = [];
        $queue = [];
        $sorted = [];

        $vertices = $this->graph->getVertices();

        if (empty($vertices)) {
            $this->results = [];
            return $this;
        }

        foreach ($vertices as $id => $vertex) {
            $inDegrees[$id] = 0;
        }

        foreach ($vertices as $id => $vertex) {
            $neighbors = $this->graph->getOutgoingNeighbors($id);
            foreach ($neighbors as $neighborId => $neighborObj) {
                $inDegrees[(string) $neighborId]++;
            }
        }

        foreach ($inDegrees as $id => $degree) {
            if ($degree === 0) {
                $queue[] = $id;
            }
        }

        while (!empty($queue)) {
            $currentId = array_shift($queue);
            $sorted[] = $currentId;

            $neighbors = $this->graph->getOutgoingNeighbors($currentId);
            foreach ($neighbors as $neighborId => $neighborObj) {
                $neighborId = (string) $neighborId;
                $inDegrees[$neighborId]--;

                if ($inDegrees[$neighborId] === 0) {
                    $queue[] = $neighborId;
                }
            }
        }

        if (count($sorted) !== count($vertices)) {
            throw new LogicException(
                'Graph contains a cycle! Topological sort requires a Directed Acyclic Graph (DAG). ' .
                'You cannot resolve dependencies if they depend on each other.'
            );
        }

        $this->results = $sorted;

        return $this;
    }
}