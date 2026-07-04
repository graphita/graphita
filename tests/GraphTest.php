<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\DirectedEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\UndirectedEdge;
use Graphita\Graphita\Vertex;
use LogicException;
use OutOfBoundsException;
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
    }

    public function testGetGraphAttributes()
    {
        $graph = new Graph(['name' => 'Euler Graph']);
        $this->assertEquals('Euler Graph', $graph->getAttribute('name'));
    }

    public function testCreateVertex()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex('1', ['name' => 'Vertex 1']);

        $this->assertInstanceOf(Vertex::class, $vertex);
        $this->assertEquals(1, $graph->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $graph->getVertices());
    }

    public function testCreateDuplicateVertex()
    {
        $graph = new Graph();
        $graph->createVertex('1');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Vertex [1] already exists!');
        $graph->createVertex('1');
    }

    public function testGetVertexAndEdge()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $edge = $graph->createDirectedEdge('1', '2');

        $this->assertInstanceOf(Vertex::class, $graph->getVertex('1'));
        $this->assertInstanceOf(DirectedEdge::class, $graph->getEdge($edge->getId()));
    }

    public function testGetMissingVertexThrowsException()
    {
        $graph = new Graph();
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Vertex [99] does not exist in the graph.');
        $graph->getVertex('99');
    }

    public function testGetMissingEdgeThrowsException()
    {
        $graph = new Graph();
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Edge [e_99] does not exist in the graph.');
        $graph->getEdge('e_99');
    }

    public function testRemoveVertex()
    {
        $graph = new Graph();
        $graph->createVertex('1');

        $this->assertFalse($graph->removeVertex('2'));
        $this->assertTrue($graph->removeVertex('1'));
        $this->assertEquals(0, $graph->countVertices());

        $graph->createVertex('1');
        $graph->createVertex('2');
        $graph->createUndirectedEdge('1', '2');

        $this->assertTrue($graph->removeVertex('1'));
        $this->assertEquals(1, $graph->countVertices());
        $this->assertEquals(0, $graph->countEdges());
    }

    public function testCreateUndirectedEdge()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $undirectedEdge = $graph->createUndirectedEdge('1', '2');

        $this->assertInstanceOf(AbstractEdge::class, $undirectedEdge);
        $this->assertEquals(1, $graph->countEdges());
        $this->assertTrue($graph->hasEdge($undirectedEdge->getId()));
    }

    public function testCreateUndirectedEdgeFromTwoGraph()
    {
        $graph1 = new Graph();
        $graph1->createVertex('1');

        $graph2 = new Graph();
        $graph2->createVertex('3');
        $graph2->createVertex('4');

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Both Vertices must exist in the graph.');
        $graph1->createUndirectedEdge('3', '4');
    }

    public function testCreateDirectedEdge()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $directedEdge = $graph->createDirectedEdge('1', '2');

        $this->assertInstanceOf(AbstractEdge::class, $directedEdge);
        $this->assertEquals(1, $graph->countEdges());
        $this->assertTrue($graph->hasEdge($directedEdge->getId()));
    }

    public function testRemoveEdge()
    {
        $graph = new Graph();
        $graph->createVertex('1');
        $graph->createVertex('2');
        $undirectedEdge = $graph->createUndirectedEdge('1', '2');

        $this->assertFalse($graph->removeEdge('non-existent'));
        $this->assertTrue($graph->removeEdge($undirectedEdge->getId()));
        $this->assertEquals(0, $graph->countEdges());
    }
}