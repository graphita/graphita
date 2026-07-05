<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\CycleFindingAlgorithm;
use Graphita\Graphita\Walks\Cycle;
use Graphita\Graphita\Graph;
use PHPUnit\Framework\TestCase;

class CycleFindingAlgorithmTest extends TestCase
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

    public function testCalculateWithTwoSteps()
    {
        $algorithm = new CycleFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('1');
        $algorithm->setSteps(2);
        $algorithm->calculate();

        $this->assertCount(2, $algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Cycle::class, $algorithm->getResults());
    }

    public function testCalculateWithoutStepsConstraints()
    {
        $algorithm = new CycleFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('1');
        $algorithm->calculate();

        $this->assertEquals(14, $algorithm->countResults());
    }
}