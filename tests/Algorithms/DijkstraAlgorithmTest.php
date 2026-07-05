<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\DijkstraAlgorithm;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Path;
use LogicException;
use PHPUnit\Framework\TestCase;

class DijkstraAlgorithmTest extends TestCase
{
    private Graph $graph;

    public function setUp(): void
    {
        parent::setUp();
        $this->graph = new Graph();

        // Create a weighted network
        $this->graph->createVertex('A');
        $this->graph->createVertex('B');
        $this->graph->createVertex('C');
        $this->graph->createVertex('D');

        $this->graph->createDirectedEdge('A', 'B')->setWeight(2);
        $this->graph->createDirectedEdge('B', 'C')->setWeight(1);
        $this->graph->createDirectedEdge('A', 'C')->setWeight(4);
        $this->graph->createDirectedEdge('C', 'D')->setWeight(3);
    }

    public function testShortestPathFindsOptimalRoute()
    {
        $algo = new DijkstraAlgorithm($this->graph);
        $algo->setSource('A')->setDestination('D')->calculate();

        $path = $algo->getShortestResult();

        $this->assertInstanceOf(Path::class, $path);
        // A -> B -> C -> D is cheaper (2+1+3 = 6) than A -> C -> D (4+3 = 7)
        $this->assertEquals(['A', 'B', 'C', 'D'], $path->getVertices());
        $this->assertEquals(6.0, $path->getTotalWeight());
    }

    public function testMissingEndpointsThrowException()
    {
        $algo = new DijkstraAlgorithm($this->graph);

        $this->expectException(LogicException::class);
        $algo->setSource('Z'); // 'Z' does not exist
    }

    public function testIdenticalEndpointsThrowException()
    {
        $algo = new DijkstraAlgorithm($this->graph);
        $algo->setSource('A')->setDestination('A');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('For non-loop traversals, the source and destination CANNOT be identical.');

        $algo->calculate();
    }
}