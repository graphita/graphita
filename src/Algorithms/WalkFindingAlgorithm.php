<?php

namespace Graphita\Graphita\Algorithms;

use Exception;
use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Vertex;
use Graphita\Graphita\Walk;
use InvalidArgumentException;
use ReflectionClass;
use Throwable;

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

    const TRAVERSE_TYPE = Walk::class;

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
     * @param Vertex|null $source
     * @return WalkFindingAlgorithm
     */
    public function setSource(?Vertex &$source = null): WalkFindingAlgorithm
    {
        if ($source !== null && $source->getGraph() !== $this->getGraph()) {
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
     * @param Vertex|null $destination
     * @return WalkFindingAlgorithm
     */
    public function setDestination(?Vertex &$destination = null): WalkFindingAlgorithm
    {
        if ($destination !== null && $destination->getGraph() !== $this->getGraph()) {
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
     * @param int|null $steps
     * @return WalkFindingAlgorithm
     */
    public function setSteps(?int $steps = null): WalkFindingAlgorithm
    {
        if ($steps !== null && $steps < 1) {
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
    public function setMinSteps(int $minSteps): WalkFindingAlgorithm
    {
        if ($minSteps < 1) {
            throw new InvalidArgumentException('Min Steps must be Positive Integer Number equal or bigger than 1 !');
        } else if ($this->getMaxSteps() !== null && $minSteps > $this->getMaxSteps()) {
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
    public function setMaxSteps(int $maxSteps): WalkFindingAlgorithm
    {
        if ($maxSteps < 1) {
            throw new InvalidArgumentException('Max Steps must be Positive Integer Number equal or bigger than 1 !');
        } else if ($this->getMinSteps() !== null && $maxSteps < $this->getMinSteps()) {
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
        return count($this->getResults());
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function calculate(): WalkFindingAlgorithm
    {
        if (! $this->getSource()) {
            throw new Exception('Source must be set, before calculate !');
        }
        if (! $this->getDestination()) {
            throw new Exception('Destination must be set, before calculate !');
        }

        if ($this->getSteps() === null) {
            for ($step = $this->getMinSteps(); $step <= $this->getMaxSteps(); $step++) {
                $walks = (new static($this->graph))
                    ->setSource($this->source)
                    ->setDestination($this->destination)
                    ->setSteps($step)
                    ->calculate()
                    ->getResults();

                $this->addResults($walks);
            }
        } else {
            $graph = $this->getGraph();

            /** @var Walk[] $walks */
            $walks = [];
            /** @var Walk $walk */
            $walk = (new ReflectionClass(static::TRAVERSE_TYPE))->newInstanceArgs([
                &$graph
            ]);
            $walk->start($this->getSource());

            $walks[] = $walk;

            for ($step = 1; $step <= $this->getSteps(); $step++) {
                $newWalks = [];

                foreach ($walks as $walk) {
                    /** @var Vertex $lastVertex */
                    $lastVertex = $walk->getLastStep();

                    /** @var AbstractEdge[] $outgoingEdges */
                    $outgoingEdges = $lastVertex->getOutgoingEdges();

                    foreach ($outgoingEdges as $outgoingEdge) {
                        /** @var Vertex[] $vertices */
                        $vertices = $outgoingEdge->getVertices();

                        /** @var array $verticesId */
                        $verticesId = array_keys($vertices);

                        $nextVertex = $vertices[$verticesId[0]]->getId() == $lastVertex->getId() ? $vertices[$verticesId[1]] : $vertices[$verticesId[0]];

                        try {
                            $newWalk = clone $walk;
                            $newWalk->addStep($nextVertex, $outgoingEdge);

                            $newWalks[] = $newWalk;
                        } catch (Throwable $exception) {
                            // Nothing to do, because it's important to add step successfully !
                        }
                    }
                }

                $newWalks = array_filter($newWalks, function (Walk $newWalk) {
                    return $newWalk->countEdges() <= $this->getSteps();
                });

                $walks = $newWalks;
            }

            foreach ($walks as $walk) {
                try {
                    $walk->finish();
                } catch (Throwable $exception) {
                    // Nothing to do, because it's important to finish successfully !
                }
            }

            $walks = array_filter($walks, function (Walk $walk) {
                return $walk->isFinished() &&
                    $walk->countEdges() == $this->getSteps() &&
                    $walk->getFirstStep()->getId() == $this->getSource()->getId() &&
                    $walk->getLastStep()->getId() == $this->getDestination()->getId();
            });

            $this->addResults($walks);
        }
        return $this;
    }

    /**
     * @param Walk[] $walks
     * @return void
     */
    private function addResults(array $walks, bool $sort = false): void
    {
        foreach ($walks as $walk) {
            $this->results[] = $walk;
        }

        if ($sort) {
            $this->sortResults();
        }
    }

    /**
     * @return void
     */
    private function sortResults(): void
    {
        usort($this->results, function (Walk $walk1, Walk $walk2) {
            if ($walk1->getTotalWeight() == $walk2->getTotalWeight()) {
                if ($walk1->countEdges() == $walk2->countEdges()) {
                    $vertices1 = array_map(function (Vertex $vertex) {
                        return $vertex->getId();
                    }, $walk1->getVertices());

                    $vertices2 = array_map(function (Vertex $vertex) {
                        return $vertex->getId();
                    }, $walk2->getVertices());

                    return strcmp(implode('-', $vertices1), implode('-', $vertices2));
                }

                return $walk1->countEdges() >= $walk2->countEdges() ? 1 : -1;
            }

            return $walk1->getTotalWeight() > $walk2->getTotalWeight() ? 1 : -1;
        });
    }

    /**
     * @return Walk|null
     */
    public function getShortestResult(): ?Walk
    {
        if ($this->countResults()) {
            return $this->getResults()[0];
        }

        return null;
    }

    /**
     * @return Walk|null
     */
    public function getLongestResult(): ?Walk
    {
        if ($this->countResults()) {
            return $this->getResults()[$this->countResults() - 1];
        }

        return null;
    }

    /**
     * @return $this
     */
    public function calculateTotalWeight(bool $sort = false): WalkFindingAlgorithm
    {
        $results = $this->getResults();
        array_walk($results, function (Walk $walk) {
            $walk->calculateTotalWeight();
        });

        if ($sort) {
            $this->sortResults();
        }

        return $this;
    }
}