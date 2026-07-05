<?php

namespace Graphita\Graphita\Walks;

use Exception;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Traits\AttributesHandlerTrait;
use InvalidArgumentException;
use LogicException;

class Walk
{
    private Graph $graph;

    /**
     * @var array<string>
     */
    private array $vertexIds = [];

    /**
     * @var array<string>
     */
    private array $edgeIds = [];

    private bool $started = false;

    private bool $finished = false;

    private float $totalWeight = 0.0;

    const REPEAT_VERTICES = true;

    const REPEAT_EDGES = true;

    const IS_LOOP = false;

    use AttributesHandlerTrait;

    /**
     * Construct a new Walk instance.
     *
     * @param Graph $graph
     * @param array $attributes
     */
    public function __construct(Graph $graph, array $attributes = [])
    {
        $this->graph = $graph;
        $this->setAttributes($attributes);
    }

    /**
     * Get the graph associated with this walk.
     *
     * @return Graph
     */
    public function getGraph(): Graph
    {
        return $this->graph;
    }

    /**
     * Check if the walk allows repeating vertices.
     *
     * @return bool
     */
    public function canRepeatVertices(): bool
    {
        return static::REPEAT_VERTICES;
    }

    /**
     * Check if the walk allows repeating edges.
     *
     * @return bool
     */
    public function canRepeatEdges(): bool
    {
        return static::REPEAT_EDGES;
    }

    /**
     * Check if the walk is explicitly a loop.
     *
     * @return bool
     */
    public function isLoop(): bool
    {
        return static::IS_LOOP;
    }

    /**
     * Check if the walk has started.
     *
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * Check if the walk has finished.
     *
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    /**
     * Get all steps (interleaved vertex IDs and edge IDs).
     *
     * @return array<string>
     */
    public function getSteps(): array
    {
        $steps = [];
        foreach ($this->vertexIds as $index => $vertexId) {
            $steps[] = $vertexId;
            if (isset($this->edgeIds[$index])) {
                $steps[] = $this->edgeIds[$index];
            }
        }
        return $steps;
    }

    /**
     * Count the total number of steps (vertices + edges).
     *
     * @return int
     */
    public function countSteps(): int
    {
        return count($this->vertexIds) + count($this->edgeIds);
    }

    /**
     * Get the ID of the first vertex in the walk.
     *
     * @return string
     * @throws Exception
     */
    public function getFirstStep(): string
    {
        if ($this->isStarted()) {
            return $this->vertexIds[0];
        }

        throw new Exception('Walk is not Started !');
    }

    /**
     * Get the ID of the last vertex in the walk.
     *
     * @return string
     * @throws Exception
     */
    public function getLastStep(): string
    {
        if ($this->isStarted()) {
            return end($this->vertexIds);
        }

        throw new Exception('Walk is not Started !');
    }

    /**
     * Get the total weight of the walk.
     *
     * @return float
     */
    public function getTotalWeight(): float
    {
        return $this->totalWeight;
    }

    /**
     * Calculate the total weight of the walk.
     *
     * @return void
     */
    public function calculateTotalWeight(): void
    {
        $this->totalWeight = 0.0;
        foreach ($this->getEdges() as $edgeId) {
            $this->totalWeight += $this->graph->getEdge($edgeId)->getWeight();
        }
    }

    /**
     * Get an array of all vertex IDs in the walk.
     *
     * @return array<string>
     */
    public function getVertices(): array
    {
        return $this->vertexIds;
    }

    /**
     * Count the total number of vertices in the walk.
     *
     * @return int
     */
    public function countVertices(): int
    {
        return count($this->vertexIds);
    }

    /**
     * Get an array of all edge IDs in the walk.
     *
     * @return array<string>
     */
    public function getEdges(): array
    {
        return $this->edgeIds;
    }

    /**
     * Count the total number of edges in the walk.
     *
     * @return int
     */
    public function countEdges(): int
    {
        return count($this->edgeIds);
    }

