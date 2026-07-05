<?php

namespace Graphita\Graphita\Algorithms;

use Exception;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Path;
use LogicException;

/**
 * Calculates the shortest path in a graph that may contain negative edge weights.
 * Also detects negative weight cycles which invalidate shortest path math.
 */
class BellmanFordAlgorithm
{
    /**
     * @var Graph
     */
    private Graph $graph;

    /**
     * @var array<string>
     */
    private array $sources = [];

    /**
     * @var array<string>
     */
    private array $destinations = [];

    /**
     * @var array<Path>
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
     * Add a single source vertex ID to the search array.
     *
     * @param string $sourceId
     * @return self
     * @throws LogicException
     */
    public function addSource(string $sourceId): self
    {
        if (!$this->graph->hasVertex($sourceId)) {
            throw new LogicException("Source Vertex [{$sourceId}] must exist in Graph!");
        }
        $this->sources[] = $sourceId;
        return $this;
    }

    /**
     * Set a single source vertex ID, replacing any existing sources.
     *
     * @param string $sourceId
     * @return self
     */
    public function setSource(string $sourceId): self
    {
        $this->sources = [];
        return $this->addSource($sourceId);
    }

    /**
     * Add a single destination vertex ID to the search array.
     *
     * @param string $destinationId
     * @return self
     * @throws LogicException
     */
    public function addDestination(string $destinationId): self
    {
        if (!$this->graph->hasVertex($destinationId)) {
            throw new LogicException("Destination Vertex [{$destinationId}] must exist in Graph!");
        }
        $this->destinations[] = $destinationId;
        return $this;
    }

    /**
     * Set a single destination vertex ID, replacing any existing destinations.
     *
     * @param string $destinationId
     * @return self
     */
    public function setDestination(string $destinationId): self
    {
        $this->destinations = [];
        return $this->addDestination($destinationId);
    }

    /**
     * Set multiple source vertex IDs.
     *
     * @param array<string> $sourceIds
     * @return self
     */
    public function setSources(array $sourceIds): self
    {
        $this->sources = [];
        foreach ($sourceIds as $sourceId) {
            $this->addSource($sourceId);
        }
        return $this;
    }

    /**
     * Set multiple destination vertex IDs.
     *
     * @param array<string> $destinationIds
     * @return self
     */
    public function setDestinations(array $destinationIds): self
    {
        $this->destinations = [];
        foreach ($destinationIds as $destinationId) {
            $this->addDestination($destinationId);
        }
        return $this;
    }

    /**
     * Retrieve all successfully calculated Path objects.
     *
     * @return array<Path>
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Count the total number of Paths found.
     *
     * @return int
     */
    public function countResults(): int
    {
        return count($this->getResults());
    }

    /**
     * Retrieve the shortest Path.
     *
     * @return Path|null
     */
    public function getShortestResult(): ?Path
    {
        return $this->results[0] ?? null;
    }

    /**
     * Execute the Bellman-Ford calculation for all configured source/destination pairs.
     *
     * @return self
     * @throws LogicException
     */
    public function calculate(): self
    {
        if (empty($this->sources) || empty($this->destinations)) {
            throw new LogicException('Sources and Destinations must be set before calculating!');
        }

        $sourceCount = count($this->sources);
        $destinationsCount = count($this->destinations);

        if ($sourceCount !== $destinationsCount) {
            throw new LogicException('The number of sources must exactly match the number of destinations!');
        }

        for ($i = 0; $i < $sourceCount; $i++) {
            $this->runBellmanFord($this->sources[$i], $this->destinations[$i]);
        }

        return $this;
    }

    /**
     * Internal implementation of the Bellman-Ford Algorithm.
     *
     * @param string $sourceId
     * @param string $destinationId
     * @return void
     * @throws LogicException
     */
    private function runBellmanFord(string $sourceId, string $destinationId): void
    {
        if ($sourceId === $destinationId) {
            throw new LogicException("For non-loop traversals, the source and destination CANNOT be identical.");
        }

        $distances = [];
        $previousVertex = [];
        $previousEdge = [];

        foreach (array_keys($this->graph->getVertices()) as $id) {
            $distances[$id] = INF;
        }

        $distances[$sourceId] = 0;

        $this->relaxAllEdges($distances, $previousVertex, $previousEdge);
        $this->checkForNegativeCycles($distances);
        $this->buildPath($destinationId, $previousVertex, $previousEdge);
    }

