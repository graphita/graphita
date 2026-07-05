<?php

namespace Graphita\Graphita\Tests\Walks;

use Exception;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Walks\Trail;
use PHPUnit\Framework\TestCase;

class TrailTest extends TestCase
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

        $this->edges['1-2-A'] = $this->graph->createUndirectedEdge('1', '2')->getId();
        $this->edges['1-2-B'] = $this->graph->createUndirectedEdge('1', '2')->getId();
        $this->edges['2-3'] = $this->graph->createUndirectedEdge('2', '3')->getId();
    }

    public function testAddStepWithRepeatEdgeThrowsException()
    {
        $trail = new Trail($this->graph);
        $trail->addStep('1');
        $trail->addStep('2', $this->edges['1-2-A']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("You can't Repeat Edge!");

        $trail->addStep('1', $this->edges['1-2-A']);
    }

    public function testTrailAllowsRepeatingVerticesButNotEdges()
    {
        $trail = new Trail($this->graph);
        $trail->addStep('1');
        $trail->addStep('2', $this->edges['1-2-A']);
        $trail->addStep('1', $this->edges['1-2-B']);

        $this->assertCount(3, $trail->getVertices());
        $this->assertCount(2, $trail->getEdges());
    }
}