    /**
     * Start the walk at a specific vertex.
     *
     * @param string $sourceVertexId
     * @return void
     * @throws Exception
     */
    public function start(string $sourceVertexId): void
    {
        if ($this->isStarted()) {
            throw new Exception('Walk started before !');
        }

        $this->addStep($sourceVertexId);
    }

    /**
     * Finish the walk, optionally adding a final destination vertex.
     *
     * @param string|null $destinationVertexId
     * @param string|null $throughEdgeId
     * @return void
     * @throws Exception
     */
    public function finish(?string $destinationVertexId = null, ?string $throughEdgeId = null): void
    {
        if ($destinationVertexId) {
            $this->addStep($destinationVertexId, $throughEdgeId);
        }

        if (empty($this->vertexIds)) {
            $this->finished = true;
            return;
        }

        $firstVertex = $this->vertexIds[0];
        $lastVertex = end($this->vertexIds);

        if ($this->isLoop() && empty($this->edgeIds)) {
            throw new Exception('A loop must traverse at least one edge!');
        }

        if ($this->isLoop() && $firstVertex !== $lastVertex) {
            throw new Exception('Source Vertex and Destination Vertex must be Equal for a loop!');
        }

        if (!$this->isLoop() && $firstVertex === $lastVertex) {
            throw new Exception("Source Vertex and Destination Vertex shouldn't be Equal for this path type!");
        }

        $this->finished = true;
    }

    /**
     * Add the next vertex to the walk, optionally specifying the edge.
     *
     * @param string $nextVertexId
     * @param string|null $throughEdgeId
     * @return bool|void
     * @throws Exception|LogicException
     */
    public function addStep(string $nextVertexId, ?string $throughEdgeId = null)
    {
        if (!$this->getGraph()->hasVertex($nextVertexId)) {
            throw new LogicException('Vertices must be in the same Graph!');
        }

        if ($throughEdgeId && !$this->getGraph()->hasEdge($throughEdgeId)) {
            throw new LogicException('Edges must be in the same Graph!');
        }

        if ($this->isFinished()) {
            throw new Exception('Walk is finished before!');
        }

        if (!$this->isStarted()) {
            $this->vertexIds[] = $nextVertexId;
            $this->started = true;
            return true;
        }

        $prevVertexId = end($this->vertexIds);

        if (!$this->graph->hasOutgoingNeighbor($prevVertexId, $nextVertexId)) {
            throw new Exception('Prev Vertex has no Edges to new Vertex!');
        }

        $nextEdges = $this->graph->getOutgoingEdgesTo($prevVertexId, $nextVertexId);

        if ($throughEdgeId !== null && !isset($nextEdges[$throughEdgeId])) {
            throw new Exception('The provided throughEdgeId is not a valid outgoing edge between these vertices!');
        }

        if (count($nextEdges) > 1 && $throughEdgeId === null) {
            throw new Exception('There are multiple Edges between Prev Vertex and Next Vertex. You must specify throughEdgeId!');
        }

        if (count($nextEdges) === 1 && $throughEdgeId === null) {
            $throughEdgeId = array_key_first($nextEdges);
        }

        $isNextVertexDuplicate = in_array($nextVertexId, $this->vertexIds, true);

        if (
            (!$this->canRepeatVertices() && $isNextVertexDuplicate) &&
            !($this->isLoop() && $this->vertexIds[0] === $nextVertexId)
        ) {
            throw new Exception("You can't Repeat Vertex!");
        }

        $isNextEdgeDuplicate = in_array($throughEdgeId, $this->edgeIds, true);

        if (!$this->canRepeatEdges() && $isNextEdgeDuplicate) {
            throw new Exception("You can't Repeat Edge!");
        }

        $this->edgeIds[] = $throughEdgeId;
        $this->vertexIds[] = $nextVertexId;

        if ($throughEdgeId === null) {
            throw new InvalidArgumentException('You must provide a valid Edge ID to complete this step.');
        }

        $this->totalWeight += $this->graph->getEdge($throughEdgeId)->getWeight();
    }
}