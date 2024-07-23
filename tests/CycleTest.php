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
        $this->edges['4-2'] = $this->graph->createUndirectedEdge($this->vertices[4], $this->vertices[2]);
    }

    public function testEmptyCycle()
    {
        $cycle = new Cycle($this->graph);

        $this->assertEquals($this->graph, $cycle->getGraph());

        $this->assertIsArray($cycle->getSteps());
        $this->assertCount(0, $cycle->getSteps());
        $this->assertEquals(0, $cycle->countSteps());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $firstStep = $cycle->getFirstStep();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $lastStep = $cycle->getLastStep();

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(0, $cycle->getVertices());
        $this->assertEquals(0, $cycle->countVertices());

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(0, $cycle->getEdges());
        $this->assertEquals(0, $cycle->countEdges());

        $this->assertFalse($cycle->isStarted());
        $this->assertFalse($cycle->isFinished());

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

    public function testStarting()
    {
        $cycle = new Cycle($this->graph);
        $cycle->start($this->vertices[1]);

        $this->assertEquals($this->graph, $cycle->getGraph());

        $this->assertIsArray($cycle->getSteps());
        $this->assertCount(1, $cycle->getSteps());
        $this->assertEquals(1, $cycle->countSteps());
        $this->assertEquals($this->vertices[1], $cycle->getFirstStep());
        $this->assertEquals($this->vertices[1], $cycle->getLastStep());

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(1, $cycle->getVertices());
        $this->assertEquals(1, $cycle->countVertices());

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(0, $cycle->getEdges());
        $this->assertEquals(0, $cycle->countEdges());

        $this->assertTrue($cycle->isStarted());
        $this->assertFalse($cycle->isFinished());

        $this->assertEquals(0, $cycle->getTotalWeight());
    }

    public function testDuplicateStarting()
    {
        $cycle = new Cycle($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk started before !');

        $cycle->start($this->vertices[1]);
        $cycle->start($this->vertices[2]);
    }

    public function testFinishingWhenSourceAndDestinationNotSame()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);
        $cycle->addStep($this->vertices[2]);
        $cycle->addStep($this->vertices[3]);
        $cycle->addStep($this->vertices[4]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Source Vertex and Destination Vertex must be Equal !');

        $cycle->finish();
    }

    public function testFinishing()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);
        $cycle->addStep($this->vertices[2]);
        $cycle->addStep($this->vertices[3]);
        $cycle->addStep($this->vertices[4]);

        $this->assertFalse($cycle->isFinished());

        $cycle->addStep($this->vertices[1], $this->edges['4-1-1']);

        $this->assertTrue($cycle->isFinished());

        $cycle->finish();

        $this->assertTrue($cycle->isFinished());
    }

    public function testAddStepWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $cycle = new Cycle($this->graph);
        $cycle->addStep($anotherVertex);
    }

    public function testAddStepWithOutsideOfGraphThroughEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1], $anotherEdge);
    }

    public function testAddStepWhenFinished()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);
        $cycle->addStep($this->vertices[2]);
        $cycle->addStep($this->vertices[3]);
        $cycle->addStep($this->vertices[4]);
        $cycle->addStep($this->vertices[1], $this->edges['4-1-1']);
        $cycle->finish();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is finished before !');

        $cycle->addStep($this->vertices[2]);
    }

    public function testStartingByAddStep()
    {
        $cycle = new Cycle($this->graph);

        $this->assertFalse($cycle->isStarted());

        $cycle->addStep($this->vertices[1]);

        $this->assertTrue($cycle->isStarted());
    }

    public function testAddStepNotNeighborVertices()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex has no Edges to new Vertex !');

        $cycle->addStep($this->vertices[3]);
    }

    public function testAddStepMultiEdgesVertices()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There is many Edges between Prev Vertex and Next Vertex !');

        $cycle->addStep($this->vertices[4]);
    }

    public function testAddStepThroughEdgeThatNotConnectedToPrevVertex()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex is not connected to Through Edge !');

        $cycle->addStep($this->vertices[2], $this->edges['3-4']);
    }

    public function testAddStepThroughEdgeThatNotConnectedToNextVertex()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Next Vertex is not connected to Through Edge !');

        $cycle->addStep($this->vertices[2], $this->edges['4-1-1']);
    }

    public function testAddStepWithRepeatVertices()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);
        $cycle->addStep($this->vertices[2]);
        $cycle->addStep($this->vertices[3]);
        $cycle->addStep($this->vertices[4]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You can\'t Repeat Vertex !');

        $cycle->addStep($this->vertices[2]);
    }

    public function testAddStepWithRepeatEdge()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);
        $cycle->addStep($this->vertices[2]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You can\'t Repeat Edge !');

        $cycle->addStep($this->vertices[1]);
    }

    public function testAddStepWithoutThroughEdge()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);

        $this->assertIsArray($cycle->getSteps());
        $this->assertCount(1, $cycle->getSteps());
        $this->assertEquals(1, $cycle->countSteps());
        $this->assertEquals($this->vertices[1], $cycle->getFirstStep());
        $this->assertEquals($this->vertices[1], $cycle->getLastStep());

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(1, $cycle->getVertices());
        $this->assertEquals(1, $cycle->countVertices());

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(0, $cycle->getEdges());
        $this->assertEquals(0, $cycle->countEdges());

        $steps = $cycle->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $cycle->addStep($this->vertices[2]);

        $this->assertIsArray($cycle->getSteps());
        $this->assertCount(3, $cycle->getSteps());
        $this->assertEquals(3, $cycle->countSteps());
        $this->assertEquals($this->vertices[1], $cycle->getFirstStep());
        $this->assertEquals($this->vertices[2], $cycle->getLastStep());

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(2, $cycle->getVertices());
        $this->assertEquals(2, $cycle->countVertices());

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(1, $cycle->getEdges());
        $this->assertEquals(1, $cycle->countEdges());

        $steps = $cycle->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testAddStepWithThroughEdge()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);

        $this->assertIsArray($cycle->getSteps());
        $this->assertCount(1, $cycle->getSteps());
        $this->assertEquals(1, $cycle->countSteps());
        $this->assertEquals($this->vertices[1], $cycle->getFirstStep());
        $this->assertEquals($this->vertices[1], $cycle->getLastStep());

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(1, $cycle->getVertices());
        $this->assertEquals(1, $cycle->countVertices());

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(0, $cycle->getEdges());
        $this->assertEquals(0, $cycle->countEdges());

        $steps = $cycle->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $cycle->addStep($this->vertices[2], $this->edges['1-2']);

        $this->assertIsArray($cycle->getSteps());
        $this->assertCount(3, $cycle->getSteps());
        $this->assertEquals(3, $cycle->countSteps());
        $this->assertEquals($this->vertices[1], $cycle->getFirstStep());
        $this->assertEquals($this->vertices[2], $cycle->getLastStep());

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(2, $cycle->getVertices());
        $this->assertEquals(2, $cycle->countVertices());

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(1, $cycle->getEdges());
        $this->assertEquals(1, $cycle->countEdges());

        $steps = $cycle->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testGetTotalWeight()
    {
        $cycle = new Cycle($this->graph);
        $cycle->addStep($this->vertices[1]);

        $this->assertEquals(0, $cycle->getTotalWeight());

        $cycle->addStep($this->vertices[2]);

        $this->assertEquals(1, $cycle->getTotalWeight());

        $cycle->addStep($this->vertices[3]);

        $this->assertEquals(2, $cycle->getTotalWeight());

        $cycle->addStep($this->vertices[4]);

        $this->assertEquals(3, $cycle->getTotalWeight());
    }

    public function testGetVertices()
    {
        $cycle = new Cycle($this->graph);

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(0, $cycle->getVertices());
        $this->assertEquals(0, $cycle->countVertices());

        $cycle->addStep($this->vertices[1]);

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(1, $cycle->getVertices());
        $this->assertEquals(1, $cycle->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $cycle->getVertices());

        $cycle->addStep($this->vertices[2]);

        $this->assertIsArray($cycle->getVertices());
        $this->assertCount(2, $cycle->getVertices());
        $this->assertEquals(2, $cycle->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $cycle->getVertices());
    }

    public function testGetEdges()
    {
        $cycle = new Cycle($this->graph);

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(0, $cycle->getEdges());
        $this->assertEquals(0, $cycle->countEdges());

        $cycle->addStep($this->vertices[1]);

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(0, $cycle->getEdges());
        $this->assertEquals(0, $cycle->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $cycle->getEdges());

        $cycle->addStep($this->vertices[2]);

        $this->assertIsArray($cycle->getEdges());
        $this->assertCount(1, $cycle->getEdges());
        $this->assertEquals(1, $cycle->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $cycle->getEdges());
    }
}
