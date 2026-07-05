<?php

namespace Graphita\Graphita\Tests\Walks;

use Exception;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Circuit;
use PHPUnit\Framework\TestCase;

class CircuitTest extends TestCase
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
        $this->edges['3-1'] = $this->graph->createUndirectedEdge('3', '1')->getId();
    }

    public function testFinishingWhenSourceAndDestinationNotSame()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep('1');
        $circuit->addStep('2', $this->edges['1-2']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Source Vertex and Destination Vertex must be Equal for a loop!');
        $circuit->finish();
    }

    public function testCircuitAllowsClosingLoop()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep('1');
        $circuit->addStep('2', $this->edges['1-2']);
        $circuit->addStep('3', $this->edges['2-3']);

        $this->assertFalse($circuit->isFinished());

        $circuit->addStep('1', $this->edges['3-1']);
        $circuit->finish();

        $this->assertTrue($circuit->isFinished());
        $this->assertEquals('1', $circuit->getFirstStep());
        $this->assertEquals('1', $circuit->getLastStep());
    }
}