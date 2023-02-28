<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Vertex;
use Graphita\Graphita\Path;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    private Graph $graph;
    private array $vertices = [];
    private array $edges = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->graph = new Graph();

        $this->vertices[1] = $this->graph->createVertex(1);
        $this->vertices[2] = $this->graph->createVertex(2);
        $this->vertices[3] = $this->graph->createVertex(3);
        $this->vertices[4] = $this->graph->createVertex(4);

        $this->edges['1-2'] = $this->graph->createUndirectedEdge($this->vertices[1], $this->vertices[2]);
        $this->edges['2-3'] = $this->graph->createUndirectedEdge($this->vertices[2], $this->vertices[3]);
        $this->edges['3-4'] = $this->graph->createUndirectedEdge($this->vertices[3], $this->vertices[4]);
        $this->edges['4-1-1'] = $this->graph->createUndirectedEdge($this->vertices[4], $this->vertices[1]);
        $this->edges['4-1-2'] = $this->graph->createUndirectedEdge($this->vertices[4], $this->vertices[1]);
    }

    public function testEmptyPath()
    {
        $path = new Path($this->graph);

        $this->assertEquals($this->graph, $path->getGraph());

        $this->assertIsArray($path->getVertices());
        $this->assertCount(0, $path->getVertices());
        $this->assertEquals(0, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertCount(0, $path->getEdges());
        $this->assertEquals(0, $path->countEdges());

        $this->assertEquals(0, $path->getTotalWeight());

        $this->assertFalse($path->canRepeatVertices());
        $this->assertFalse($path->canRepeatEdges());
        $this->assertFalse($path->isLoop());

        $this->assertIsArray($path->getAttributes());
        $this->assertEmpty($path->getAttributes());
    }

    public function testGetPathAttributes()
    {
        $path = new Path($this->graph, ['name' => 'Euler Path']);

        $this->assertIsArray($path->getAttributes());
        $this->assertCount(1, $path->getAttributes());
        $this->assertEquals('Euler Path', $path->getAttribute('name'));
        $this->assertEquals('Euler Path', $path->getAttribute('name', 'Leonhard Path'));
        $this->assertNull($path->getAttribute('color'));
        $this->assertEquals('Red', $path->getAttribute('color', 'Red'));
    }

    public function testSetPathAttribute()
    {
        $path = new Path($this->graph);
        $path->setAttribute('name', 'Euler Path');

        $this->assertIsArray($path->getAttributes());
        $this->assertCount(1, $path->getAttributes());
        $this->assertEquals('Euler Path', $path->getAttribute('name'));
    }

    public function testSetPathAttributes()
    {
        $path = new Path($this->graph);
        $path->setAttributes(['name' => 'Euler Path']);

        $this->assertIsArray($path->getAttributes());
        $this->assertCount(1, $path->getAttributes());
        $this->assertEquals('Euler Path', $path->getAttribute('name'));
    }

    public function testRemovePathAttribute()
    {
        $path = new Path($this->graph, ['name' => 'Euler Path']);
        $path->removeAttribute('name');

        $this->assertIsArray($path->getAttributes());
        $this->assertCount(0, $path->getAttributes());
        $this->assertNull($path->getAttribute('name'));
    }

    public function testEmptyPathAttributes()
    {
        $path = new Path($this->graph, ['name' => 'Euler Path']);
        $path->emptyAttributes();

        $this->assertIsArray($path->getAttributes());
        $this->assertCount(0, $path->getAttributes());
        $this->assertNull($path->getAttribute('name'));
    }

    public function testAddVerticesWithArrayOfNonVertex()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be array of Vertex !');

        $path = new Path($this->graph);
        $path->addVertices([1, 2, 3]);
    }

    public function testAddVerticesWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $path = new Path($this->graph);
        $path->addVertices([$anotherVertex]);
    }

    public function testAddVerticesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! Vertex ' . $this->vertices[1]->getId() . ' does not have Neighbor Vertex ' . $this->vertices[3]->getId() . ' !');

        $path = new Path($this->graph);
        $path->addVertices([
            $this->vertices[1],
            $this->vertices[3],
        ]);
    }

    public function testAddVerticesWithUnknownSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown steps ! There are more than one Edges from ' . $this->vertices[4]->getId() . ' to ' . $this->vertices[1]->getId() . ' !');

        $path = new Path($this->graph);
        $path->addVertices([
            $this->vertices[4],
            $this->vertices[1],
        ]);
    }

    public function testAddDuplicateVertices()
    {
        $path = new Path($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be unique !');

        $path->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[4],
            $this->vertices[1],
        ]);
    }

    public function testAddVertices()
    {
        $path = new Path($this->graph);
        $path->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[4],
        ]);

        $this->assertIsArray($path->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
        $this->assertCount(4, $path->getVertices());
        $this->assertEquals(4, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertCount(3, $path->getEdges());
        $this->assertEquals(3, $path->countEdges());
        $this->assertEquals($this->edges['1-2'], $path->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $path->getEdges()[1]);
        $this->assertEquals($this->edges['3-4'], $path->getEdges()[2]);
    }

    public function testAddVertex()
    {
        $path = new Path($this->graph);
        $path->addVertex($this->vertices[1]);

        $this->assertIsArray($path->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
        $this->assertCount(1, $path->getVertices());
        $this->assertEquals(1, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertCount(0, $path->getEdges());
        $this->assertEquals(0, $path->countEdges());

        $path->addVertex($this->vertices[2]);

        $this->assertIsArray($path->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
        $this->assertCount(2, $path->getVertices());
        $this->assertEquals(2, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $path->getEdges());
        $this->assertCount(1, $path->getEdges());
        $this->assertEquals(1, $path->countEdges());
        $this->assertEquals($this->edges['1-2'], $path->getEdges()[0]);

        $path->addVertex($this->vertices[3]);

        $this->assertIsArray($path->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
        $this->assertCount(3, $path->getVertices());
        $this->assertEquals(3, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $path->getEdges());
        $this->assertCount(2, $path->getEdges());
        $this->assertEquals(2, $path->countEdges());
        $this->assertEquals($this->edges['1-2'], $path->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $path->getEdges()[1]);

        $path->addVertex($this->vertices[4]);

        $this->assertIsArray($path->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
        $this->assertCount(4, $path->getVertices());
        $this->assertEquals(4, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $path->getEdges());
        $this->assertCount(3, $path->getEdges());
        $this->assertEquals(3, $path->countEdges());
        $this->assertEquals($this->edges['1-2'], $path->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $path->getEdges()[1]);
        $this->assertEquals($this->edges['3-4'], $path->getEdges()[2]);
    }

    public function testAddEdgesWithArrayOfNonEdge()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be array of AbstractEdge !');

        $path = new Path($this->graph);
        $path->addEdges([1, 2, 3]);
    }

    public function testAddEdgesWithOutsideOfGraphEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $path = new Path($this->graph);
        $path->addEdges([$anotherEdge]);
    }

    public function testAddEdgesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! There is no common Vertex between Edge ' . $this->edges['1-2']->getId() . ' and ' . $this->edges['3-4']->getId() . ' !');

        $path = new Path($this->graph);
        $path->addEdges([
            $this->edges['1-2'],
            $this->edges['3-4'],
        ]);
    }

    public function testAddDuplicateEdges()
    {
        $path = new Path($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be unique !');

        $path->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['1-2'],
        ]);
    }

    public function testAddEdges()
    {
        $path = new Path($this->graph);
        $path->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['3-4'],
        ]);

        $this->assertIsArray($path->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $path->getEdges());
        $this->assertCount(3, $path->getEdges());
        $this->assertEquals(3, $path->countEdges());

        $this->assertIsArray($path->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
        $this->assertCount(4, $path->getVertices());
        $this->assertEquals(4, $path->countVertices());
        $this->assertEquals($this->vertices[1], $path->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $path->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $path->getVertices()[2]);
        $this->assertEquals($this->vertices[4], $path->getVertices()[3]);
    }

    public function testAddEdge()
    {
        $path = new Path($this->graph);
        $path->addEdge($this->edges['1-2']);

        $this->assertIsArray($path->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $path->getEdges());
        $this->assertCount(1, $path->getEdges());
        $this->assertEquals(1, $path->countEdges());

        $this->assertIsArray($path->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
        $this->assertCount(2, $path->getVertices());
        $this->assertEquals(2, $path->countVertices());
        $this->assertEquals($this->vertices[1], $path->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $path->getVertices()[1]);

        $path->addEdge($this->edges['2-3']);

        $this->assertIsArray($path->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $path->getEdges());
        $this->assertCount(2, $path->getEdges());
        $this->assertEquals(2, $path->countEdges());

        $this->assertIsArray($path->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
        $this->assertCount(3, $path->getVertices());
        $this->assertEquals(3, $path->countVertices());
        $this->assertEquals($this->vertices[1], $path->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $path->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $path->getVertices()[2]);

        $path->addEdge($this->edges['3-4']);

        $this->assertIsArray($path->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $path->getEdges());
        $this->assertCount(3, $path->getEdges());
        $this->assertEquals(3, $path->countEdges());

        $this->assertIsArray($path->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
        $this->assertCount(4, $path->getVertices());
        $this->assertEquals(4, $path->countVertices());
        $this->assertEquals($this->vertices[1], $path->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $path->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $path->getVertices()[2]);
        $this->assertEquals($this->vertices[4], $path->getVertices()[3]);
    }

    public function testGetTotalWeight()
    {
        $path = new Path($this->graph);
        $path->addVertex($this->vertices[1]);

        $this->assertEquals(0, $path->getTotalWeight());

        $path->addVertex($this->vertices[2]);

        $this->assertEquals(1, $path->getTotalWeight());

        $path->addVertex($this->vertices[3]);

        $this->assertEquals(2, $path->getTotalWeight());

        $path->addVertex($this->vertices[4]);

        $this->assertEquals(3, $path->getTotalWeight());
    }
}
