<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Walk;
use LogicException;

class WalkFindingAlgorithm
{
    private Graph $graph;

    /**
     * @var array<string>
     */
    private array $sources = [];

    /**
     * @var array<string>
     */
    private array $destinations = [];

    private ?int $steps = null;

    private int $minSteps = 1;

    private ?int $maxSteps = null;

    /**
     * @var array<Walk>
     */
    private array $results = [];

    const TRAVERSE_TYPE = Walk::class;

    /**
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * @return Graph
     */
    public function getGraph(): Graph
    {
        return $this->graph;
    }

    /**
     * @return array<string>
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @param string $sourceId
     * @return self
     */
    public function addSource(string $sourceId): self
    {
        if (!$this->getGraph()->hasVertex($sourceId)) {
            throw new LogicException("Source Vertex [{$sourceId}] must exist in Graph!");
        }

        $this->sources[] = $sourceId;

        return $this;
    }

    /**
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
     * @param string $sourceId
     * @return self
     */
    public function setSource(string $sourceId): self
    {
        return $this->setSources([$sourceId]);
    }

    /**
     * @return array<string>
     */
    public function getDestinations(): array
    {
        return $this->destinations;
    }

    /**
     * @param string $destinationId
     * @return self
     */
    public function addDestination(string $destinationId): self
    {
        if (!$this->getGraph()->hasVertex($destinationId)) {
            throw new LogicException("Destination Vertex [{$destinationId}] must exist in Graph!");
        }

        $this->destinations[] = $destinationId;

        return $this;
    }

    /**
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
     * @param string $destinationId
     * @return self
     */
    public function setDestination(string $destinationId): self
    {
        return $this->setDestinations([$destinationId]);
    }

    /**
     * @return int|null
     */
    public function getSteps(): ?int
    {
        return $this->steps;
    }

