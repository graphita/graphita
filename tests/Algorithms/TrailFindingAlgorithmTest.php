<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\TrailFindingAlgorithm;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Trail;
use LogicException;
use PHPUnit\Framework\TestCase;

class TrailFindingAlgorithmTest extends TestCase
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

        $this->graph->createUndirectedEdge('1', '2');
        $this->graph->createUndirectedEdge('2', '3');
        $this->graph->createUndirectedEdge('3', '4');
        $this->graph->createUndirectedEdge('4', '1');
        $this->graph->createUndirectedEdge('1', '3');
        $this->graph->createUndirectedEdge('2', '4');
    }

    public function testCalculateWithInfiniteLoopGuard()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Traversals that allow repeating elements (Walk/Trail/Circuit) must have a strictly defined setMaxSteps() or setSteps() limit to prevent mathematically infinite loops.');

        $algorithm->calculate();
    }

    public function testCalculateWithStepsConstraint()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');
        $algorithm->setSteps(2);
        $algorithm->calculate();

        $this->assertCount(2, $algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Trail::class, $algorithm->getResults());
    }

    public function testCalculateWithMaxStepsConstraint()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');
        $algorithm->setMaxSteps(4);
        $algorithm->calculate();

        $this->assertEquals(9, $algorithm->countResults());
    }
}