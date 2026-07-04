<?php

namespace Graphita\Graphita\Tests\Walks;

use Exception;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Path;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
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

        $this->edges['1-2'] = $this->graph->createUndirectedEdge('1', '2')->getId();
        $this->edges['2-3'] = $this->graph->createUndirectedEdge('2', '3')->getId();
    }

    public function testAddStepWithRepeatVerticesThrowsException()
    {
        $path = new Path($this->graph);
        $path->addStep('1');
        $path->addStep('2', $this->edges['1-2']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("You can't Repeat Vertex!");

        $path->addStep('1', $this->edges['1-2']);
    }

    public function testPathAllowsValidSteps()
    {
        $path = new Path($this->graph);
        $path->addStep('1');
        $path->addStep('2', $this->edges['1-2']);
        $path->addStep('3', $this->edges['2-3']);

        $this->assertEquals('1', $path->getFirstStep());
        $this->assertEquals('3', $path->getLastStep());
        $this->assertCount(3, $path->getVertices());
        $this->assertCount(2, $path->getEdges());
    }

    public function testFinishingWhenSourceAndDestinationSame()
    {
        $path = new Path($this->graph);

        $path->addStep('1');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Source Vertex and Destination Vertex shouldn't be Equal for this path type!");

        $path->finish();
    }
}