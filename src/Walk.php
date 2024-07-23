<?php

namespace Graphita\Graphita;

use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\DirectedEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Traits\AttributesHandlerTrait;
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
     * @var Vertex[]|AbstractEdge[]
     */
    private array $steps = array();

    /**
     * @var bool
     */
    private bool $started = false;

    /**
     * @var bool
     */
    private bool $finished = false;

    /**
     * @var float
     */
    private float $totalWeight = 0;

    const REPEAT_VERTICES = true;

    const REPEAT_EDGES = true;

    const IS_LOOP = false;

    use AttributesHandlerTrait;

    /**
     * @param Graph $graph
     * @param array $attributes
     */
    public function __construct(Graph &$graph, array $attributes = array())
    {
        $this->graph = $graph;
        $this->setAttributes($attributes);
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
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
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
        return count($this->getSteps());
    }

    /**
     * @return Vertex
     * @throws Exception
     */
    public function getFirstStep(): Vertex
    {
        if( $this->isStarted() )
            return $this->steps[0];

        throw new Exception('Walk is not Started !');
    }

    /**
     * @return Vertex
     * @throws Exception
     */
    public function getLastStep(): Vertex
    {
        if( $this->isStarted() )
            return $this->steps[ $this->countSteps() - 1 ];

        throw new Exception('Walk is not Started !');
    }

    /**
     * @return float
     */
    public function getTotalWeight(): float
    {
        return $this->totalWeight;
    }

    /**
     * @return array
     */
    public function getVertices(): array
    {
        return array_filter($this->getSteps(), function ($step) {
            if ($step instanceof Vertex) {
                return true;
            }

            return false;
        });
    }

    /**
     * @return int
     */
    public function countVertices(): int
    {
        return count($this->getVertices());
    }

    /**
     * @return array
     */
    public function getEdges(): array
    {
        return array_filter($this->getSteps(), function ($step) {
            if ($step instanceof AbstractEdge) {
                return true;
            }

            return false;
        });
    }

    /**
     * @return int
     */
    public function countEdges(): int
    {
        return count($this->getEdges());
    }

    /**
     * @param Vertex $sourceVertex
     * @return void
     * @throws Exception
     */
    public function start(Vertex $sourceVertex)
    {
        if( $this->isStarted() ){
            throw new Exception('Walk started before !');
        }

        $this->addStep($sourceVertex);
    }

    /**
     * @param Vertex|null $destinationVertex
     * @param AbstractEdge|null $throughEdge
     * @return void
     * @throws Exception
     */
    public function finish(?Vertex $destinationVertex = null, ?AbstractEdge $throughEdge = null)
    {
        if( $destinationVertex ){
            $this->addStep($destinationVertex, $throughEdge);
        }

        $vertices = $this->getVertices();

        if(
            $this->isLoop() &&
            $vertices[array_key_first($vertices)]->getId() != $vertices[array_key_last($vertices)]->getId()
        ){
            throw new Exception('Source Vertex and Destination Vertex must be Equal !');
        }

        if(
            !$this->isLoop() &&
            $vertices[array_key_first($vertices)]->getId() == $vertices[array_key_last($vertices)]->getId()
        ){
            throw new Exception('Source Vertex and Destination Vertex shouldn\'t be Equal !');
        }

        $this->finished = true;
    }

    /**
     * @param Vertex $nextVertex
     * @param AbstractEdge|null $throughEdge
     * @return true|void
     * @throws Exception
     */
    public function addStep(Vertex $nextVertex, ?AbstractEdge $throughEdge = null)
    {
        if ($nextVertex->getGraph() !== $this->getGraph())
            throw new InvalidArgumentException('Vertices must be in a same Graph !');

        if ($throughEdge && $throughEdge->getGraph() !== $this->getGraph())
            throw new InvalidArgumentException('Edges must be in a same Graph !');

        if( $this->isFinished() ){
            throw new Exception('Walk is finished before !');
        }

        if (! $this->isStarted()) {
            $this->steps[] = $nextVertex;
            $this->started = true;

            return true;
        }

        $steps = $this->getSteps();
        $prevVertex = $steps[array_key_last($steps)];

        if( !$prevVertex->hasOutgoingNeighbors($nextVertex->getId()) ){
            throw new Exception('Prev Vertex has no Edges to new Vertex !');
        }

        $nextEdges = $prevVertex->getOutgoingEdgesTo($nextVertex);

        if (count($nextEdges) > 1 && $throughEdge == null) {
            throw new Exception('There is many Edges between Prev Vertex and Next Vertex !');
        }

        if (count($nextEdges) == 1 && $throughEdge == null) {
            $throughEdge = $nextEdges[array_key_first($nextEdges)];
        }

        if (! array_key_exists($prevVertex->getId(), $throughEdge->getVertices())) {
            throw new Exception('Prev Vertex is not connected to Through Edge !');
        }

        if (! array_key_exists($nextVertex->getId(), $throughEdge->getVertices())) {
            throw new Exception('Next Vertex is not connected to Through Edge !');
        }

        $isNextVertexDuplicate = false;
        foreach ($this->getVertices() as $vertex) {
            if ($vertex->getId() == $nextVertex->getId()) {
                $isNextVertexDuplicate = true;
            }
        }
        if (
            (
                ! $this->canRepeatVertices() && $isNextVertexDuplicate
            ) &&
            !(
                $this->isLoop() && $this->getSteps()[0]->getId() == $nextVertex->getId()
            )
        ) {
            throw new Exception('You can\'t Repeat Vertex !');
        }

        $isNextEdgeDuplicate = false;
        foreach ($this->getEdges() as $edge) {
            if ($edge->getId() == $throughEdge->getId()) {
                $isNextEdgeDuplicate = true;
            }
        }
        if (! $this->canRepeatEdges() && $isNextEdgeDuplicate) {
            throw new Exception('You can\'t Repeat Edge !');
        }

        $this->steps[] = $throughEdge;
        $this->steps[] = $nextVertex;

        $this->totalWeight = array_reduce($this->getEdges(), function ($totalWeight, AbstractEdge $edge) {
            $totalWeight += $edge->getWeight();
            return $totalWeight;
        }, 0);

        if(
            $this->isLoop() &&
            $this->getSteps()[0]->getId() == $nextVertex->getId()
        ){
            $this->finish();
        }
    }
}