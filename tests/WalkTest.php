<?php

namespace Graphita\Graphita\Tests;

use Exception;
use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Vertex;
use Graphita\Graphita\Walk;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class WalkTest extends TestCase
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

    public function testEmptyWalk()
    {
        $walk = new Walk($this->graph);

        $this->assertEquals($this->graph, $walk->getGraph());

        $this->assertIsArray($walk->getSteps());
        $this->assertCount(0, $walk->getSteps());
        $this->assertEquals(0, $walk->countSteps());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $firstStep = $walk->getFirstStep();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $lastStep = $walk->getLastStep();

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(0, $walk->getVertices());
        $this->assertEquals(0, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(0, $walk->getEdges());
        $this->assertEquals(0, $walk->countEdges());

        $this->assertFalse($walk->isStarted());
        $this->assertFalse($walk->isFinished());

        $this->assertEquals(0, $walk->getTotalWeight());

        $this->assertTrue($walk->canRepeatVertices());
        $this->assertFalse($walk->canRepeatEdges());
        $this->assertFalse($walk->isLoop());

        $this->assertIsArray($walk->getAttributes());
        $this->assertEmpty($walk->getAttributes());
    }

    public function testGetWalkAttributes()
    {
        $walk = new Walk($this->graph, ['name' => 'Euler Walk']);

        $this->assertIsArray($walk->getAttributes());
        $this->assertCount(1, $walk->getAttributes());
        $this->assertEquals('Euler Walk', $walk->getAttribute('name'));
        $this->assertEquals('Euler Walk', $walk->getAttribute('name', 'Leonhard Walk'));
        $this->assertNull($walk->getAttribute('color'));
        $this->assertEquals('Red', $walk->getAttribute('color', 'Red'));
    }

    public function testSetWalkAttribute()
    {
        $walk = new Walk($this->graph);
        $walk->setAttribute('name', 'Euler Walk');

        $this->assertIsArray($walk->getAttributes());
        $this->assertCount(1, $walk->getAttributes());
        $this->assertEquals('Euler Walk', $walk->getAttribute('name'));
    }

    public function testSetWalkAttributes()
    {
        $walk = new Walk($this->graph);
        $walk->setAttributes(['name' => 'Euler Walk']);

        $this->assertIsArray($walk->getAttributes());
        $this->assertCount(1, $walk->getAttributes());
        $this->assertEquals('Euler Walk', $walk->getAttribute('name'));
    }

    public function testRemoveWalkAttribute()
    {
        $walk = new Walk($this->graph, ['name' => 'Euler Walk']);
        $walk->removeAttribute('name');

        $this->assertIsArray($walk->getAttributes());
        $this->assertCount(0, $walk->getAttributes());
        $this->assertNull($walk->getAttribute('name'));
    }

    public function testEmptyWalkAttributes()
    {
        $walk = new Walk($this->graph, ['name' => 'Euler Walk']);
        $walk->emptyAttributes();

        $this->assertIsArray($walk->getAttributes());
        $this->assertCount(0, $walk->getAttributes());
        $this->assertNull($walk->getAttribute('name'));
    }

    public function testStartingViaConstructor()
    {
        $walk = new Walk($this->graph, $this->vertices[1]);

        $this->assertEquals($this->graph, $walk->getGraph());

        $this->assertIsArray($walk->getSteps());
        $this->assertCount(1, $walk->getSteps());
        $this->assertEquals(1, $walk->countSteps());
        $this->assertEquals($this->vertices[1], $walk->getFirstStep());
        $this->assertEquals($this->vertices[1], $walk->getLastStep());

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(1, $walk->getVertices());
        $this->assertEquals(1, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(0, $walk->getEdges());
        $this->assertEquals(0, $walk->countEdges());

        $this->assertTrue($walk->isStarted());
        $this->assertFalse($walk->isFinished());

        $this->assertEquals(0, $walk->getTotalWeight());
    }

    public function testStarting()
    {
        $walk = new Walk($this->graph);
        $walk->start($this->vertices[1]);

        $this->assertEquals($this->graph, $walk->getGraph());

        $this->assertIsArray($walk->getSteps());
        $this->assertCount(1, $walk->getSteps());
        $this->assertEquals(1, $walk->countSteps());
        $this->assertEquals($this->vertices[1], $walk->getFirstStep());
        $this->assertEquals($this->vertices[1], $walk->getLastStep());

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(1, $walk->getVertices());
        $this->assertEquals(1, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(0, $walk->getEdges());
        $this->assertEquals(0, $walk->countEdges());

        $this->assertTrue($walk->isStarted());
        $this->assertFalse($walk->isFinished());

        $this->assertEquals(0, $walk->getTotalWeight());
    }

    public function testDuplicateStarting()
    {
        $walk = new Walk($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk started before !');

        $walk->start($this->vertices[1]);
        $walk->start($this->vertices[2]);
    }

    public function testFinishingWhenSourceAndDestinationSame()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);
        $walk->addStep($this->vertices[2]);
        $walk->addStep($this->vertices[3]);
        $walk->addStep($this->vertices[4]);
        $walk->addStep($this->vertices[1], $this->edges['4-1-1']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Source Vertex and Destination Vertex shouldn\'t be Equal !');

        $walk->finish();
    }

    public function testFinishing()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);
        $walk->addStep($this->vertices[2]);
        $walk->addStep($this->vertices[3]);

        $this->assertFalse($walk->isFinished());

        $walk->finish();

        $this->assertTrue($walk->isFinished());
    }

    public function testAddStepWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $walk = new Walk($this->graph);
        $walk->addStep($anotherVertex);
    }

    public function testAddStepWithOutsideOfGraphThroughEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1], $anotherEdge);
    }

    public function testAddStepWhenFinished()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);
        $walk->addStep($this->vertices[2]);
        $walk->finish();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is finished before !');

        $walk->addStep($this->vertices[3]);
    }

    public function testStartingByAddStep()
    {
        $walk = new Walk($this->graph);

        $this->assertFalse($walk->isStarted());

        $walk->addStep($this->vertices[1]);

        $this->assertTrue($walk->isStarted());
    }

    public function testAddStepNotNeighborVertices()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex has no Edges to new Vertex !');

        $walk->addStep($this->vertices[3]);
    }

    public function testAddStepMultiEdgesVertices()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There is many Edges between Prev Vertex and Next Vertex !');

        $walk->addStep($this->vertices[4]);
    }

    public function testAddStepThroughEdgeThatNotConnectedToPrevVertex()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex is not connected to Through Edge !');

        $walk->addStep($this->vertices[2], $this->edges['3-4']);
    }

    public function testAddStepThroughEdgeThatNotConnectedToNextVertex()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Next Vertex is not connected to Through Edge !');

        $walk->addStep($this->vertices[2], $this->edges['4-1-1']);
    }

    public function testAddStepWithoutThroughEdge()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);

        $this->assertIsArray($walk->getSteps());
        $this->assertCount(1, $walk->getSteps());
        $this->assertEquals(1, $walk->countSteps());
        $this->assertEquals($this->vertices[1], $walk->getFirstStep());
        $this->assertEquals($this->vertices[1], $walk->getLastStep());

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(1, $walk->getVertices());
        $this->assertEquals(1, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(0, $walk->getEdges());
        $this->assertEquals(0, $walk->countEdges());

        $steps = $walk->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $walk->addStep($this->vertices[2]);

        $this->assertIsArray($walk->getSteps());
        $this->assertCount(3, $walk->getSteps());
        $this->assertEquals(3, $walk->countSteps());
        $this->assertEquals($this->vertices[1], $walk->getFirstStep());
        $this->assertEquals($this->vertices[2], $walk->getLastStep());

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(2, $walk->getVertices());
        $this->assertEquals(2, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(1, $walk->getEdges());
        $this->assertEquals(1, $walk->countEdges());

        $steps = $walk->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testAddStepWithThroughEdge()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);

        $this->assertIsArray($walk->getSteps());
        $this->assertCount(1, $walk->getSteps());
        $this->assertEquals(1, $walk->countSteps());
        $this->assertEquals($this->vertices[1], $walk->getFirstStep());
        $this->assertEquals($this->vertices[1], $walk->getLastStep());

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(1, $walk->getVertices());
        $this->assertEquals(1, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(0, $walk->getEdges());
        $this->assertEquals(0, $walk->countEdges());

        $steps = $walk->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $walk->addStep($this->vertices[2], $this->edges['1-2']);

        $this->assertIsArray($walk->getSteps());
        $this->assertCount(3, $walk->getSteps());
        $this->assertEquals(3, $walk->countSteps());
        $this->assertEquals($this->vertices[1], $walk->getFirstStep());
        $this->assertEquals($this->vertices[2], $walk->getLastStep());

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(2, $walk->getVertices());
        $this->assertEquals(2, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(1, $walk->getEdges());
        $this->assertEquals(1, $walk->countEdges());

        $steps = $walk->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testGetTotalWeight()
    {
        $walk = new Walk($this->graph);
        $walk->addStep($this->vertices[1]);

        $this->assertEquals(0, $walk->getTotalWeight());

        $walk->addStep($this->vertices[2]);

        $this->assertEquals(1, $walk->getTotalWeight());

        $walk->addStep($this->vertices[3]);

        $this->assertEquals(2, $walk->getTotalWeight());

        $walk->addStep($this->vertices[4]);

        $this->assertEquals(3, $walk->getTotalWeight());
    }

    public function testGetVertices()
    {
        $walk = new Walk($this->graph);

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(0, $walk->getVertices());
        $this->assertEquals(0, $walk->countVertices());

        $walk->addStep($this->vertices[1]);

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(1, $walk->getVertices());
        $this->assertEquals(1, $walk->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());

        $walk->addStep($this->vertices[2]);

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(2, $walk->getVertices());
        $this->assertEquals(2, $walk->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
    }

    public function testGetEdges()
    {
        $walk = new Walk($this->graph);

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(0, $walk->getEdges());
        $this->assertEquals(0, $walk->countEdges());

        $walk->addStep($this->vertices[1]);

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(0, $walk->getEdges());
        $this->assertEquals(0, $walk->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $walk->getEdges());

        $walk->addStep($this->vertices[2]);

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(1, $walk->getEdges());
        $this->assertEquals(1, $walk->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $walk->getEdges());
    }
}
