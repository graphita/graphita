<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\DirectedEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\UndirectedEdge;
use Graphita\Graphita\Vertex;
use PHPUnit\Framework\TestCase;

class VertexTest extends TestCase
{
    public function testGetVertexAttributes()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(1, ['name' => 'Vertex 1']);

        $this->assertIsArray($vertex->getAttributes());
        $this->assertCount(1, $vertex->getAttributes());
        $this->assertEquals('Vertex 1', $vertex->getAttribute('name'));
        $this->assertEquals('Vertex 1', $vertex->getAttribute('name', 'Leonhard Graph'));
        $this->assertNull($vertex->getAttribute('color'));
        $this->assertEquals('Red', $vertex->getAttribute('color', 'Red'));
    }

    public function testSetVertexAttribute()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(1);
        $vertex->setAttribute('name', 'Vertex 1');

        $this->assertIsArray($vertex->getAttributes());
        $this->assertCount(1, $vertex->getAttributes());
        $this->assertEquals('Vertex 1', $vertex->getAttribute('name'));
    }

    public function testSetVertexAttributes()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(1);
        $vertex->setAttributes(['name' => 'Vertex 1']);

        $this->assertIsArray($vertex->getAttributes());
        $this->assertCount(1, $vertex->getAttributes());
        $this->assertEquals('Vertex 1', $vertex->getAttribute('name'));
    }

    public function testRemoveVertexAttribute()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex->removeAttribute('name');

        $this->assertIsArray($vertex->getAttributes());
        $this->assertCount(0, $vertex->getAttributes());
        $this->assertNull($vertex->getAttribute('name'));
    }

    public function testGetVertexId()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(1, ['name' => 'Vertex 1']);

        $this->assertEquals(1, $vertex->getId());
    }

    public function testGetVertexGraph()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(1, ['name' => 'Vertex 1']);

        $this->assertEquals($graph, $vertex->getGraph());
    }

    public function testGetEdgesWhenVertexHasNoEdge()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(1, ['name' => 'Vertex 1']);

        $this->assertIsArray($vertex->getEdges());
        $this->assertEmpty($vertex->getEdges());
        $this->assertEquals(0, $vertex->countEdges());

        $this->assertIsArray($vertex->getIncomingEdges());
        $this->assertEmpty($vertex->getIncomingEdges());
        $this->assertEquals(0, $vertex->countIncomingEdges());

        $this->assertIsArray($vertex->getOutgoingEdges());
        $this->assertEmpty($vertex->getOutgoingEdges());
        $this->assertEquals(0, $vertex->countOutgoingEdges());
    }

    public function testGetEdgesWhenVertexHasUndirectedEdge()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2);

        $this->assertIsArray($vertex1->getEdges());
        $this->assertEquals(1, $vertex1->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex1->getEdges());
        $this->assertContainsOnlyInstancesOf(UndirectedEdge::class, $vertex1->getEdges());
        $this->assertTrue($vertex1->hasEdge($undirectedEdge->getId()));

        $this->assertIsArray($vertex2->getEdges());
        $this->assertEquals(1, $vertex2->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex2->getEdges());
        $this->assertContainsOnlyInstancesOf(UndirectedEdge::class, $vertex2->getEdges());
        $this->assertTrue($vertex2->hasEdge($undirectedEdge->getId()));

        $this->assertIsArray($vertex1->getIncomingEdges());
        $this->assertEquals(1, $vertex1->countIncomingEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex1->getIncomingEdges());
        $this->assertContainsOnlyInstancesOf(UndirectedEdge::class, $vertex1->getIncomingEdges());
        $this->assertTrue($vertex1->hasIncomingEdge($undirectedEdge->getId()));

        $this->assertIsArray($vertex2->getIncomingEdges());
        $this->assertEquals(1, $vertex2->countIncomingEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex2->getIncomingEdges());
        $this->assertContainsOnlyInstancesOf(UndirectedEdge::class, $vertex2->getIncomingEdges());
        $this->assertTrue($vertex2->hasIncomingEdge($undirectedEdge->getId()));

        $this->assertIsArray($vertex1->getOutgoingEdges());
        $this->assertEquals(1, $vertex1->countOutgoingEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex1->getOutgoingEdges());
        $this->assertContainsOnlyInstancesOf(UndirectedEdge::class, $vertex1->getOutgoingEdges());
        $this->assertTrue($vertex1->hasOutgoingEdges($undirectedEdge->getId()));

        $this->assertIsArray($vertex2->getOutgoingEdges());
        $this->assertEquals(1, $vertex2->countOutgoingEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex2->getOutgoingEdges());
        $this->assertContainsOnlyInstancesOf(UndirectedEdge::class, $vertex2->getOutgoingEdges());
        $this->assertTrue($vertex2->hasOutgoingEdges($undirectedEdge->getId()));
    }

    public function testGetEdgesWhenVertexHasDirectedEdge()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2);

        $this->assertIsArray($vertex1->getEdges());
        $this->assertEquals(1, $vertex1->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex1->getEdges());
        $this->assertContainsOnlyInstancesOf(DirectedEdge::class, $vertex1->getEdges());
        $this->assertTrue($vertex1->hasEdge($directedEdge->getId()));

        $this->assertIsArray($vertex2->getEdges());
        $this->assertEquals(1, $vertex2->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex2->getEdges());
        $this->assertContainsOnlyInstancesOf(DirectedEdge::class, $vertex2->getEdges());
        $this->assertTrue($vertex2->hasEdge($directedEdge->getId()));

        $this->assertIsArray($vertex1->getIncomingEdges());
        $this->assertEmpty($vertex1->getIncomingEdges());
        $this->assertEquals(0, $vertex1->countIncomingEdges());
        $this->assertFalse($vertex1->hasIncomingEdge($directedEdge->getId()));


        $this->assertIsArray($vertex2->getIncomingEdges());
        $this->assertEquals(1, $vertex2->countIncomingEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex2->getIncomingEdges());
        $this->assertContainsOnlyInstancesOf(DirectedEdge::class, $vertex2->getIncomingEdges());
        $this->assertTrue($vertex2->hasIncomingEdge($directedEdge->getId()));

        $this->assertIsArray($vertex1->getOutgoingEdges());
        $this->assertEquals(1, $vertex1->countOutgoingEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $vertex1->getOutgoingEdges());
        $this->assertContainsOnlyInstancesOf(DirectedEdge::class, $vertex1->getOutgoingEdges());
        $this->assertTrue($vertex1->hasOutgoingEdges($directedEdge->getId()));

        $this->assertIsArray($vertex2->getOutgoingEdges());
        $this->assertEmpty($vertex2->getOutgoingEdges());
        $this->assertEquals(0, $vertex2->countOutgoingEdges());
        $this->assertFalse($vertex2->hasOutgoingEdges($directedEdge->getId()));
    }

    public function testGetIncomingEdgesFromWhenVertexHasNoEdge()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Vertex ' . $vertex1->getId() . ' has no Edge from Vertex ' . $vertex2->getId());
        $unknownEdges = $vertex1->getIncomingEdgesFrom($vertex2);
    }

    public function testGetIncomingEdgesFromWhenVertexHasEdges()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $vertex3 = $graph->createVertex(3, ['name' => 'Vertex 3']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2);

        $incomingEdges = $vertex1->getIncomingEdgesFrom($vertex2);

        $this->assertIsArray($incomingEdges);
        $this->assertCount(1, $incomingEdges);
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $incomingEdges);
        $this->assertArrayHasKey($undirectedEdge->getId(), $incomingEdges);

        $directedEdge = $graph->createUndirectedEdge($vertex3, $vertex1);
        $incomingEdges = $vertex1->getIncomingEdgesFrom($vertex3);

        $this->assertIsArray($incomingEdges);
        $this->assertCount(1, $incomingEdges);
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $incomingEdges);
        $this->assertArrayHasKey($directedEdge->getId(), $incomingEdges);
    }

    public function testGetOutgoingEdgesFromWhenVertexHasNoEdge()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Vertex ' . $vertex1->getId() . ' has no Edge to Vertex ' . $vertex2->getId());
        $unknownEdges = $vertex1->getOutgoingEdgesTo($vertex2);
    }

    public function testGetOutgoingEdgesFromWhenVertexHasEdges()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $vertex3 = $graph->createVertex(3, ['name' => 'Vertex 3']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2);

        $outgoingEdges = $vertex1->getOutgoingEdgesTo($vertex2);

        $this->assertIsArray($outgoingEdges);
        $this->assertCount(1, $outgoingEdges);
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $outgoingEdges);
        $this->assertArrayHasKey($undirectedEdge->getId(), $outgoingEdges);

        $directedEdge = $graph->createUndirectedEdge($vertex1, $vertex3);
        $outgoingEdges = $vertex1->getIncomingEdgesFrom($vertex3);

        $this->assertIsArray($outgoingEdges);
        $this->assertCount(1, $outgoingEdges);
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $outgoingEdges);
        $this->assertArrayHasKey($directedEdge->getId(), $outgoingEdges);
    }

    public function testRemoveEdges()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2);
        $vertex1->removeEdge($undirectedEdge->getId());

        $this->assertIsArray($vertex1->getEdges());
        $this->assertEmpty($vertex1->getEdges());
        $this->assertEquals(0, $vertex1->countEdges());
        $this->assertFalse($vertex1->hasEdge($undirectedEdge->getId()));

        $this->assertIsArray($vertex2->getEdges());
        $this->assertEmpty($vertex2->getEdges());
        $this->assertEquals(0, $vertex2->countEdges());
        $this->assertFalse($vertex2->hasEdge($undirectedEdge->getId()));

        $this->assertIsArray($vertex1->getIncomingEdges());
        $this->assertEmpty($vertex1->getIncomingEdges());
        $this->assertEquals(0, $vertex1->countIncomingEdges());
        $this->assertFalse($vertex1->hasIncomingEdge($undirectedEdge->getId()));

        $this->assertIsArray($vertex2->getIncomingEdges());
        $this->assertEmpty($vertex2->getIncomingEdges());
        $this->assertEquals(0, $vertex2->countIncomingEdges());
        $this->assertFalse($vertex2->hasIncomingEdge($undirectedEdge->getId()));

        $this->assertIsArray($vertex1->getOutgoingEdges());
        $this->assertEmpty($vertex1->getOutgoingEdges());
        $this->assertEquals(0, $vertex1->countOutgoingEdges());
        $this->assertFalse($vertex1->hasOutgoingEdges($undirectedEdge->getId()));

        $this->assertIsArray($vertex2->getOutgoingEdges());
        $this->assertEmpty($vertex2->getOutgoingEdges());
        $this->assertEquals(0, $vertex2->countOutgoingEdges());
        $this->assertFalse($vertex2->hasOutgoingEdges($undirectedEdge->getId()));
    }

    public function testGetNeighborsWhenVertexHasNoEdge()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);

        $this->assertIsArray($vertex1->getNeighbors());
        $this->assertEmpty($vertex1->getNeighbors());
        $this->assertEquals(0, $vertex1->countNeighbors());

        $this->assertIsArray($vertex2->getNeighbors());
        $this->assertEmpty($vertex2->getNeighbors());
        $this->assertEquals(0, $vertex2->countNeighbors());

        $this->assertIsArray($vertex1->getIncomingNeighbors());
        $this->assertEmpty($vertex1->getIncomingNeighbors());
        $this->assertEquals(0, $vertex1->countIncomingNeighbors());

        $this->assertIsArray($vertex2->getIncomingNeighbors());
        $this->assertEmpty($vertex2->getIncomingNeighbors());
        $this->assertEquals(0, $vertex2->countIncomingNeighbors());

        $this->assertIsArray($vertex1->getOutgoingNeighbors());
        $this->assertEmpty($vertex1->getOutgoingNeighbors());
        $this->assertEquals(0, $vertex1->countOutgoingNeighbors());

        $this->assertIsArray($vertex2->getOutgoingNeighbors());
        $this->assertEmpty($vertex2->getOutgoingNeighbors());
        $this->assertEquals(0, $vertex2->countOutgoingNeighbors());
    }

    public function testGetNeighborsWhenVertexHasUndirectedEdge()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2);

        $this->assertIsArray($vertex1->getNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex1->getNeighbors());
        $this->assertEquals(1, $vertex1->countNeighbors());
        $this->assertArrayHasKey($vertex2->getId(), $vertex1->getNeighbors());
        $this->assertTrue($vertex1->hasNeighbor($vertex2->getId()));

        $this->assertIsArray($vertex2->getNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex2->getNeighbors());
        $this->assertEquals(1, $vertex2->countNeighbors());
        $this->assertArrayHasKey($vertex1->getId(), $vertex2->getNeighbors());
        $this->assertTrue($vertex2->hasNeighbor($vertex1->getId()));

        $this->assertIsArray($vertex1->getIncomingNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex1->getIncomingNeighbors());
        $this->assertEquals(1, $vertex1->countIncomingNeighbors());
        $this->assertArrayHasKey($vertex2->getId(), $vertex1->getIncomingNeighbors());
        $this->assertTrue($vertex1->hasIncomingNeighbors($vertex2->getId()));

        $this->assertIsArray($vertex2->getIncomingNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex2->getIncomingNeighbors());
        $this->assertEquals(1, $vertex2->countIncomingNeighbors());
        $this->assertArrayHasKey($vertex1->getId(), $vertex2->getIncomingNeighbors());
        $this->assertTrue($vertex2->hasIncomingNeighbors($vertex1->getId()));

        $this->assertIsArray($vertex1->getOutgoingNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex1->getOutgoingNeighbors());
        $this->assertEquals(1, $vertex1->countOutgoingNeighbors());
        $this->assertArrayHasKey($vertex2->getId(), $vertex1->getOutgoingNeighbors());
        $this->assertTrue($vertex1->hasOutgoingNeighbors($vertex2->getId()));

        $this->assertIsArray($vertex2->getOutgoingNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex2->getOutgoingNeighbors());
        $this->assertEquals(1, $vertex2->countOutgoingNeighbors());
        $this->assertArrayHasKey($vertex1->getId(), $vertex2->getOutgoingNeighbors());
        $this->assertTrue($vertex2->hasOutgoingNeighbors($vertex1->getId()));
    }

    public function testGetNeighborsWhenVertexHasDirectedEdge()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2);

        $this->assertIsArray($vertex1->getNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex1->getNeighbors());
        $this->assertEquals(1, $vertex1->countNeighbors());
        $this->assertArrayHasKey($vertex2->getId(), $vertex1->getNeighbors());
        $this->assertTrue($vertex1->hasNeighbor($vertex2->getId()));

        $this->assertIsArray($vertex2->getNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex2->getNeighbors());
        $this->assertEquals(1, $vertex2->countNeighbors());
        $this->assertArrayHasKey($vertex1->getId(), $vertex2->getNeighbors());
        $this->assertTrue($vertex2->hasNeighbor($vertex1->getId()));

        $this->assertIsArray($vertex1->getIncomingNeighbors());
        $this->assertEmpty($vertex1->getIncomingNeighbors());
        $this->assertEquals(0, $vertex1->countIncomingNeighbors());
        $this->assertFalse($vertex1->hasIncomingNeighbors($vertex2->getId()));

        $this->assertIsArray($vertex2->getIncomingNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex2->getIncomingNeighbors());
        $this->assertEquals(1, $vertex2->countIncomingNeighbors());
        $this->assertArrayHasKey($vertex1->getId(), $vertex2->getIncomingNeighbors());
        $this->assertTrue($vertex2->hasIncomingNeighbors($vertex1->getId()));

        $this->assertIsArray($vertex1->getOutgoingNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex1->getOutgoingNeighbors());
        $this->assertEquals(1, $vertex1->countOutgoingNeighbors());
        $this->assertArrayHasKey($vertex2->getId(), $vertex1->getOutgoingNeighbors());
        $this->assertTrue($vertex1->hasOutgoingNeighbors($vertex2->getId()));

        $this->assertIsArray($vertex2->getOutgoingNeighbors());
        $this->assertEmpty($vertex2->getOutgoingNeighbors());
        $this->assertEquals(0, $vertex2->countOutgoingNeighbors());
        $this->assertFalse($vertex2->hasOutgoingNeighbors($vertex1->getId()));
    }
}
