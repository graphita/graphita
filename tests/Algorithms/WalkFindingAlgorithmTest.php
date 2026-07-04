<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Graphita\Graphita\Algorithms\WalkFindingAlgorithm;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Walk;
use LogicException;
use PHPUnit\Framework\TestCase;

class WalkFindingAlgorithmTest extends TestCase
{
    private Graph $graph;
    private array $edges = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->graph = new Graph();

        $this->graph->createVertex('1');
        $this->graph->createVertex('2');
        $this->graph->createVertex('3');
        $this->graph->createVertex('4');

        $this->edges['1-2'] = $this->graph->createUndirectedEdge('1', '2')->getId();
        $this->edges['2-3'] = $this->graph->createUndirectedEdge('2', '3')->getId();
        $this->edges['3-4'] = $this->graph->createUndirectedEdge('3', '4')->getId();
        $this->edges['4-1'] = $this->graph->createUndirectedEdge('4', '1')->getId();
        $this->edges['1-3'] = $this->graph->createUndirectedEdge('1', '3')->getId();
        $this->edges['2-4'] = $this->graph->createUndirectedEdge('2', '4')->getId();
    }

    public function testEmptyWalkFindingAlgorithm()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);

        $this->assertEquals($this->graph, $algorithm->getGraph());
        $this->assertEmpty($algorithm->getSources());
        $this->assertEmpty($algorithm->getDestinations());
        $this->assertNull($algorithm->getSteps());
        $this->assertEquals(1, $algorithm->getMinSteps());
        $this->assertNull($algorithm->getMaxSteps());
        $this->assertEmpty($algorithm->getResults());
        $this->assertEquals(0, $algorithm->countResults());
    }

    public function testAddSourceOutsideGraph()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Source Vertex [5] must exist in Graph!');

        $algorithm->addSource('5');
    }

    public function testGetAndAddSource()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->addSource('1');

        $this->assertCount(1, $algorithm->getSources());
        $this->assertEquals('1', $algorithm->getSources()[0]);
    }

    public function testGetAndSetSources()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->setSources(['1', '2']);

        $this->assertCount(2, $algorithm->getSources());
        $this->assertEquals('1', $algorithm->getSources()[0]);
        $this->assertEquals('2', $algorithm->getSources()[1]);
    }

    public function testAddDestinationOutsideGraph()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Destination Vertex [5] must exist in Graph!');

        $algorithm->addDestination('5');
    }

    public function testCalculateWithInfiniteWalkGuard()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Traversals that allow repeating elements (Walk/Trail/Circuit) must have a strictly defined setMaxSteps() or setSteps() limit to prevent mathematically infinite loops.');

        $algorithm->calculate();
    }

    public function testCalculateWithOneStep()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');
        $algorithm->setSteps(1);
        $algorithm->calculate();

        $this->assertCount(1, $algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Walk::class, $algorithm->getResults());

        $results = $algorithm->getResults();
        $this->assertEquals('1', $results[0]->getFirstStep());
        $this->assertEquals('3', $results[0]->getLastStep());
        $this->assertEquals(1, $results[0]->countEdges());
        $this->assertEquals($this->edges['1-3'], $results[0]->getSteps()[1]);
    }

    public function testCalculateWithTwoSteps()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');
        $algorithm->setSteps(2);
        $algorithm->calculate();

        $this->assertCount(2, $algorithm->getResults());
        $this->assertEquals(2, $algorithm->getShortestResult()->getTotalWeight());

        foreach ($algorithm->getResults() as $result) {
            $this->assertEquals('1', $result->getFirstStep());
            $this->assertEquals('3', $result->getLastStep());
            $this->assertEquals(2, $result->countEdges());
        }
    }

    public function testCalculateWithMaxStepsConstraint()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->addSource('1');
        $algorithm->addDestination('3');
        $algorithm->setMaxSteps(4);
        $algorithm->calculate();

        $this->assertEquals(30, $algorithm->countResults());
    }

    public function testCalculateMultipleSourcesAndDestinations()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->setSources(['1', '2']);
        $algorithm->setDestinations(['3', '4']);
        $algorithm->setMaxSteps(4);
        $algorithm->calculate();

        $this->assertEquals(60, $algorithm->countResults());

        foreach ($algorithm->getResults() as $result) {
            $this->assertContains($result->getFirstStep(), ['1', '2']);
            $this->assertContains($result->getLastStep(), ['3', '4']);
        }
    }
}