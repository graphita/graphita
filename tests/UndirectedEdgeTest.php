<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Graph;
use Graphita\Graphita\UndirectedEdge;
use Graphita\Graphita\Vertex;
use PHPUnit\Framework\TestCase;

class UndirectedEdgeTest extends TestCase
{
    public function testGetUndirectedEdgeAttributes()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $vertex3 = $graph->createVertex(3, ['name' => 'Vertex 3']);
        $undirectedEdge1 = $graph->createUndirectedEdge($vertex1, $vertex2);

        $this->assertIsArray($undirectedEdge1->getAttributes());
        $this->assertEmpty($undirectedEdge1->getAttributes());

        $undirectedEdge2 = $graph->createUndirectedEdge($vertex2, $vertex3, ['name' => 'Route 66']);

        $this->assertIsArray($undirectedEdge2->getAttributes());
        $this->assertCount(1, $undirectedEdge2->getAttributes());
        $this->assertEquals('Route 66', $undirectedEdge2->getAttribute('name'));
        $this->assertEquals('Route 66', $undirectedEdge2->getAttribute('name', 'Route 67'));
        $this->assertNull($undirectedEdge2->getAttribute('color'));
        $this->assertEquals('Red', $undirectedEdge2->getAttribute('color', 'Red'));
    }

    public function testSetUndirectedEdgeAttribute()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2);
        $undirectedEdge->setAttribute('name', 'Route 66');

        $this->assertIsArray($undirectedEdge->getAttributes());
        $this->assertCount(1, $undirectedEdge->getAttributes());
        $this->assertEquals('Route 66', $undirectedEdge->getAttribute('name'));
        $this->assertEquals('Route 66', $undirectedEdge->getAttribute('name', 'Route 67'));
        $this->assertNull($undirectedEdge->getAttribute('color'));
        $this->assertEquals('Red', $undirectedEdge->getAttribute('color', 'Red'));
    }

    public function testSetUndirectedEdgeAttributes()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2);
        $undirectedEdge->setAttributes(['name' => 'Route 66']);

        $this->assertIsArray($undirectedEdge->getAttributes());
        $this->assertCount(1, $undirectedEdge->getAttributes());
        $this->assertEquals('Route 66', $undirectedEdge->getAttribute('name'));
        $this->assertEquals('Route 66', $undirectedEdge->getAttribute('name', 'Route 67'));
        $this->assertNull($undirectedEdge->getAttribute('color'));
        $this->assertEquals('Red', $undirectedEdge->getAttribute('color', 'Red'));
    }

    public function testRemoveUndirectedEdgeAttribute()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);
        $undirectedEdge->removeAttribute('name');

        $this->assertIsArray($undirectedEdge->getAttributes());
        $this->assertCount(0, $undirectedEdge->getAttributes());
        $this->assertNull($undirectedEdge->getAttribute('name'));
    }

    public function testGetUndirectedEdgeId()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertStringStartsWith($vertex1->getId() . '-' . $vertex2->getId() . '-', $undirectedEdge->getId());
    }

    public function testGetUndirectedEdgeGraph()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertEquals($graph, $undirectedEdge->getGraph());
    }

    public function testGetUndirectedEdgeVertices()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertIsArray($undirectedEdge->getVertices());
        $this->assertCount(2, $undirectedEdge->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $undirectedEdge->getVertices());
        $this->assertArrayHasKey($vertex1->getId(), $undirectedEdge->getVertices());
        $this->assertArrayHasKey($vertex2->getId(), $undirectedEdge->getVertices());
    }

    public function testGetAndSetUndirectedEdgeWeight()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertIsNumeric($undirectedEdge->getWeight());
        $this->assertEquals(1, $undirectedEdge->getWeight());

        $undirectedEdge->setWeight(2.5);

        $this->assertIsNumeric($undirectedEdge->getWeight());
        $this->assertEquals(2.5, $undirectedEdge->getWeight());
    }
}
