<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Algorithms\WalkFindingAlgorithm;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walk;
use PHPUnit\Framework\TestCase;

class WalkFindingAlgorithmTest extends TestCase
{
    private Graph $graph;
    private array $vertices = [];
    private array $edges = [];
    public function setUp(): void
    {
        parent::setUp();

        $this->graph = new Graph();

        $this->vertices[1] = $this->graph->createVertex(1);
        $this->vertices[2] = $this->graph->createVertex(2);
        $this->vertices[3] = $this->graph->createVertex(3);
        $this->vertices[4] = $this->graph->createVertex(4);

        $this->edges['1-2'] = $this->graph->createUndirectedEdge($this->vertices[1], $this->vertices[2]);
        $this->edges['2-3'] = $this->graph->createUndirectedEdge($this->vertices[2], $this->vertices[3]);
        $this->edges['3-4'] = $this->graph->createUndirectedEdge($this->vertices[3], $this->vertices[4]);
        $this->edges['4-1'] = $this->graph->createUndirectedEdge($this->vertices[4], $this->vertices[1]);
        $this->edges['1-3'] = $this->graph->createUndirectedEdge($this->vertices[1], $this->vertices[3]);
        $this->edges['2-4'] = $this->graph->createUndirectedEdge($this->vertices[2], $this->vertices[4]);
    }

    public function testEmptyWalkFindingAlgorithm()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);

        $this->assertEquals($this->graph, $algorithm->getGraph());
        $this->assertEmpty($algorithm->getSource());
        $this->assertEmpty($algorithm->getDestination());
        $this->assertEmpty($algorithm->getSteps());
        $this->assertIsArray($algorithm->getResults());
        $this->assertEmpty($algorithm->getResults());
        $this->assertCount(0, $algorithm->getResults());
    }

    public function testGetAndSetSource()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);

        $this->assertEquals($this->vertices[1], $algorithm->getSource());
    }

    public function testGetAndSetDestination()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->setDestination($this->vertices[4]);

        $this->assertEquals($this->vertices[4], $algorithm->getDestination());
    }

    public function testGetAndSetSteps()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->setSteps(2);

        $this->assertEquals(2, $algorithm->getSteps());
    }

    public function testCalculateWithoutSource()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Source must be set, before calculate !');

        $algorithm->calculate();
    }

    public function testCalculateWithoutDestination()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Destination must be set, before calculate !');

        $algorithm->calculate();
    }

    public function testCalculateWithOneStep()
    {
        $algorithm = new WalkFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);
        $algorithm->setDestination($this->vertices[3]);
        $algorithm->setSteps(1);
        $algorithm->calculate();

        $this->assertIsArray($algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Walk::class, $algorithm->getResults());
        $this->assertCount(1, $algorithm->getResults());
        $this->assertEquals(1, $algorithm->countResults());
        $this->assertEquals(1, $algorithm->getShortestResult()->getTotalWeight());
    }
}
