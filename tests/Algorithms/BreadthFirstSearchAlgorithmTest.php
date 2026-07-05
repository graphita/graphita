<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\BreadthFirstSearchAlgorithm;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Path;
use LogicException;
use PHPUnit\Framework\TestCase;

class BreadthFirstSearchAlgorithmTest extends TestCase
{
    private Graph $graph;

    public function setUp(): void
    {
        parent::setUp();
        $this->graph = new Graph();

        $this->graph->createVertex('1');
        $this->graph->createVertex('2');
        $this->graph->createVertex('3');
        $this->graph->createVertex('4');

        // Creates a graph where 1->4 is one hop, but 1->2->3->4 is three hops
        $this->graph->createDirectedEdge('1', '2');
        $this->graph->createDirectedEdge('2', '3');
        $this->graph->createDirectedEdge('3', '4');
        $this->graph->createDirectedEdge('1', '4');
    }

    public function testBfsFindsFewestHops()
    {
        $algo = new BreadthFirstSearchAlgorithm($this->graph);
        $algo->setSource('1')->setDestination('4')->calculate();

        $path = $algo->getShortestResult();

        $this->assertInstanceOf(Path::class, $path);

        // Even if 1->2->3->4 exists, BFS MUST find 1->4 immediately
        $this->assertEquals(['1', '4'], $path->getVertices());
        $this->assertEquals(1, $path->countEdges());
    }

    public function testUnreachableDestinationReturnsNoResults()
    {
        $this->graph->createVertex('Isolated');

        $algo = new BreadthFirstSearchAlgorithm($this->graph);
        $algo->setSource('1')->setDestination('Isolated')->calculate();

        $this->assertNull($algo->getShortestResult());
        $this->assertEquals(0, $algo->countResults());
    }

    public function testCalculateWithoutEndpointsThrowsException()
    {
        $algo = new BreadthFirstSearchAlgorithm($this->graph);

        $this->expectException(LogicException::class);
        $algo->calculate();
    }
}