    /**
     * @param int|null $steps
     * @return self
     */
    public function setSteps(?int $steps = null): self
    {
        if ($steps !== null && $steps < 1) {
            throw new LogicException('Steps must be a positive integer equal to or greater than 1!');
        }
        $this->steps = $steps;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinSteps(): ?int
    {
        return $this->minSteps;
    }

    /**
     * @param int $minSteps
     * @return self
     */
    public function setMinSteps(int $minSteps): self
    {
        if ($minSteps < 1) {
            throw new LogicException('Min Steps must be a positive integer equal to or greater than 1!');
        }
        if ($this->getMaxSteps() !== null && $minSteps > $this->getMaxSteps()) {
            throw new LogicException('Min Steps must be less than or equal to Max Steps!');
        }
        $this->minSteps = $minSteps;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxSteps(): ?int
    {
        return $this->maxSteps;
    }

    /**
     * @param int $maxSteps
     * @return self
     */
    public function setMaxSteps(int $maxSteps): self
    {
        if ($maxSteps < 1) {
            throw new LogicException('Max Steps must be a positive integer equal to or greater than 1!');
        }
        if ($this->getMinSteps() !== null && $maxSteps < $this->getMinSteps()) {
            throw new LogicException('Max Steps must be equal to or greater than Min Steps!');
        }
        $this->maxSteps = $maxSteps;

        return $this;
    }

    /**
     * @return array<Walk>
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return int
     */
    public function countResults(): int
    {
        return count($this->getResults());
    }

    /**
     * @return self
     * @throws LogicException
     */
    public function calculate(): self
    {
        if (empty($this->getSources())) {
            throw new LogicException('Sources must be set before calculating!');
        }

        if (empty($this->getDestinations())) {
            throw new LogicException('Destinations must be set before calculating!');
        }

        if (count($this->getSources()) !== count($this->getDestinations())) {
            throw new LogicException('The number of sources must exactly match the number of destinations!');
        }

        $walkClass = static::TRAVERSE_TYPE;
        $rules = [
            'repeatVertices' => constant($walkClass . '::REPEAT_VERTICES'),
            'repeatEdges'    => constant($walkClass . '::REPEAT_EDGES'),
            'isLoop'         => constant($walkClass . '::IS_LOOP'),
        ];

        for ($i = 0; $i < count($this->sources); $i++) {
            $sourceId = $this->sources[$i];
            $destinationId = $this->destinations[$i];

            if ($rules['isLoop'] && $sourceId !== $destinationId) {
                throw new LogicException("For loop traversals, the source and destination MUST be identical.");
            }

            if (!$rules['isLoop'] && $sourceId === $destinationId) {
                throw new LogicException("For non-loop traversals, the source and destination CANNOT be identical.");
            }
        }

        if (($rules['repeatVertices'] || $rules['repeatEdges']) && $this->maxSteps === null && $this->steps === null) {
            throw new LogicException(
                'Traversals that allow repeating elements (Walk/Trail/Circuit) must have a strictly defined setMaxSteps() or setSteps() limit to prevent mathematically infinite loops.'
            );
        }

        for ($i = 0; $i < count($this->sources); $i++) {
            $sourceId = $this->sources[$i];
            $destinationId = $this->destinations[$i];

            $pathVertices = [$sourceId];
            $pathEdges = [];
            $visitedVertices = [$sourceId => true];
            $visitedEdges = [];

            $this->dfs(
                $sourceId,
                $destinationId,
                $pathVertices,
                $pathEdges,
                $visitedVertices,
                $visitedEdges,
                0,
                $rules
            );
        }

        return $this;
    }

    /**
     * @param string $currentVertex
     * @param string $targetVertex
     * @param array<string> $pathVertices
     * @param array<string> $pathEdges
     * @param array<string, bool> $visitedVertices
     * @param array<string, bool> $visitedEdges
     * @param int $depth
     * @param array $rules
     * @return void
     */
    private function dfs(
        string $currentVertex,
        string $targetVertex,
        array &$pathVertices,
        array &$pathEdges,
        array &$visitedVertices,
        array &$visitedEdges,
        int $depth,
        array $rules
    ): void {
        if ($this->steps !== null && $depth > $this->steps) {
            return;
        }

        if ($this->maxSteps !== null && $depth > $this->maxSteps) {
            return;
        }

        $minRequired = $this->steps ?? $this->minSteps ?? 1;

        if ($depth >= $minRequired && $currentVertex === $targetVertex) {
            if ($this->steps === null || $depth === $this->steps) {
                $this->buildAndStoreWalk($pathVertices, $pathEdges);
            }

            if ($rules['isLoop'] && !$rules['repeatVertices']) {
                return;
            }
        }

        $neighbors = $this->graph->getOutgoingNeighbors($currentVertex);

        foreach ($neighbors as $neighborId => $neighborObj) {
            $neighborId = (string) $neighborId;
            $edges = $this->graph->getOutgoingEdgesTo($currentVertex, $neighborId);

            foreach ($edges as $edgeId => $edge) {
                $edgeId = (string) $edgeId;

                if (!$rules['repeatEdges'] && isset($visitedEdges[$edgeId])) {
                    continue;
                }

                if (!$rules['repeatVertices'] && isset($visitedVertices[$neighborId])) {
                    if (!($rules['isLoop'] && $neighborId === $pathVertices[0])) {
                        continue;
                    }
                }

                $pathVertices[] = $neighborId;
                $pathEdges[] = $edgeId;

                $wasEdgeVisited = isset($visitedEdges[$edgeId]);
                $visitedEdges[$edgeId] = true;

                $wasVertexVisited = isset($visitedVertices[$neighborId]);
                $visitedVertices[$neighborId] = true;

                $this->dfs(
                    $neighborId,
                    $targetVertex,
                    $pathVertices,
                    $pathEdges,
                    $visitedVertices,
                    $visitedEdges,
                    $depth + 1,
                    $rules
                );

                array_pop($pathVertices);
                array_pop($pathEdges);

                if (!$wasEdgeVisited) {
                    unset($visitedEdges[$edgeId]);
                }

                if (!$wasVertexVisited) {
                    unset($visitedVertices[$neighborId]);
                }
            }
        }
    }

    /**
     * @param array<string> $pathVertices
     * @param array<string> $pathEdges
     * @return void
     */
    private function buildAndStoreWalk(array $pathVertices, array $pathEdges): void
    {
        $class = static::TRAVERSE_TYPE;
        $walk = new $class($this->graph);

        $walk->start($pathVertices[0]);

        for ($i = 0; $i < count($pathEdges); $i++) {
            $walk->addStep($pathVertices[$i + 1], $pathEdges[$i]);
        }

        $walk->finish();
        $this->results[] = $walk;
    }

    /**
     * @return self
     */
    public function sortResults(): self
    {
        usort($this->results, function (Walk $walk1, Walk $walk2) {
            if ($walk1->getTotalWeight() == $walk2->getTotalWeight()) {
                if ($walk1->countEdges() == $walk2->countEdges()) {
                    return strcmp(implode('-', $walk1->getVertices()), implode('-', $walk2->getVertices()));
                }
                return $walk1->countEdges() <=> $walk2->countEdges();
            }

            return $walk1->getTotalWeight() <=> $walk2->getTotalWeight();
        });

        return $this;
    }

    /**
     * @return Walk|null
     */
    public function getShortestResult(): ?Walk
    {
        if ($this->countResults() === 0) {
            return null;
        }

        $this->sortResults();
        return $this->results[0];
    }

    /**
     * @return Walk|null
     */
    public function getLongestResult(): ?Walk
    {
        if ($this->countResults() === 0) {
            return null;
        }

        $this->sortResults();
        return $this->results[$this->countResults() - 1];
    }
}