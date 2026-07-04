<?php

namespace Graphita\Graphita\Tests\Walks;

use Exception;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Walk;
use LogicException;
use PHPUnit\Framework\TestCase;

class WalkTest extends TestCase
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
        $this->edges['4-1-1'] = $this->graph->createUndirectedEdge('4', '1')->getId();
        $this->edges['4-1-2'] = $this->graph->createUndirectedEdge('4', '1')->getId();
    }

    public function testEmptyWalk()
    {
        $walk = new Walk($this->graph);

        $this->assertCount(0, $walk->getSteps());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $walk->getFirstStep();
    }

    public function testStarting()
    {
        $walk = new Walk($this->graph);
        $walk->start('1');

        $this->assertEquals('1', $walk->getFirstStep());
        $this->assertEquals('1', $walk->getLastStep());
        $this->assertTrue($walk->isStarted());
        $this->assertFalse($walk->isFinished());
    }

    public function testDuplicateStarting()
    {
        $walk = new Walk($this->graph);
        $walk->start('1');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk started before !');
        $walk->start('2');
    }

    public function testFinishing()
    {
        $walk = new Walk($this->graph);
        $walk->addStep('1');
        $walk->addStep('2', $this->edges['1-2']);
        $walk->finish();
        $this->assertTrue($walk->isFinished());
    }

    public function testAddStepWithOutsideOfGraphVertex()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Vertices must be in the same Graph!');

        $walk = new Walk($this->graph);
        $walk->addStep('99');
    }

    public function testAddStepNotNeighborVertices()
    {
        $walk = new Walk($this->graph);
        $walk->addStep('1');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex has no Edges to new Vertex!');
        $walk->addStep('3');
    }

    public function testAddStepMultiEdgesVertices()
    {
        $walk = new Walk($this->graph);
        $walk->addStep('1');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There are multiple Edges between Prev Vertex and Next Vertex. You must specify throughEdgeId!');
        $walk->addStep('4');
    }

    public function testAddStepWithThroughEdge()
    {
        $walk = new Walk($this->graph);
        $walk->addStep('1');
        $walk->addStep('2', $this->edges['1-2']);

        $this->assertEquals('1', $walk->getFirstStep());
        $this->assertEquals('2', $walk->getLastStep());
        $this->assertCount(2, $walk->getVertices());
        $this->assertCount(1, $walk->getEdges());

        $steps = $walk->getSteps();
        $this->assertEquals('1', $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals('2', $steps[2]);
    }

    public function testGetTotalWeight()
    {
        $walk = new Walk($this->graph);
        $walk->addStep('1');
        $this->assertEquals(0, $walk->getTotalWeight());

        $walk->addStep('2', $this->edges['1-2']);
        $this->assertEquals(1.0, $walk->getTotalWeight());
    }
}