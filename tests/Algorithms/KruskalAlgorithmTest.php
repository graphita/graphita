<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\KruskalAlgorithm;
use Graphita\Graphita\Graph;
use PHPUnit\Framework\TestCase;

class KruskalAlgorithmTest extends TestCase
{
    private Graph $graph;

    public function setUp(): void
    {
        parent::setUp();
        $this->graph = new Graph();

        $this->graph->createVertex('A', ['type' => 'server']);
        $this->graph->createVertex('B', ['type' => 'server']);
        $this->graph->createVertex('C', ['type' => 'server']);
        $this->graph->createVertex('D', ['type' => 'server']);

        // Create a dense network with various weights
        $this->graph->createUndirectedEdge('A', 'B')->setWeight(1);
        $this->graph->createUndirectedEdge('B', 'C')->setWeight(2);
        $this->graph->createUndirectedEdge('A', 'C')->setWeight(3); // This edge should be dropped to prevent a loop
        $this->graph->createUndirectedEdge('C', 'D')->setWeight(1);
        $this->graph->createUndirectedEdge('B', 'D')->setWeight(5); // This edge should be dropped (too expensive)
    }

    public function testKruskalGeneratesMinimumSpanningTree()
    {
        $algo = new KruskalAlgorithm($this->graph);
        $algo->calculate();

        $mstGraph = $algo->getResultGraph();

        $this->assertInstanceOf(Graph::class, $mstGraph);

        // An MST for 4 connected vertices must have exactly 3 edges (V - 1)
        $this->assertEquals(4, $mstGraph->countVertices());
        $this->assertEquals(3, $mstGraph->countEdges());

        // The chosen edges should be A-B (1), B-C (2), and C-D (1). Total weight = 4.
        $totalWeight = 0;
        foreach ($mstGraph->getEdges() as $edge) {
            $totalWeight += $edge->getWeight();
        }

        $this->assertEquals(4.0, $totalWeight);
    }

    public function testMstRetainsVertexAttributes()
    {
        $algo = new KruskalAlgorithm($this->graph);
        $algo->calculate();

        $mstGraph = $algo->getResultGraph();
        $vertexA = $mstGraph->getVertex('A');

        $this->assertEquals('server', $vertexA->getAttribute('type'));
    }

    public function testEmptyGraphReturnsEmptyGraph()
    {
        $emptyGraph = new Graph();
        $algo = new KruskalAlgorithm($emptyGraph);
        $algo->calculate();

        $mstGraph = $algo->getResultGraph();

        $this->assertEquals(0, $mstGraph->countVertices());
        $this->assertEquals(0, $mstGraph->countEdges());
    }
}