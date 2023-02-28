<?php

namespace Graphita\Graphita\Tests;

use Exception;
use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Vertex;
use Graphita\Graphita\Cycle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CycleTest extends TestCase
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
        $this->edges['3-1'] = $this->graph->createUndirectedEdge($this->vertices[3], $this->vertices[1]);
    }

    public function testEmptyCycle()
    {
        $cycle = new Cycle($this->graph);

        $this->assertEquals($this->graph, $cycle->getGraph());

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(0, $cycle->getVertices());
        $this->assertEquals(0, $cycle->countVertices());

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(0, $cycle->getEdges());
        $this->assertEquals(0, $cycle->countEdges());

        $this->assertEquals(0, $cycle->getTotalWeight());

        $this->assertFalse($cycle->canRepeatVertices());
        $this->assertFalse($cycle->canRepeatEdges());
        $this->assertTrue($cycle->isLoop());

        $this->assertIsArray($cycle->getAttributes());
        $this->assertEmpty($cycle->getAttributes());
    }

    public function testGetCycleAttributes()
    {
        $cycle = new Cycle($this->graph, ['name' => 'Euler Cycle']);

        $this->assertIsArray($cycle->getAttributes());
        $this->assertCount(1, $cycle->getAttributes());
        $this->assertEquals('Euler Cycle', $cycle->getAttribute('name'));
        $this->assertEquals('Euler Cycle', $cycle->getAttribute('name', 'Leonhard Cycle'));
        $this->assertNull($cycle->getAttribute('color'));
        $this->assertEquals('Red', $cycle->getAttribute('color', 'Red'));
    }

    public function testSetCycleAttribute()
    {
        $cycle = new Cycle($this->graph);
        $cycle->setAttribute('name', 'Euler Cycle');

        $this->assertIsArray($cycle->getAttributes());
        $this->assertCount(1, $cycle->getAttributes());
        $this->assertEquals('Euler Cycle', $cycle->getAttribute('name'));
    }

    public function testSetCycleAttributes()
    {
        $cycle = new Cycle($this->graph);
        $cycle->setAttributes(['name' => 'Euler Cycle']);

        $this->assertIsArray($cycle->getAttributes());
        $this->assertCount(1, $cycle->getAttributes());
        $this->assertEquals('Euler Cycle', $cycle->getAttribute('name'));
    }

    public function testRemoveCycleAttribute()
    {
        $cycle = new Cycle($this->graph, ['name' => 'Euler Cycle']);
        $cycle->removeAttribute('name');

        $this->assertIsArray($cycle->getAttributes());
        $this->assertCount(0, $cycle->getAttributes());
        $this->assertNull($cycle->getAttribute('name'));
    }

    public function testEmptyCycleAttributes()
    {
        $cycle = new Cycle($this->graph, ['name' => 'Euler Cycle']);
        $cycle->emptyAttributes();

        $this->assertIsArray($cycle->getAttributes());
        $this->assertCount(0, $cycle->getAttributes());
        $this->assertNull($cycle->getAttribute('name'));
    }

    public function testAddVerticesWithArrayOfNonVertex()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be array of Vertex !');

        $cycle = new Cycle($this->graph);
        $cycle->addVertices([1, 2, 3]);
    }

    public function testAddVerticesWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $cycle = new Cycle($this->graph);
        $cycle->addVertices([$anotherVertex]);
    }

    public function testAddVerticesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! Vertex ' . $this->vertices[2]->getId() . ' does not have Neighbor Vertex ' . $this->vertices[4]->getId() . ' !');

        $cycle = new Cycle($this->graph);
        $cycle->addVertices([
            $this->vertices[2],
            $this->vertices[4],
        ]);
    }

    public function testAddVerticesWithUnknownSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown steps ! There are more than one Edges from ' . $this->vertices[4]->getId() . ' to ' . $this->vertices[1]->getId() . ' !');

        $cycle = new Cycle($this->graph);
        $cycle->addVertices([
            $this->vertices[4],
            $this->vertices[1],
        ]);
    }

    public function testAddDuplicateVertices()
    {
        $cycle = new Cycle($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be unique !');

        $cycle->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[4],
            $this->vertices[2],
        ]);
    }

    public function testAddVerticesWithoutLoop()
    {
        $cycle = new Cycle($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('First Vertex & Last Vertex must be same in Loop !');

        $cycle->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[4],
        ]);
    }

    public function testAddVertices()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[1],
        ]);

        $this->assertIsArray($cycle->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $cycle->getVertices());
        $this->assertCount(4, $cycle->getVertices());
        $this->assertEquals(4, $cycle->countVertices());

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(3, $cycle->getEdges());
        $this->assertEquals(3, $cycle->countEdges());
        $this->assertEquals($this->edges['1-2'], $cycle->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $cycle->getEdges()[1]);
        $this->assertEquals($this->edges['3-1'], $cycle->getEdges()[2]);
    }

    public function testAddVertex()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addVertex($this->vertices[1]);

        $this->assertIsArray($cycle->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $cycle->getVertices());
        $this->assertCount(1, $cycle->getVertices());
        $this->assertEquals(1, $cycle->countVertices());

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(0, $cycle->getEdges());
        $this->assertEquals(0, $cycle->countEdges());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('First Vertex & Last Vertex must be same in Loop !');

        $cycle->addVertex($this->vertices[2]);
    }

    public function testAddEdgesWithArrayOfNonEdge()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be array of AbstractEdge !');

        $cycle = new Cycle($this->graph);
        $cycle->addEdges([1, 2, 3]);
    }

    public function testAddEdgesWithOutsideOfGraphEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $cycle = new Cycle($this->graph);
        $cycle->addEdges([$anotherEdge]);
    }

    public function testAddEdgesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! There is no common Vertex between Edge ' . $this->edges['1-2']->getId() . ' and ' . $this->edges['3-4']->getId() . ' !');

        $cycle = new Cycle($this->graph);
        $cycle->addEdges([
            $this->edges['1-2'],
            $this->edges['3-4'],
        ]);
    }

    public function testAddDuplicateEdges()
    {
        $cycle = new Cycle($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be unique !');

        $cycle->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['1-2'],
        ]);
    }

    public function testAddEdgesWithoutLoop()
    {
        $cycle = new Cycle($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('First Vertex & Last Vertex must be same in Loop !');

        $cycle->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['3-4'],
        ]);
    }

    public function testAddEdges()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['3-1'],
        ]);

        $this->assertIsArray($cycle->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $cycle->getEdges());
        $this->assertCount(3, $cycle->getEdges());
        $this->assertEquals(3, $cycle->countEdges());

        $this->assertIsArray($cycle->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $cycle->getVertices());
        $this->assertCount(4, $cycle->getVertices());
        $this->assertEquals(4, $cycle->countVertices());
        $this->assertEquals($this->vertices[1], $cycle->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $cycle->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $cycle->getVertices()[2]);
        $this->assertEquals($this->vertices[1], $cycle->getVertices()[3]);
    }

    public function testAddEdge()
    {
        $cycle = new Cycle($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('First Vertex & Last Vertex must be same in Loop !');

        $cycle->addEdge($this->edges['1-2']);
    }

    public function testGetTotalWeight()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[1],
        ]);

        $this->assertEquals(3, $cycle->getTotalWeight());
    }
}
