<?php

namespace Graphita\Graphita\Algorithms;

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

        $vertices = $this->graph->getVertices();
        $vertexCount = count($vertices);

        $distances = [];
        $previousVertex = [];
        $previousEdge = [];

        foreach ($vertices as $id => $vertex) {
            $distances[$id] = INF;
        }

        $distances[$sourceId] = 0;

        // Step 1: Relax all edges (V - 1) times
        for ($i = 1; $i < $vertexCount; $i++) {
            $updated = false;

            foreach ($vertices as $uId => $u) {
                if ($distances[$uId] === INF) {
                    continue;
                }

                $neighbors = $this->graph->getOutgoingNeighbors($uId);

                foreach ($neighbors as $vId => $v) {
                    $vId = (string) $vId;
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
                            $updated = true;
                        }
                    }
                }
            }

            // Optimization: If no distances were updated in a full pass, we can stop early
            if (!$updated) {
                break;
            }
        }

        // Step 2: Check for negative-weight cycles
        foreach ($vertices as $uId => $u) {
            if ($distances[$uId] === INF) {
                continue;
            }

            $neighbors = $this->graph->getOutgoingNeighbors($uId);
            foreach ($neighbors as $vId => $v) {
                $vId = (string) $vId;
                $edges = $this->graph->getOutgoingEdgesTo($uId, $vId);

                $minEdgeWeight = INF;
                foreach ($edges as $edgeId => $edge) {
                    if ($edge->getWeight() < $minEdgeWeight) {
                        $minEdgeWeight = $edge->getWeight();
                    }
                }

                if ($distances[$uId] + $minEdgeWeight < $distances[$vId]) {
                    throw new LogicException('Graph contains a negative-weight cycle! A shortest path cannot be mathematically determined.');
                }
            }
        }

        // Step 3: Backtrack and build the final Path
        if (isset($previousVertex[$destinationId])) {
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

            for ($j = 0; $j < count($pathEdges); $j++) {
                $path->addStep($pathVertices[$j + 1], $pathEdges[$j]);
            }

            $path->finish();
            $this->results[] = $path;
        }
    }
}