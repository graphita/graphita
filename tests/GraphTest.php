<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\DirectedEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\UndirectedEdge;
use Graphita\Graphita\Vertex;
use PHPUnit\Framework\TestCase;

class GraphTest extends TestCase
{
    public function testEmptyGraph()
    {
        $graph = new Graph();

        $this->assertIsArray($graph->getVertices());
        $this->assertEmpty($graph->getVertices());
        $this->assertEquals(0, $graph->countVertices());

        $this->assertIsArray($graph->getEdges());
        $this->assertEmpty($graph->getEdges());
        $this->assertEquals(0, $graph->countEdges());

        $this->assertIsArray($graph->getAttributes());
        $this->assertEmpty($graph->getAttributes());
    }

    public function testGetGraphAttributes()
    {
        $graph = new Graph(['name' => 'Euler Graph']);

        $this->assertIsArray($graph->getAttributes());
        $this->assertCount(1, $graph->getAttributes());
        $this->assertEquals('Euler Graph', $graph->getAttribute('name'));
        $this->assertEquals('Euler Graph', $graph->getAttribute('name', 'Leonhard Graph'));
        $this->assertNull($graph->getAttribute('color'));
        $this->assertEquals('Red', $graph->getAttribute('color', 'Red'));
    }

    public function testSetGraphAttribute()
    {
        $graph = new Graph();
        $graph->setAttribute('name', 'Euler Graph');

        $this->assertIsArray($graph->getAttributes());
        $this->assertCount(1, $graph->getAttributes());
        $this->assertEquals('Euler Graph', $graph->getAttribute('name'));
    }

    public function testSetGraphAttributes()
    {
        $graph = new Graph();
        $graph->setAttributes(['name' => 'Euler Graph']);

        $this->assertIsArray($graph->getAttributes());
        $this->assertCount(1, $graph->getAttributes());
        $this->assertEquals('Euler Graph', $graph->getAttribute('name'));
    }

    public function testRemoveGraphAttribute()
    {
        $graph = new Graph(['name' => 'Euler Graph']);
        $graph->removeAttribute('name');

        $this->assertIsArray($graph->getAttributes());
        $this->assertCount(0, $graph->getAttributes());
        $this->assertNull($graph->getAttribute('name'));
    }

    public function testCreateVertex()
    {
        $graph = new Graph(['name' => 'Euler Graph']);
        $vertex = $graph->createVertex(1, ['name' => 'Vertex 1']);

        $this->assertInstanceOf(Vertex::class, $vertex);
        $this->assertIsArray($graph->getVertices());
        $this->assertEquals(1, $graph->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $graph->getVertices());
    }

    public function testCreateDuplicateVertex()
    {
        $graph = new Graph(['name' => 'Euler Graph']);
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Vertex exist !');

        $vertex2 = $graph->createVertex(1, ['name' => 'Vertex 1']);
    }

    public function testRemoveVertex()
    {
        $graph = new Graph(['name' => 'Euler Graph']);
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);

        $this->assertFalse($graph->removeVertex(2));
        $this->assertTrue($graph->removeVertex(1));
        $this->assertIsArray($graph->getVertices());
        $this->assertEquals(0, $graph->countVertices());

        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2, []);

        $this->assertTrue($graph->removeVertex(1));
        $this->assertIsArray($graph->getVertices());
        $this->assertEquals(1, $graph->countVertices());
        $this->assertIsArray($graph->getEdges());
        $this->assertEquals(0, $graph->countEdges());
    }

    public function testCreateUndirectedEdge()
    {
        $graph1 = new Graph(['name' => 'Euler Graph']);
        $vertex1 = $graph1->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph1->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge1 = $graph1->createUndirectedEdge($vertex1, $vertex2, []);

        $this->assertInstanceOf(AbstractEdge::class, $undirectedEdge1);
        $this->assertIsArray($graph1->getEdges());
        $this->assertEquals(1, $graph1->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $graph1->getEdges());
        $this->assertContainsOnlyInstancesOf(UndirectedEdge::class, $graph1->getEdges());
        $this->assertTrue($graph1->hasEdge($vertex1->getId() . '-' . $vertex2->getId()));

        $graph2 = new Graph(['name' => 'Another Euler Graph']);
        $vertex3 = $graph2->createVertex(3, ['name' => 'Vertex 3']);
        $vertex4 = $graph2->createVertex(4, ['name' => 'Vertex 4']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Vertex must be in graph !');
        $undirectedEdge2 = $graph1->createDirectedEdge($vertex3, $vertex4, []);
    }

    public function testCreateDirectedEdge()
    {
        $graph1 = new Graph(['name' => 'Euler Graph']);
        $vertex1 = $graph1->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph1->createVertex(2, ['name' => 'Vertex 2']);
        $directedEdge1 = $graph1->createDirectedEdge($vertex1, $vertex2, []);

        $this->assertInstanceOf(AbstractEdge::class, $directedEdge1);
        $this->assertIsArray($graph1->getEdges());
        $this->assertEquals(1, $graph1->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $graph1->getEdges());
        $this->assertContainsOnlyInstancesOf(DirectedEdge::class, $graph1->getEdges());
        $this->assertTrue($graph1->hasEdge($vertex1->getId() . '-' . $vertex2->getId()));

        $graph2 = new Graph(['name' => 'Another Euler Graph']);
        $vertex3 = $graph2->createVertex(3, ['name' => 'Vertex 3']);
        $vertex4 = $graph2->createVertex(4, ['name' => 'Vertex 4']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Vertex must be in graph !');
        $directedEdge2 = $graph1->createDirectedEdge($vertex3, $vertex4, []);
    }

    public function testRemoveEdge()
    {
        $graph = new Graph(['name' => 'Euler Graph']);
        $vertex1 = $graph->createVertex(1, ['name' => 'Vertex 1']);
        $vertex2 = $graph->createVertex(2, ['name' => 'Vertex 2']);
        $undirectedEdge = $graph->createUndirectedEdge($vertex1, $vertex2, []);

        $this->assertFalse($graph->removeEdge('2-1'));
        $this->assertTrue($graph->removeEdge('1-2'));
        $this->assertIsArray($graph->getEdges());
        $this->assertEquals(0, $graph->countEdges());
    }
}