    /**
     * Relax all edges in the graph up to (V - 1) times to find the shortest paths.
     *
     * @param array<string, float> $distances Passed by reference to update known shortest distances.
     * @param array<string, string> $previousVertex Passed by reference to build the breadcrumb trail.
     * @param array<string, string> $previousEdge Passed by reference to track the specific edge used.
     * @return void
     */
    private function relaxAllEdges(array &$distances, array &$previousVertex, array &$previousEdge): void
    {
        $vertices = $this->graph->getVertices();
        $vertexCount = count($vertices);

        for ($i = 1; $i < $vertexCount; $i++) {
            $updated = false;

            foreach ($vertices as $uId => $u) {
                if ($distances[$uId] === INF) {
                    continue;
                }

                $neighbors = $this->graph->getOutgoingNeighbors($uId);
                foreach (array_keys($neighbors) as $vId) {
                    $vId = (string) $vId;

                    if ($this->relaxSingleEdge($uId, $vId, $distances, $previousVertex, $previousEdge)) {
                        $updated = true;
                    }
                }
            }

            // Optimization: If no distances were updated in a full pass, we can stop early
            if (!$updated) {
                break;
            }
        }
    }

    /**
     * Evaluate the edges between two specific vertices to see if a cheaper path exists.
     *
     * @param string $uId The source vertex ID.
     * @param string $vId The destination vertex ID.
     * @param array<string, float> $distances Passed by reference.
     * @param array<string, string> $previousVertex Passed by reference.
     * @param array<string, string> $previousEdge Passed by reference.
     * @return bool True if a shorter distance was found and updated, false otherwise.
     */
    private function relaxSingleEdge(string $uId, string $vId, array &$distances, array &$previousVertex, array &$previousEdge): bool
    {
        $edges = $this->graph->getOutgoingEdgesTo($uId, $vId);
        $bestEdgeId = null;
        $minEdgeWeight = INF;

        foreach ($edges as $edgeId => $edge) {
            if ($edge->getWeight() < $minEdgeWeight) {
                $minEdgeWeight = $edge->getWeight();
                $bestEdgeId = (string) $edgeId;
            }
        }

        if ($bestEdgeId !== null) {
            $newDist = $distances[$uId] + $minEdgeWeight;
            if ($newDist < $distances[$vId]) {
                $distances[$vId] = $newDist;
                $previousVertex[$vId] = $uId;
                $previousEdge[$vId] = $bestEdgeId;
                return true;
            }
        }

        return false;
    }

    /**
     * Perform one final pass over the graph to mathematically prove the absence of negative-weight cycles.
     *
     * @param array<string, float> $distances The calculated shortest distances.
     * @return void
     * @throws LogicException If a negative cycle is detected, invalidating the shortest path.
     */
    private function checkForNegativeCycles(array $distances): void
    {
        foreach (array_keys($this->graph->getVertices()) as $uId) {
            if ($distances[$uId] === INF) {
                continue;
            }

            $neighbors = $this->graph->getOutgoingNeighbors($uId);
            foreach (array_keys($neighbors) as $vId) {
                $vId = (string) $vId;
                $edges = $this->graph->getOutgoingEdgesTo($uId, $vId);

                $minEdgeWeight = INF;
                foreach ($edges as $edge) {
                    if ($edge->getWeight() < $minEdgeWeight) {
                        $minEdgeWeight = $edge->getWeight();
                    }
                }

                if ($distances[$uId] + $minEdgeWeight < $distances[$vId]) {
                    throw new LogicException('Graph contains a negative-weight cycle! A shortest path cannot be mathematically determined.');
                }
            }
        }
    }

    /**
     * Backtrack through the calculated previous vertices to construct the final Path object.
     *
     * @param string $destinationId The final target vertex ID.
     * @param array<string, string> $previousVertex The breadcrumb trail of optimal vertex steps.
     * @param array<string, string> $previousEdge The breadcrumb trail of optimal edge steps.
     * @return void
     * @throws Exception
     */
    private function buildPath(string $destinationId, array $previousVertex, array $previousEdge): void
    {
        if (!isset($previousVertex[$destinationId])) {
            return;
        }

        $pathVertices = [];
        $pathEdges = [];
        $curr = $destinationId;

        while (isset($previousVertex[$curr])) {
            array_unshift($pathVertices, $curr);
            array_unshift($pathEdges, $previousEdge[$curr]);
            $curr = $previousVertex[$curr];
        }

        array_unshift($pathVertices, $curr);

        $path = new Path($this->graph);
        $path->start($pathVertices[0]);

        $edgesCount = count($pathEdges);
        for ($j = 0; $j < $edgesCount; $j++) {
            $path->addStep($pathVertices[$j + 1], $pathEdges[$j]);
        }

        $path->finish();
        $this->results[] = $path;
    }
}