<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\BellmanFordAlgorithm;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Path;
use LogicException;
use PHPUnit\Framework\TestCase;

class BellmanFordAlgorithmTest extends TestCase
{
    private Graph $graph;

    public function setUp(): void
    {
        parent::setUp();
        $this->graph = new Graph();

        $this->graph->createVertex('A');
        $this->graph->createVertex('B');
        $this->graph->createVertex('C');
        $this->graph->createVertex('D');
    }

    public function testFindsShortestPathWithNegativeWeights()
    {
        // A -> B costs 4
        $this->graph->createDirectedEdge('A', 'B')->setWeight(4);
        // A -> C costs 5
        $this->graph->createDirectedEdge('A', 'C')->setWeight(5);
        // B -> C has a NEGATIVE weight of -2
        $this->graph->createDirectedEdge('B', 'C')->setWeight(-2);
        // C -> D costs 3
        $this->graph->createDirectedEdge('C', 'D')->setWeight(3);

        $algo = new BellmanFordAlgorithm($this->graph);
        $algo->setSource('A')->setDestination('D')->calculate();

        $path = $algo->getShortestResult();

        $this->assertInstanceOf(Path::class, $path);

        // A -> B -> C -> D = 4 + (-2) + 3 = 5
        // A -> C -> D = 5 + 3 = 8
        // The algorithm MUST choose the negative path route
        $this->assertEquals(['A', 'B', 'C', 'D'], $path->getVertices());
        $this->assertEquals(5.0, $path->getTotalWeight());
    }

    public function testNegativeWeightCycleThrowsException()
    {
        // Create a cycle that generates infinite negative value
        $this->graph->createDirectedEdge('A', 'B')->setWeight(1);
        $this->graph->createDirectedEdge('B', 'C')->setWeight(-1);
        $this->graph->createDirectedEdge('C', 'A')->setWeight(-1);

        $algo = new BellmanFordAlgorithm($this->graph);
        $algo->setSource('A')->setDestination('C');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Graph contains a negative-weight cycle! A shortest path cannot be mathematically determined.');

        $algo->calculate();
    }

    public function testIdenticalEndpointsThrowException()
    {
        $algo = new BellmanFordAlgorithm($this->graph);
        $algo->setSource('A')->setDestination('A');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('For non-loop traversals, the source and destination CANNOT be identical.');

        $algo->calculate();
    }
}