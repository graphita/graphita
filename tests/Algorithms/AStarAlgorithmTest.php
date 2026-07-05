<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\AStarAlgorithm;
use Graphita\Graphita\Graph;
use LogicException;
use PHPUnit\Framework\TestCase;

class AStarAlgorithmTest extends TestCase
{
    private Graph $graph;

    public function setUp(): void
    {
        parent::setUp();
        $this->graph = new Graph();

        // Create vertices with X, Y coordinates as attributes
        $this->graph->createVertex('A', ['x' => 0, 'y' => 0]);
        $this->graph->createVertex('B', ['x' => 10, 'y' => 0]);
        $this->graph->createVertex('C', ['x' => 10, 'y' => 10]);

        $this->graph->createDirectedEdge('A', 'B')->setWeight(10);
        $this->graph->createDirectedEdge('B', 'C')->setWeight(10);

        // Hypothetical diagonal edge
        $this->graph->createDirectedEdge('A', 'C')->setWeight(14.14);
    }

    public function testAStarFindsOptimalPathWithHeuristic()
    {
        $algo = new AStarAlgorithm($this->graph);
        $algo->setSource('A')->setDestination('C');

        // Straight line distance heuristic
        $algo->setHeuristic(function (string $current, string $target) {
            $v1 = $this->graph->getVertex($current);
            $v2 = $this->graph->getVertex($target);

            $dx = $v1->getAttribute('x') - $v2->getAttribute('x');
            $dy = $v1->getAttribute('y') - $v2->getAttribute('y');

            return sqrt(($dx * $dx) + ($dy * $dy));
        });

        $algo->calculate();

        $path = $algo->getShortestResult();

        $this->assertNotNull($path);
        $this->assertEquals(['A', 'C'], $path->getVertices());
        $this->assertEquals(14.14, $path->getTotalWeight());
    }

    public function testAStarFallsBackToDijkstraWithoutHeuristic()
    {
        $algo = new AStarAlgorithm($this->graph);
        // By not setting a heuristic, it should still function perfectly (behaves as Dijkstra)
        $algo->setSource('A')->setDestination('C')->calculate();

        $path = $algo->getShortestResult();

        $this->assertNotNull($path);
        $this->assertEquals(['A', 'C'], $path->getVertices());
    }
}