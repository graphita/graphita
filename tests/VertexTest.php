<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Abstracts\AbstractEdge;
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

        $this->assertEquals( $graph, $vertex->getGraph() );
    }

    public function testGetEdges()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(1, ['name' => 'Vertex 1']);

        $this->assertIsArray($vertex->getEdges());
        $this->assertEmpty($vertex->getEdges());
        $this->assertEquals(0, $vertex->countEdges());
    }

    public function testAddEdges()
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
    }

    public function testRemoveEdges()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2);
        $vertex1->removeEdge($undirectedEdge->getId());

        $this->assertIsArray($vertex1->getEdges());
        $this->assertEquals(0, $vertex1->countEdges());
        $this->assertFalse($vertex1->hasEdge($undirectedEdge->getId()));

        $this->assertIsArray($vertex2->getEdges());
        $this->assertEquals(0, $vertex2->countEdges());
        $this->assertFalse($vertex2->hasEdge($undirectedEdge->getId()));
    }

    public function testGetNeighbors()
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

        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2);

        $this->assertIsArray($vertex1->getNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex1->getNeighbors());
        $this->assertEquals(1, $vertex1->countNeighbors());
        $this->assertArrayHasKey($vertex2->getId(), $vertex1->getNeighbors());

        $this->assertIsArray($vertex2->getNeighbors());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $vertex2->getNeighbors());
        $this->assertEquals(1, $vertex2->countNeighbors());
        $this->assertArrayHasKey($vertex1->getId(), $vertex2->getNeighbors());
    }
}
