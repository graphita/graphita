<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Path;
use LogicException;

/**
 * Calculates the shortest path using the A* (A-Star) search algorithm, guided by a heuristic.
 */
class AStarAlgorithm
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
     * @var callable|null
     */
    private $heuristicFunction = null;

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
     * Set the heuristic function to estimate distance between two vertices.
     * It should accept two Vertex IDs (current, target) and return a float.
     * * @param callable $heuristic
     * @return self
     */
    public function setHeuristic(callable $heuristic): self
    {
        $this->heuristicFunction = $heuristic;
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
     * Retrieve the single shortest Path.
     *
     * @return Path|null
     */
    public function getShortestResult(): ?Path
    {
        return $this->results[0] ?? null;
    }

    /**
     * Execute the A* calculation for all configured source/destination pairs.
     *
     * @return self
     * @throws LogicException
     */
    public function calculate(): self
    {
        if (empty($this->sources) || empty($this->destinations)) {
            throw new LogicException('Sources and Destinations must be set before calculating!');
        }

        if ($this->heuristicFunction === null) {
            $this->heuristicFunction = fn(string $a, string $b) => 0.0;
        }

        for ($i = 0; $i < count($this->sources); $i++) {
            $this->runAStar($this->sources[$i], $this->destinations[$i]);
        }

        return $this;
    }

    /**
     * Internal implementation of A* Algorithm for a single endpoint pair.
     *
     * @param string $sourceId
     * @param string $destinationId
     * @return void
     * @throws LogicException
     */
    private function runAStar(string $sourceId, string $destinationId): void
    {
        if ($sourceId === $destinationId) {
            throw new LogicException("For non-loop traversals, the source and destination CANNOT be identical.");
        }

        $gScore = [];
        $fScore = [];
        $previousVertex = [];
        $previousEdge = [];
        $openSet = [$sourceId => true];

        foreach ($this->graph->getVertices() as $id => $vertex) {
            $gScore[$id] = INF;
            $fScore[$id] = INF;
        }

        $gScore[$sourceId] = 0;
        $fScore[$sourceId] = call_user_func($this->heuristicFunction, $sourceId, $destinationId);

        while (!empty($openSet)) {
            $currentId = null;
            $minFScore = INF;

            foreach ($openSet as $id => $status) {
                if ($fScore[$id] < $minFScore) {
                    $minFScore = $fScore[$id];
                    $currentId = $id;
                }
            }

            if ($currentId === null) break;

            if ($currentId === $destinationId) {
                $this->buildPath($sourceId, $destinationId, $previousVertex, $previousEdge);
                return;
            }

            unset($openSet[$currentId]);

            $neighbors = $this->graph->getOutgoingNeighbors($currentId);

            foreach ($neighbors as $neighborId => $neighborObj) {
                $neighborId = (string) $neighborId;
                $edges = $this->graph->getOutgoingEdgesTo($currentId, $neighborId);

                $bestEdgeId = null;
                $minEdgeWeight = INF;

                foreach ($edges as $edgeId => $edge) {
                    if ($edge->getWeight() < $minEdgeWeight) {
                        $minEdgeWeight = $edge->getWeight();
                        $bestEdgeId = (string) $edgeId;
                    }
                }

                if ($bestEdgeId !== null) {
                    $tentativeGScore = $gScore[$currentId] + $minEdgeWeight;

                    if ($tentativeGScore < $gScore[$neighborId]) {
                        $previousVertex[$neighborId] = $currentId;
                        $previousEdge[$neighborId] = $bestEdgeId;
                        $gScore[$neighborId] = $tentativeGScore;

                        $hScore = call_user_func($this->heuristicFunction, $neighborId, $destinationId);
                        $fScore[$neighborId] = $gScore[$neighborId] + $hScore;

                        if (!isset($openSet[$neighborId])) {
                            $openSet[$neighborId] = true;
                        }
                    }
                }
            }
        }
    }

    /**
     * Backtrack and build the final Path object upon successful targeting.
     *
     * @param string $sourceId
     * @param string $destinationId
     * @param array $previousVertex
     * @param array $previousEdge
     * @return void
     */
    private function buildPath(string $sourceId, string $destinationId, array $previousVertex, array $previousEdge): void
    {
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