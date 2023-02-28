<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Graph;
use Graphita\Graphita\DirectedEdge;
use Graphita\Graphita\Vertex;
use PHPUnit\Framework\TestCase;

class DirectedEdgeTest extends TestCase
{
    public function testGetDirectedEdgeAttributes()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $vertex3 = $graph->createVertex(3, ['name' => 'Vertex 3']);
        $directedEdge1 = $graph->createDirectedEdge($vertex1, $vertex2);

        $this->assertIsArray($directedEdge1->getAttributes());
        $this->assertEmpty($directedEdge1->getAttributes());

        $directedEdge2 = $graph->createDirectedEdge($vertex2, $vertex3, ['name' => 'Route 66']);

        $this->assertIsArray($directedEdge2->getAttributes());
        $this->assertCount(1, $directedEdge2->getAttributes());
        $this->assertEquals('Route 66', $directedEdge2->getAttribute('name'));
        $this->assertEquals('Route 66', $directedEdge2->getAttribute('name', 'Route 67'));
        $this->assertNull($directedEdge2->getAttribute('color'));
        $this->assertEquals('Red', $directedEdge2->getAttribute('color', 'Red'));
    }

    public function testSetDirectedEdgeAttribute()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2);
        $directedEdge->setAttribute('name', 'Route 66');

        $this->assertIsArray($directedEdge->getAttributes());
        $this->assertCount(1, $directedEdge->getAttributes());
        $this->assertEquals('Route 66', $directedEdge->getAttribute('name'));
        $this->assertEquals('Route 66', $directedEdge->getAttribute('name', 'Route 67'));
        $this->assertNull($directedEdge->getAttribute('color'));
        $this->assertEquals('Red', $directedEdge->getAttribute('color', 'Red'));
    }

    public function testSetDirectedEdgeAttributes()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2);
        $directedEdge->setAttributes(['name' => 'Route 66']);

        $this->assertIsArray($directedEdge->getAttributes());
        $this->assertCount(1, $directedEdge->getAttributes());
        $this->assertEquals('Route 66', $directedEdge->getAttribute('name'));
        $this->assertEquals('Route 66', $directedEdge->getAttribute('name', 'Route 67'));
        $this->assertNull($directedEdge->getAttribute('color'));
        $this->assertEquals('Red', $directedEdge->getAttribute('color', 'Red'));
    }

    public function testRemoveDirectedEdgeAttribute()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);
        $directedEdge->removeAttribute('name');

        $this->assertIsArray($directedEdge->getAttributes());
        $this->assertCount(0, $directedEdge->getAttributes());
        $this->assertNull($directedEdge->getAttribute('name'));
    }

    public function testEmptyDirectedEdgeAttributes()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);
        $directedEdge->emptyAttributes();

        $this->assertIsArray($directedEdge->getAttributes());
        $this->assertCount(0, $directedEdge->getAttributes());
        $this->assertNull($directedEdge->getAttribute('name'));
    }

    public function testGetDirectedEdgeId()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertStringStartsWith($vertex1->getId() . '-' . $vertex2->getId() . '-', $directedEdge->getId());
    }

    public function testGetDirectedEdgeGraph()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertEquals($graph, $directedEdge->getGraph());
    }

    public function testGetDirectedEdgeVertices()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertIsArray($directedEdge->getVertices());
        $this->assertCount(2, $directedEdge->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $directedEdge->getVertices());
        $this->assertArrayHasKey($vertex1->getId(), $directedEdge->getVertices());
        $this->assertArrayHasKey($vertex2->getId(), $directedEdge->getVertices());
    }

    public function testGetAndSetDirectedEdgeWeight()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertIsNumeric($directedEdge->getWeight());
        $this->assertEquals(1, $directedEdge->getWeight());

        $directedEdge->setWeight(2.5);

        $this->assertIsNumeric($directedEdge->getWeight());
        $this->assertEquals(2.5, $directedEdge->getWeight());
    }

    public function testGetSource()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertInstanceOf(Vertex::class, $directedEdge->getSource());
        $this->assertEquals($vertex1, $directedEdge->getSource());
    }

    public function testGetDestination()
    {
        $graph = new Graph();
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge = $graph->createDirectedEdge($vertex1, $vertex2, ['name' => 'Route 66']);

        $this->assertInstanceOf(Vertex::class, $directedEdge->getDestination());
        $this->assertEquals($vertex2, $directedEdge->getDestination());
    }
}
