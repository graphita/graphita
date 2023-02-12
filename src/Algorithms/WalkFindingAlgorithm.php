<?php

namespace Graphita\Graphita\Algorithms;

use Exception;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Vertex;
use Graphita\Graphita\Walk;
use Graphita\Mathematics\Permutation;
use InvalidArgumentException;

class WalkFindingAlgorithm
{
    /**
     * @var Graph
     */
    private Graph $graph;

    /**
     * @var ?Vertex
     */
    private ?Vertex $source = null;

    /**
     * @var ?Vertex
     */
    private ?Vertex $destination = null;

    /**
     * @var ?int
     */
    private ?int $steps = null;

    /**
     * @var ?int
     */
    private ?int $minSteps = null;

    /**
     * @var ?int
     */
    private ?int $maxSteps = null;

    /**
     * @var array
     */
    private array $results = [];

    /**
     * @var string
     */
    private string $traversType = Walk::class;

    /**
     * @param Graph $graph
     */
    public function __construct(Graph &$graph)
    {
        $this->graph = $graph;
        $this->setMinSteps(1);
        $this->setMaxSteps($this->graph->countVertices());
    }

    /**
     * @return Graph
     */
    public function getGraph(): Graph
    {
        return $this->graph;
    }

    /**
     * @return Vertex|null
     */
    public function getSource(): ?Vertex
    {
        return $this->source;
    }

    /**
     * @param Vertex $source
     * @return WalkFindingAlgorithm
     */
    public function setSource(Vertex &$source): static
    {
        if ($source->getGraph() !== $this->getGraph()) {
            throw new InvalidArgumentException('Source Vertex must be in Graph !');
        }
        $this->source = $source;
        return $this;
    }

    /**
     * @return Vertex|null
     */
    public function getDestination(): ?Vertex
    {
        return $this->destination;
    }

    /**
     * @param Vertex $destination
     * @return WalkFindingAlgorithm
     */
    public function setDestination(Vertex &$destination): static
    {
        if ($destination->getGraph() !== $this->getGraph()) {
            throw new InvalidArgumentException('Destination Vertex must be in Graph !');
        }
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSteps(): ?int
    {
        return $this->steps;
    }

    /**
     * @param int $steps
     * @return WalkFindingAlgorithm
     */
    public function setSteps(int $steps): static
    {
        if ($steps < 1) {
            throw new InvalidArgumentException('Steps must be Positive Integer Number equal or bigger than 1 !');
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
     * @return WalkFindingAlgorithm
     */
    public function setMinSteps(int $minSteps): static
    {
        if ($minSteps < 1) {
            throw new InvalidArgumentException('Min Steps must be Positive Integer Number equal or bigger than 1 !');
        } else if ($this->getMaxSteps() != null && $minSteps > $this->getMaxSteps()) {
            throw new InvalidArgumentException('Min Steps must be Positive Integer Number equal or less than Max Steps !');
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
     * @return WalkFindingAlgorithm
     */
    public function setMaxSteps(int $maxSteps): static
    {
        if ($maxSteps < 1) {
            throw new InvalidArgumentException('Max Steps must be Positive Integer Number equal or bigger than 1 !');
        } else if ($this->getMinSteps() != null && $maxSteps < $this->getMinSteps()) {
            throw new InvalidArgumentException('Max Steps must be Positive Integer Number equal or bigger than Min Steps !');
        }
        $this->maxSteps = $maxSteps;
        return $this;
    }

    /**
     * @return array
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
        return count( $this->getResults() );
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function calculate(): static
    {

        if (!$this->getSource()) {
            throw new Exception('Source must be set, before calculate !');
        }
        if (!$this->getDestination()) {
            throw new Exception('Destination must be set, before calculate !');
        }
        if (!$this->getSteps()) {
            for ($step = $this->getMinSteps(); $step <= $this->getMaxSteps(); $step++) {
                $walks = (new static($this->graph))->setSource($this->source)->setDestination($this->destination)->setSteps($step)->calculate()->getResults();
                foreach ($walks as $walk) {
                    $this->addResult($walk->getEdges());
                }
            }
        } else {
            $verticesCount = $this->getSteps() - 1;
            if ($verticesCount == 0) {
                $edges = $this->getEdgesBetweenVertices([$this->getSource()->getId(), $this->getDestination()->getId()]);
                foreach ($edges as $edge) {
                    $this->addResult($edge);
                }
            } else {
                $verticesId = array_map(function (Vertex $vertex) {
                    return $vertex->getId();
                }, $this->getGraph()->getVertices());
                $verticesId = array_values(array_filter($verticesId, function ($vertexId) {
                    return $vertexId != $this->getSource()->getId() && $vertexId != $this->getDestination()->getId();
                }));

                $permutation = new Permutation();
                $permutation->setItems($verticesId);
                $permutation->setSelection($verticesCount);
                $permutation->setRepetitions(true);
                $permutation->calculate();
                $possibilities = $permutation->getPossibilities();
                foreach ($possibilities as $possibility) {
                    array_unshift($possibility, $this->getSource()->getId());
                    array_push($possibility, $this->getDestination()->getId());

                    $edges = $this->getEdgesBetweenVertices($possibility);
                    foreach ($edges as $edge) {
                        $this->addResult($edge);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @param array $verticesId
     * @return array
     */
    private function getEdgesBetweenVertices(array $verticesId): array
    {
        try {
            $edges = [];
            foreach ($verticesId as $vertexKey => $vertexId) {
                $prevVertexId = $verticesId[$vertexKey - 1] ?? false;
                if ($prevVertexId) {
                    $incomingEdges = $this->getGraph()->getVertex($vertexId)->getIncomingEdgesFrom($this->getGraph()->getVertex($prevVertexId));
                    if (count($edges) > 0) {
                        $traveledRoutes = $edges;
                        $edges = [];
                        foreach ($traveledRoutes as $traveledRoute) {
                            foreach ($incomingEdges as $incomingEdge) {
                                $edges[] = array_merge($traveledRoute, [$incomingEdge]);
                            }
                        }
                    } else {
                        foreach ($incomingEdges as $incomingEdge) {
                            $edges[] = [$incomingEdge];
                        }
                    }
                }
            }
        } catch (Exception $exception) {
            $edges = [];
        }
        return $edges;
    }

    /**
     * @param array $result
     * @return void
     */
    private function addResult(array $result): void
    {
        try {
            $graph = $this->getGraph();
            $walk = new $this->traversType($graph);
            $walk->addEdges($result);
            $this->results[] = $walk;

            $this->sortResults();
        } catch (Exception $exception) {

        }
    }

    /**
     * @return void
     */
    private function sortResults(): void
    {
        usort($this->results, function (Walk $result1, Walk $result2) {
            if ($result1->getTotalWeight() == $result2->getTotalWeight()) {
                return $result1->countEdges() > $result2->countEdges() ? 1 : -1;
            }
            return $result1->getTotalWeight() > $result2->getTotalWeight() ? 1 : -1;
        });
    }

    /**
     * @return Walk|null
     */
    public function getShortestResult(): ?Walk
    {
        if( $this->countResults() ){
            return $this->getResults()[array_key_first($this->getResults())];
        }
        return null;
    }

    /**
     * @return Walk|null
     */
    public function getLongestResult(): ?Walk
    {
        if( $this->countResults() ){
            return $this->getResults()[array_key_last($this->getResults())];
        }
        return null;
    }
}