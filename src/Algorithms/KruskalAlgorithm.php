<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Graph;
use LogicException;

/**
 * Calculates the Minimum Spanning Tree (MST) of the graph using Kruskal's Algorithm.
 * Returns a new Graph containing all vertices connected by the cheapest possible non-looping edges.
 */
class KruskalAlgorithm
{
    /**
     * @var Graph
     */
    private Graph $graph;

    /**
     * @var Graph|null
     */
    private ?Graph $resultGraph = null;

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
     * Get the resulting Minimum Spanning Tree as a new Graph object.
     *
     * @return Graph|null
     */
    public function getResultGraph(): ?Graph
    {
        return $this->resultGraph;
    }

    /**
     * Calculate the Minimum Spanning Tree.
     *
     * @return self
     * @throws LogicException
     */
    public function calculate(): self
    {
        $vertices = $this->graph->getVertices();
        $edges = $this->graph->getEdges();

        if (empty($vertices)) {
            $this->resultGraph = new Graph();
            return $this;
        }

        // 1. Sort all edges by weight ascending
        usort($edges, function($a, $b) {
            return $a->getWeight() <=> $b->getWeight();
        });

        // 2. Initialize the Union-Find (Disjoint Set) structures
        $parent = [];
        $rank = [];

        foreach ($vertices as $id => $vertex) {
            $parent[$id] = $id;
            $rank[$id] = 0;
        }

        $mstEdges = [];
        $edgesNeeded = count($vertices) - 1;

        // 3. Process edges to build the MST
        foreach ($edges as $edge) {
            if (count($mstEdges) >= $edgesNeeded) {
                break;
            }

            // Undirected and Directed edges are treated as structural links for MST purposes
            $endpoints = $edge->getEndpointIds();
            if (count($endpoints) !== 2) {
                continue; // Ignore self-loops safely
            }

            $u = $endpoints[0];
            $v = $endpoints[1];

            $rootU = $this->findRoot($u, $parent);
            $rootV = $this->findRoot($v, $parent);

            // If they don't share the same root, adding this edge won't create a cycle
            if ($rootU !== $rootV) {
                $mstEdges[] = $edge;
                $this->unionSets($rootU, $rootV, $parent, $rank);
            }
        }

        // 4. Construct the new MST Graph object
        $mstGraph = new Graph($this->graph->getAttributes());

        // Copy all vertices
        foreach ($vertices as $id => $vertex) {
            $mstGraph->createVertex($id, $vertex->getAttributes());
        }

        // Copy only the optimized edges
        foreach ($mstEdges as $edge) {
            $endpoints = $edge->getEndpointIds();
            $className = get_class($edge);

            // Re-create the edge exactly as it was, but in the new Graph
            if (strpos($className, 'DirectedEdge') !== false) {
                $newEdge = $mstGraph->createDirectedEdge($edge->getSourceId(), $edge->getDestinationId(), $edge->getAttributes());
            } else {
                $newEdge = $mstGraph->createUndirectedEdge($endpoints[0], $endpoints[1], $edge->getAttributes());
            }
            $newEdge->setWeight($edge->getWeight());
        }

        $this->resultGraph = $mstGraph;

        return $this;
    }

    /**
     * Find the root of the set in which element `i` belongs (with path compression).
     *
     * @param string $i
     * @param array $parent
     * @return string
     */
    private function findRoot(string $i, array &$parent): string
    {
        if ($parent[$i] === $i) {
            return $i;
        }

        $parent[$i] = $this->findRoot($parent[$i], $parent);
        return $parent[$i];
    }

    /**
     * Union two sets together based on rank.
     *
     * @param string $rootU
     * @param string $rootV
     * @param array $parent
     * @param array $rank
     * @return void
     */
    private function unionSets(string $rootU, string $rootV, array &$parent, array &$rank): void
    {
        if ($rank[$rootU] < $rank[$rootV]) {
            $parent[$rootU] = $rootV;
        } elseif ($rank[$rootU] > $rank[$rootV]) {
            $parent[$rootV] = $rootU;
        } else {
            $parent[$rootV] = $rootU;
            $rank[$rootU]++;
        }
    }
}