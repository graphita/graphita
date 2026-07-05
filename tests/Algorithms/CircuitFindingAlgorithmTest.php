<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\CircuitFindingAlgorithm;
use Graphita\Graphita\Walks\Circuit;
use Graphita\Graphita\Graph;
use LogicException;
use PHPUnit\Framework\TestCase;

class CircuitFindingAlgorithmTest extends TestCase
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

        $this->graph->createDirectedEdge('1', '2');
        $this->graph->createDirectedEdge('2', '1');
        $this->graph->createUndirectedEdge('2', '3');
        $this->graph->createUndirectedEdge('3', '4');
        $this->graph->createDirectedEdge('4', '1');
        $this->graph->createDirectedEdge('1', '4');
        $this->graph->createUndirectedEdge('1', '3');
        $this->graph->createUndirectedEdge('2', '4');
    }

    public function testCalculateWithInfiniteLoopGuard()
    {
        $algorithm = new CircuitFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('1');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Traversals that allow repeating elements (Walk/Trail/Circuit) must have a strictly defined setMaxSteps() or setSteps() limit to prevent mathematically infinite loops.');

        $algorithm->calculate();
    }

    public function testCalculateWithTwoSteps()
    {
        $algorithm = new CircuitFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('1');
        $algorithm->setSteps(2);
        $algorithm->calculate();

        $this->assertCount(2, $algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Circuit::class, $algorithm->getResults());

        foreach ($algorithm->getResults() as $result) {
            $this->assertEquals('1', $result->getFirstStep());
            $this->assertEquals('1', $result->getLastStep());
        }
    }

    public function testCalculateWithMaxSteps()
    {
        $algorithm = new CircuitFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('1');
        $algorithm->setMaxSteps(4);
        $algorithm->calculate();

        $this->assertEquals(16, $algorithm->countResults());
    }

    public function testCalculateMismatchedLoopEndpointsThrowsException()
    {
        $algorithm = new CircuitFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('2');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('For loop traversals, the source and destination MUST be identical.');

        $algorithm->calculate();
    }
}