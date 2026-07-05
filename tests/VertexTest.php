<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Vertex;
use PHPUnit\Framework\TestCase;

class VertexTest extends TestCase
{
    public function testGetVertexId()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex('1');
        $this->assertEquals('1', $vertex->getId());
    }

    public function testGetEdgesWhenVertexHasNoEdge()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex('1');

        $this->assertEmpty($vertex->getAllEdgeIds());
        $this->assertEmpty($vertex->getIncomingEdgeIds());
        $this->assertEmpty($vertex->getOutgoingEdgeIds());
        $this->assertEquals(0, $vertex->countEdges());
    }

    public function testGetEdgesWhenVertexHasUndirectedEdge()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $edge = $graph->createUndirectedEdge('1', '2');

        $vertex1 = $graph->getVertex('1');

        $this->assertEquals(1, $vertex1->countEdges());
        $this->assertContainsOnly('string', $vertex1->getAllEdgeIds());
        $this->assertTrue($vertex1->hasEdgeId($edge->getId()));
    }

    public function testRemoveEdgeId()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $edge = $graph->createUndirectedEdge('1', '2');

        $vertex = $graph->getVertex('1');
        $this->assertTrue($vertex->hasEdgeId($edge->getId()));

        $vertex->removeEdgeId($edge->getId());
        $this->assertFalse($vertex->hasEdgeId($edge->getId()));
        $this->assertEquals(0, $vertex->countEdges());
    }

    public function testGetIncomingEdgesFromWhenVertexHasEdges()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $graph->createVertex('3');
        $edge1 = $graph->createUndirectedEdge('1', '2');
        $edge2 = $graph->createDirectedEdge('3', '1');

        $incomingFrom2 = $graph->getIncomingEdgesFrom('1', '2');
        $this->assertCount(1, $incomingFrom2);
        $this->assertArrayHasKey($edge1->getId(), $incomingFrom2);

        $incomingFrom3 = $graph->getIncomingEdgesFrom('1', '3');
        $this->assertCount(1, $incomingFrom3);
        $this->assertArrayHasKey($edge2->getId(), $incomingFrom3);
    }

    public function testGetNeighborsWhenVertexHasUndirectedEdge()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $graph->createUndirectedEdge('1', '2');

        $neighbors = $graph->getNeighbors('1');
        $this->assertContainsOnlyInstancesOf(Vertex::class, $neighbors);
        $this->assertCount(1, $neighbors);
        $this->assertTrue($graph->hasNeighbor('1', '2'));
    }

    public function testGetNeighborsWhenVertexHasDirectedEdge()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $graph->createDirectedEdge('1', '2');

        $this->assertTrue($graph->hasNeighbor('1', '2'));
        $this->assertTrue($graph->hasNeighbor('2', '1'));

        $this->assertFalse($graph->hasIncomingNeighbor('1', '2'));
        $this->assertTrue($graph->hasIncomingNeighbor('2', '1'));

        $this->assertTrue($graph->hasOutgoingNeighbor('1', '2'));
        $this->assertFalse($graph->hasOutgoingNeighbor('2', '1'));
    }
}