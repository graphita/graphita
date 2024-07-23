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
    }

    public function testEmptyCircuit()
    {
        $circuit = new Circuit($this->graph);

        $this->assertEquals($this->graph, $circuit->getGraph());

        $this->assertIsArray($circuit->getSteps());
        $this->assertCount(0, $circuit->getSteps());
        $this->assertEquals(0, $circuit->countSteps());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $firstStep = $circuit->getFirstStep();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $lastStep = $circuit->getLastStep();

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(0, $circuit->getVertices());
        $this->assertEquals(0, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(0, $circuit->getEdges());
        $this->assertEquals(0, $circuit->countEdges());

        $this->assertFalse($circuit->isStarted());
        $this->assertFalse($circuit->isFinished());

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

    public function testStartingViaConstructor()
    {
        $circuit = new Circuit($this->graph, $this->vertices[1]);

        $this->assertEquals($this->graph, $circuit->getGraph());

        $this->assertIsArray($circuit->getSteps());
        $this->assertCount(1, $circuit->getSteps());
        $this->assertEquals(1, $circuit->countSteps());
        $this->assertEquals($this->vertices[1], $circuit->getFirstStep());
        $this->assertEquals($this->vertices[1], $circuit->getLastStep());

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(1, $circuit->getVertices());
        $this->assertEquals(1, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(0, $circuit->getEdges());
        $this->assertEquals(0, $circuit->countEdges());

        $this->assertTrue($circuit->isStarted());
        $this->assertFalse($circuit->isFinished());

        $this->assertEquals(0, $circuit->getTotalWeight());
    }

    public function testStarting()
    {
        $circuit = new Circuit($this->graph);
        $circuit->start($this->vertices[1]);

        $this->assertEquals($this->graph, $circuit->getGraph());

        $this->assertIsArray($circuit->getSteps());
        $this->assertCount(1, $circuit->getSteps());
        $this->assertEquals(1, $circuit->countSteps());
        $this->assertEquals($this->vertices[1], $circuit->getFirstStep());
        $this->assertEquals($this->vertices[1], $circuit->getLastStep());

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(1, $circuit->getVertices());
        $this->assertEquals(1, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(0, $circuit->getEdges());
        $this->assertEquals(0, $circuit->countEdges());

        $this->assertTrue($circuit->isStarted());
        $this->assertFalse($circuit->isFinished());

        $this->assertEquals(0, $circuit->getTotalWeight());
    }

    public function testDuplicateStarting()
    {
        $circuit = new Circuit($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk started before !');

        $circuit->start($this->vertices[1]);
        $circuit->start($this->vertices[2]);
    }

    public function testFinishingWhenSourceAndDestinationNotSame()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);
        $circuit->addStep($this->vertices[2]);
        $circuit->addStep($this->vertices[3]);
        $circuit->addStep($this->vertices[4]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Source Vertex and Destination Vertex must be Equal !');

        $circuit->finish();
    }

    public function testFinishing()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);
        $circuit->addStep($this->vertices[2]);
        $circuit->addStep($this->vertices[3]);
        $circuit->addStep($this->vertices[4]);

        $this->assertFalse($circuit->isFinished());

        $circuit->addStep($this->vertices[1], $this->edges['4-1-1']);

        $this->assertTrue($circuit->isFinished());

        $circuit->finish();

        $this->assertTrue($circuit->isFinished());
    }

    public function testAddStepWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $circuit = new Circuit($this->graph);
        $circuit->addStep($anotherVertex);
    }

    public function testAddStepWithOutsideOfGraphThroughEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1], $anotherEdge);
    }

    public function testAddStepWhenFinished()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);
        $circuit->addStep($this->vertices[2]);
        $circuit->addStep($this->vertices[3]);
        $circuit->addStep($this->vertices[4]);
        $circuit->addStep($this->vertices[1], $this->edges['4-1-1']);
        $circuit->finish();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is finished before !');

        $circuit->addStep($this->vertices[3]);
    }

    public function testStartingByAddStep()
    {
        $circuit = new Circuit($this->graph);

        $this->assertFalse($circuit->isStarted());

        $circuit->addStep($this->vertices[1]);

        $this->assertTrue($circuit->isStarted());
    }

    public function testAddStepNotNeighborVertices()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex has no Edges to new Vertex !');

        $circuit->addStep($this->vertices[3]);
    }

    public function testAddStepMultiEdgesVertices()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There is many Edges between Prev Vertex and Next Vertex !');

        $circuit->addStep($this->vertices[4]);
    }

    public function testAddStepThroughEdgeThatNotConnectedToPrevVertex()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex is not connected to Through Edge !');

        $circuit->addStep($this->vertices[2], $this->edges['3-4']);
    }

    public function testAddStepThroughEdgeThatNotConnectedToNextVertex()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Next Vertex is not connected to Through Edge !');

        $circuit->addStep($this->vertices[2], $this->edges['4-1-1']);
    }

    public function testAddStepWithRepeatEdge()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);
        $circuit->addStep($this->vertices[2]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You can\'t Repeat Edge !');

        $circuit->addStep($this->vertices[1]);
    }

    public function testAddStepWithoutThroughEdge()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);

        $this->assertIsArray($circuit->getSteps());
        $this->assertCount(1, $circuit->getSteps());
        $this->assertEquals(1, $circuit->countSteps());
        $this->assertEquals($this->vertices[1], $circuit->getFirstStep());
        $this->assertEquals($this->vertices[1], $circuit->getLastStep());

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(1, $circuit->getVertices());
        $this->assertEquals(1, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(0, $circuit->getEdges());
        $this->assertEquals(0, $circuit->countEdges());

        $steps = $circuit->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $circuit->addStep($this->vertices[2]);

        $this->assertIsArray($circuit->getSteps());
        $this->assertCount(3, $circuit->getSteps());
        $this->assertEquals(3, $circuit->countSteps());
        $this->assertEquals($this->vertices[1], $circuit->getFirstStep());
        $this->assertEquals($this->vertices[2], $circuit->getLastStep());

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(2, $circuit->getVertices());
        $this->assertEquals(2, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(1, $circuit->getEdges());
        $this->assertEquals(1, $circuit->countEdges());

        $steps = $circuit->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testAddStepWithThroughEdge()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);

        $this->assertIsArray($circuit->getSteps());
        $this->assertCount(1, $circuit->getSteps());
        $this->assertEquals(1, $circuit->countSteps());
        $this->assertEquals($this->vertices[1], $circuit->getFirstStep());
        $this->assertEquals($this->vertices[1], $circuit->getLastStep());

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(1, $circuit->getVertices());
        $this->assertEquals(1, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(0, $circuit->getEdges());
        $this->assertEquals(0, $circuit->countEdges());

        $steps = $circuit->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $circuit->addStep($this->vertices[2], $this->edges['1-2']);

        $this->assertIsArray($circuit->getSteps());
        $this->assertCount(3, $circuit->getSteps());
        $this->assertEquals(3, $circuit->countSteps());
        $this->assertEquals($this->vertices[1], $circuit->getFirstStep());
        $this->assertEquals($this->vertices[2], $circuit->getLastStep());

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(2, $circuit->getVertices());
        $this->assertEquals(2, $circuit->countVertices());

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(1, $circuit->getEdges());
        $this->assertEquals(1, $circuit->countEdges());

        $steps = $circuit->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testGetTotalWeight()
    {
        $circuit = new Circuit($this->graph);
        $circuit->addStep($this->vertices[1]);

        $this->assertEquals(0, $circuit->getTotalWeight());

        $circuit->addStep($this->vertices[2]);

        $this->assertEquals(1, $circuit->getTotalWeight());

        $circuit->addStep($this->vertices[3]);

        $this->assertEquals(2, $circuit->getTotalWeight());

        $circuit->addStep($this->vertices[4]);

        $this->assertEquals(3, $circuit->getTotalWeight());
    }

    public function testGetVertices()
    {
        $circuit = new Circuit($this->graph);

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(0, $circuit->getVertices());
        $this->assertEquals(0, $circuit->countVertices());

        $circuit->addStep($this->vertices[1]);

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(1, $circuit->getVertices());
        $this->assertEquals(1, $circuit->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $circuit->getVertices());

        $circuit->addStep($this->vertices[2]);

        $this->assertIsArray($circuit->getVertices());
        $this->assertCount(2, $circuit->getVertices());
        $this->assertEquals(2, $circuit->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $circuit->getVertices());
    }

    public function testGetEdges()
    {
        $circuit = new Circuit($this->graph);

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(0, $circuit->getEdges());
        $this->assertEquals(0, $circuit->countEdges());

        $circuit->addStep($this->vertices[1]);

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(0, $circuit->getEdges());
        $this->assertEquals(0, $circuit->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $circuit->getEdges());

        $circuit->addStep($this->vertices[2]);

        $this->assertIsArray($circuit->getEdges());
        $this->assertCount(1, $circuit->getEdges());
        $this->assertEquals(1, $circuit->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $circuit->getEdges());
    }
}
