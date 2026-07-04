<?php

namespace Graphita\Graphita\Tests\Walks;

use Exception;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Cycle;
use PHPUnit\Framework\TestCase;

class CycleTest extends TestCase
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
        $this->edges['3-1'] = $this->graph->createUndirectedEdge('3', '1')->getId();
        $this->edges['3-4'] = $this->graph->createUndirectedEdge('3', '4')->getId();
    }

    public function testAddStepWithRepeatVerticesThrowsException()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep('1');
        $cycle->addStep('2', $this->edges['1-2']);
        $cycle->addStep('3', $this->edges['2-3']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("You can't Repeat Vertex!");

        $this->graph->createUndirectedEdge('4', '2');
        $cycle->addStep('4', $this->edges['3-4']);
        $cycle->addStep('2');
    }

    public function testCycleAllowsClosingLoop()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep('1');
        $cycle->addStep('2', $this->edges['1-2']);
        $cycle->addStep('3', $this->edges['2-3']);

        $cycle->addStep('1', $this->edges['3-1']);
        $cycle->finish();

        $this->assertTrue($cycle->isFinished());
    }
}