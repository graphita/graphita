<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\PathFindingAlgorithm;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Path;
use LogicException;
use PHPUnit\Framework\TestCase;

class PathFindingAlgorithmTest extends TestCase
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

    public function testEmptyPathFindingAlgorithm()
    {
        $algorithm = new PathFindingAlgorithm($this->graph);

        $this->assertEquals($this->graph, $algorithm->getGraph());
        $this->assertEmpty($algorithm->getSources());
        $this->assertEmpty($algorithm->getDestinations());
        $this->assertEquals(1, $algorithm->getMinSteps());
    }

    public function testCalculateWithOneStep()
    {
        $algorithm = new PathFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');
        $algorithm->setSteps(1);
        $algorithm->calculate();

        $this->assertCount(1, $algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Path::class, $algorithm->getResults());

        $results = $algorithm->getResults();
        $this->assertEquals('1', $results[0]->getFirstStep());
        $this->assertEquals('3', $results[0]->getLastStep());
    }

    public function testCalculateWithTwoSteps()
    {
        $algorithm = new PathFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');
        $algorithm->setSteps(2);
        $algorithm->calculate();

        $this->assertCount(2, $algorithm->getResults());
    }

    public function testCalculateWithoutStepsConstraints()
    {
        $algorithm = new PathFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');
        $algorithm->calculate();

        $this->assertEquals(5, $algorithm->countResults());
    }

    public function testCalculateSameSourceAndDestinationThrowsException()
    {
        $algorithm = new PathFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('1');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('For non-loop traversals, the source and destination CANNOT be identical.');

        $algorithm->calculate();
    }
}