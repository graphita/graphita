<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Graph;
use PHPUnit\Framework\TestCase;

class DirectedEdgeTest extends TestCase
{
    public function testGetDirectedEdgeAttributes()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $edge = $graph->createDirectedEdge('1', '2', ['name' => 'Route 66']);

        $this->assertEquals('Route 66', $edge->getAttribute('name'));
    }

    public function testGetDirectedEdgeEndpointIds()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $edge = $graph->createDirectedEdge('1', '2');

        $this->assertContainsOnly('string', $edge->getEndpointIds());
        $this->assertCount(2, $edge->getEndpointIds());
        $this->assertTrue($edge->hasVertexId('1'));
        $this->assertTrue($edge->hasVertexId('2'));
    }

    public function testGetAndSetDirectedEdgeWeight()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $edge = $graph->createDirectedEdge('1', '2');

        $this->assertEquals(1.0, $edge->getWeight());
        $edge->setWeight(2.5);
        $this->assertEquals(2.5, $edge->getWeight());
    }

    public function testGetSourceAndDestinationId()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $edge = $graph->createDirectedEdge('1', '2');

        $this->assertEquals('1', $edge->getSourceId());
        $this->assertEquals('2', $edge->getDestinationId());
    }
}