<?php

namespace Graphita\Graphita\Tests;

use Exception;
use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Vertex;
use Graphita\Graphita\Circuit;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CircuitTest extends TestCase
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

    public function testEmptyCircuit()
    {
        $circuit = new Circuit($this->graph);

        $this->assertEquals($this->graph, $circuit->getGraph());

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(0, $circuit->getVertices());
        $this->assertEquals(0, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(0, $circuit->getEdges());
        $this->assertEquals(0, $circuit->countEdges());

        $this->assertEquals(0, $circuit->getTotalWeight());

        $this->assertTrue($circuit->canRepeatVertices());
        $this->assertFalse($circuit->canRepeatEdges());
        $this->assertTrue($circuit->isLoop());

        $this->assertIsArray($circuit->getAttributes());
        $this->assertEmpty($circuit->getAttributes());
    }

    public function testGetCircuitAttributes()
    {
        $circuit = new Circuit($this->graph, ['name' => 'Euler Circuit']);

        $this->assertIsArray($circuit->getAttributes());
        $this->assertCount(1, $circuit->getAttributes());
        $this->assertEquals('Euler Circuit', $circuit->getAttribute('name'));
        $this->assertEquals('Euler Circuit', $circuit->getAttribute('name', 'Leonhard Circuit'));
        $this->assertNull($circuit->getAttribute('color'));
        $this->assertEquals('Red', $circuit->getAttribute('color', 'Red'));
    }

    public function testSetCircuitAttribute()
    {
        $circuit = new Circuit($this->graph);
        $circuit->setAttribute('name', 'Euler Circuit');

        $this->assertIsArray($circuit->getAttributes());
        $this->assertCount(1, $circuit->getAttributes());
        $this->assertEquals('Euler Circuit', $circuit->getAttribute('name'));
    }

    public function testSetCircuitAttributes()
    {
        $circuit = new Circuit($this->graph);
        $circuit->setAttributes(['name' => 'Euler Circuit']);

        $this->assertIsArray($circuit->getAttributes());
        $this->assertCount(1, $circuit->getAttributes());
        $this->assertEquals('Euler Circuit', $circuit->getAttribute('name'));
    }

    public function testRemoveCircuitAttribute()
    {
        $circuit = new Circuit($this->graph, ['name' => 'Euler Circuit']);
        $circuit->removeAttribute('name');

        $this->assertIsArray($circuit->getAttributes());
        $this->assertCount(0, $circuit->getAttributes());
        $this->assertNull($circuit->getAttribute('name'));
    }

    public function testEmptyCircuitAttributes()
    {
        $circuit = new Circuit($this->graph, ['name' => 'Euler Circuit']);
        $circuit->emptyAttributes();

        $this->assertIsArray($circuit->getAttributes());
        $this->assertCount(0, $circuit->getAttributes());
        $this->assertNull($circuit->getAttribute('name'));
    }

    public function testAddVerticesWithArrayOfNonVertex()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be array of Vertex !');

        $circuit = new Circuit($this->graph);
        $circuit->addVertices([1, 2, 3]);
    }

    public function testAddVerticesWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $circuit = new Circuit($this->graph);
        $circuit->addVertices([$anotherVertex]);
    }

    public function testAddVerticesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! Vertex ' . $this->vertices[2]->getId() . ' does not have Neighbor Vertex ' . $this->vertices[4]->getId() . ' !');

        $circuit = new Circuit($this->graph);
        $circuit->addVertices([
            $this->vertices[2],
            $this->vertices[4],
        ]);
    }

    public function testAddVerticesWithUnknownSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown steps ! There are more than one Edges from ' . $this->vertices[4]->getId() . ' to ' . $this->vertices[1]->getId() . ' !');

        $circuit = new Circuit($this->graph);
        $circuit->addVertices([
            $this->vertices[4],
            $this->vertices[1],
        ]);
    }

    public function testAddVerticesWithoutLoop()
    {
        $circuit = new Circuit($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('First Vertex & Last Vertex must be same in Loop !');

        $circuit->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[4],
        ]);
    }

    public function testAddVertices()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[1],
        ]);

        $this->assertIsArray($circuit->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $circuit->getVertices());
        $this->assertCount(4, $circuit->getVertices());
        $this->assertEquals(4, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(3, $circuit->getEdges());
        $this->assertEquals(3, $circuit->countEdges());
        $this->assertEquals($this->edges['1-2'], $circuit->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $circuit->getEdges()[1]);
        $this->assertEquals($this->edges['3-1'], $circuit->getEdges()[2]);
    }

    public function testAddVertex()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addVertex($this->vertices[1]);

        $this->assertIsArray($circuit->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $circuit->getVertices());
        $this->assertCount(1, $circuit->getVertices());
        $this->assertEquals(1, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(0, $circuit->getEdges());
        $this->assertEquals(0, $circuit->countEdges());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('First Vertex & Last Vertex must be same in Loop !');

        $circuit->addVertex($this->vertices[2]);
    }

    public function testAddEdgesWithArrayOfNonEdge()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be array of AbstractEdge !');

        $circuit = new Circuit($this->graph);
        $circuit->addEdges([1, 2, 3]);
    }

    public function testAddEdgesWithOutsideOfGraphEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $circuit = new Circuit($this->graph);
        $circuit->addEdges([$anotherEdge]);
    }

    public function testAddEdgesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! There is no common Vertex between Edge ' . $this->edges['1-2']->getId() . ' and ' . $this->edges['3-4']->getId() . ' !');

        $circuit = new Circuit($this->graph);
        $circuit->addEdges([
            $this->edges['1-2'],
            $this->edges['3-4'],
        ]);
    }

    public function testAddDuplicateEdges()
    {
        $circuit = new Circuit($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be unique !');

        $circuit->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['1-2'],
        ]);
    }

    public function testAddEdgesWithoutLoop()
    {
        $circuit = new Circuit($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('First Vertex & Last Vertex must be same in Loop !');

        $circuit->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['3-4'],
        ]);
    }

    public function testAddEdges()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['3-1'],
        ]);

        $this->assertIsArray($circuit->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $circuit->getEdges());
        $this->assertCount(3, $circuit->getEdges());
        $this->assertEquals(3, $circuit->countEdges());

        $this->assertIsArray($circuit->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $circuit->getVertices());
        $this->assertCount(4, $circuit->getVertices());
        $this->assertEquals(4, $circuit->countVertices());
        $this->assertEquals($this->vertices[1], $circuit->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $circuit->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $circuit->getVertices()[2]);
        $this->assertEquals($this->vertices[1], $circuit->getVertices()[3]);
    }

    public function testAddEdge()
    {
        $circuit = new Circuit($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('First Vertex & Last Vertex must be same in Loop !');

        $circuit->addEdge($this->edges['1-2']);
    }

    public function testGetTotalWeight()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[1],
        ]);

        $this->assertEquals(3, $circuit->getTotalWeight());
    }
}
