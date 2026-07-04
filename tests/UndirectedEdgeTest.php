<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Graph;
use PHPUnit\Framework\TestCase;

class UndirectedEdgeTest extends TestCase
{
    public function testGetUndirectedEdgeEndpointIds()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $edge = $graph->createUndirectedEdge('1', '2');

        $this->assertContainsOnly('string', $edge->getEndpointIds());
        $this->assertCount(2, $edge->getEndpointIds());
        $this->assertTrue($edge->hasVertexId('1'));
        $this->assertTrue($edge->hasVertexId('2'));
    }
}