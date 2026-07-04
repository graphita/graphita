<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\TopologicalSortAlgorithm;
use Graphita\Graphita\Graph;
use LogicException;
use PHPUnit\Framework\TestCase;

class TopologicalSortAlgorithmTest extends TestCase
{
    private Graph $graph;

    public function setUp(): void
    {
        parent::setUp();
        $this->graph = new Graph();
    }

    public function testValidTopologicalSort()
    {
        $this->graph->createVertex('Task_A');
        $this->graph->createVertex('Task_B');
        $this->graph->createVertex('Task_C');

        // A must happen before B, B must happen before C
        $this->graph->createDirectedEdge('Task_A', 'Task_B');
        $this->graph->createDirectedEdge('Task_B', 'Task_C');

        $algo = new TopologicalSortAlgorithm($this->graph);
        $algo->calculate();

        $results = $algo->getResults();

        $this->assertCount(3, $results);
        $this->assertEquals('Task_A', $results[0]);
        $this->assertEquals('Task_B', $results[1]);
        $this->assertEquals('Task_C', $results[2]);
    }

    public function testCyclicGraphThrowsException()
    {
        $this->graph->createVertex('A');
        $this->graph->createVertex('B');

        // Circular dependency
        $this->graph->createDirectedEdge('A', 'B');
        $this->graph->createDirectedEdge('B', 'A');

        $algo = new TopologicalSortAlgorithm($this->graph);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Graph contains a cycle! Topological sort requires a Directed Acyclic Graph (DAG).');

        $algo->calculate();
    }

    public function testEmptyGraphReturnsEmptyArray()
    {
        $algo = new TopologicalSortAlgorithm($this->graph);
        $algo->calculate();

        $this->assertEmpty($algo->getResults());
    }
}