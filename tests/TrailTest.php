<?php

namespace Graphita\Graphita\Tests;

use Exception;
use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Vertex;
use Graphita\Graphita\Trail;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TrailTest extends TestCase
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

    public function testEmptyTrail()
    {
        $trail = new Trail($this->graph);

        $this->assertEquals($this->graph, $trail->getGraph());

        $this->assertIsArray($trail->getSteps());
        $this->assertCount(0, $trail->getSteps());
        $this->assertEquals(0, $trail->countSteps());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $firstStep = $trail->getFirstStep();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $lastStep = $trail->getLastStep();

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(0, $trail->getVertices());
        $this->assertEquals(0, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(0, $trail->getEdges());
        $this->assertEquals(0, $trail->countEdges());

        $this->assertFalse($trail->isStarted());
        $this->assertFalse($trail->isFinished());

        $this->assertEquals(0, $trail->getTotalWeight());

        $this->assertTrue($trail->canRepeatVertices());
        $this->assertFalse($trail->canRepeatEdges());
        $this->assertFalse($trail->isLoop());

        $this->assertIsArray($trail->getAttributes());
        $this->assertEmpty($trail->getAttributes());
    }

    public function testGetTrailAttributes()
    {
        $trail = new Trail($this->graph, ['name' => 'Euler Trail']);

        $this->assertIsArray($trail->getAttributes());
        $this->assertCount(1, $trail->getAttributes());
        $this->assertEquals('Euler Trail', $trail->getAttribute('name'));
        $this->assertEquals('Euler Trail', $trail->getAttribute('name', 'Leonhard Trail'));
        $this->assertNull($trail->getAttribute('color'));
        $this->assertEquals('Red', $trail->getAttribute('color', 'Red'));
    }

    public function testSetTrailAttribute()
    {
        $trail = new Trail($this->graph);
        $trail->setAttribute('name', 'Euler Trail');

        $this->assertIsArray($trail->getAttributes());
        $this->assertCount(1, $trail->getAttributes());
        $this->assertEquals('Euler Trail', $trail->getAttribute('name'));
    }

    public function testSetTrailAttributes()
    {
        $trail = new Trail($this->graph);
        $trail->setAttributes(['name' => 'Euler Trail']);

        $this->assertIsArray($trail->getAttributes());
        $this->assertCount(1, $trail->getAttributes());
        $this->assertEquals('Euler Trail', $trail->getAttribute('name'));
    }

    public function testRemoveTrailAttribute()
    {
        $trail = new Trail($this->graph, ['name' => 'Euler Trail']);
        $trail->removeAttribute('name');

        $this->assertIsArray($trail->getAttributes());
        $this->assertCount(0, $trail->getAttributes());
        $this->assertNull($trail->getAttribute('name'));
    }

    public function testEmptyTrailAttributes()
    {
        $trail = new Trail($this->graph, ['name' => 'Euler Trail']);
        $trail->emptyAttributes();

        $this->assertIsArray($trail->getAttributes());
        $this->assertCount(0, $trail->getAttributes());
        $this->assertNull($trail->getAttribute('name'));
    }

    public function testStartingViaConstructor()
    {
        $trail = new Trail($this->graph, $this->vertices[1]);

        $this->assertEquals($this->graph, $trail->getGraph());

        $this->assertIsArray($trail->getSteps());
        $this->assertCount(1, $trail->getSteps());
        $this->assertEquals(1, $trail->countSteps());
        $this->assertEquals($this->vertices[1], $trail->getFirstStep());
        $this->assertEquals($this->vertices[1], $trail->getLastStep());

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(1, $trail->getVertices());
        $this->assertEquals(1, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(0, $trail->getEdges());
        $this->assertEquals(0, $trail->countEdges());

        $this->assertTrue($trail->isStarted());
        $this->assertFalse($trail->isFinished());

        $this->assertEquals(0, $trail->getTotalWeight());
    }

    public function testStarting()
    {
        $trail = new Trail($this->graph);
        $trail->start($this->vertices[1]);

        $this->assertEquals($this->graph, $trail->getGraph());

        $this->assertIsArray($trail->getSteps());
        $this->assertCount(1, $trail->getSteps());
        $this->assertEquals(1, $trail->countSteps());
        $this->assertEquals($this->vertices[1], $trail->getFirstStep());
        $this->assertEquals($this->vertices[1], $trail->getLastStep());

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(1, $trail->getVertices());
        $this->assertEquals(1, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(0, $trail->getEdges());
        $this->assertEquals(0, $trail->countEdges());

        $this->assertTrue($trail->isStarted());
        $this->assertFalse($trail->isFinished());

        $this->assertEquals(0, $trail->getTotalWeight());
    }

    public function testDuplicateStarting()
    {
        $trail = new Trail($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk started before !');

        $trail->start($this->vertices[1]);
        $trail->start($this->vertices[2]);
    }

    public function testFinishingWhenSourceAndDestinationSame()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);
        $trail->addStep($this->vertices[2]);
        $trail->addStep($this->vertices[3]);
        $trail->addStep($this->vertices[4]);
        $trail->addStep($this->vertices[1], $this->edges['4-1-1']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Source Vertex and Destination Vertex shouldn\'t be Equal !');

        $trail->finish();
    }

    public function testFinishing()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);
        $trail->addStep($this->vertices[2]);
        $trail->addStep($this->vertices[3]);

        $this->assertFalse($trail->isFinished());

        $trail->finish();

        $this->assertTrue($trail->isFinished());
    }

    public function testAddStepWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $trail = new Trail($this->graph);
        $trail->addStep($anotherVertex);
    }

    public function testAddStepWithOutsideOfGraphThroughEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1], $anotherEdge);
    }

    public function testAddStepWhenFinished()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);
        $trail->addStep($this->vertices[2]);
        $trail->finish();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is finished before !');

        $trail->addStep($this->vertices[3]);
    }

    public function testStartingByAddStep()
    {
        $trail = new Trail($this->graph);

        $this->assertFalse($trail->isStarted());

        $trail->addStep($this->vertices[1]);

        $this->assertTrue($trail->isStarted());
    }

    public function testAddStepNotNeighborVertices()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex has no Edges to new Vertex !');

        $trail->addStep($this->vertices[3]);
    }

    public function testAddStepMultiEdgesVertices()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There is many Edges between Prev Vertex and Next Vertex !');

        $trail->addStep($this->vertices[4]);
    }

    public function testAddStepThroughEdgeThatNotConnectedToPrevVertex()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex is not connected to Through Edge !');

        $trail->addStep($this->vertices[2], $this->edges['3-4']);
    }

    public function testAddStepThroughEdgeThatNotConnectedToNextVertex()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Next Vertex is not connected to Through Edge !');

        $trail->addStep($this->vertices[2], $this->edges['4-1-1']);
    }

    public function testAddStepWithRepeatEdge()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);
        $trail->addStep($this->vertices[2]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You can\'t Repeat Edge !');

        $trail->addStep($this->vertices[1]);
    }

    public function testAddStepWithoutThroughEdge()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);

        $this->assertIsArray($trail->getSteps());
        $this->assertCount(1, $trail->getSteps());
        $this->assertEquals(1, $trail->countSteps());
        $this->assertEquals($this->vertices[1], $trail->getFirstStep());
        $this->assertEquals($this->vertices[1], $trail->getLastStep());

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(1, $trail->getVertices());
        $this->assertEquals(1, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(0, $trail->getEdges());
        $this->assertEquals(0, $trail->countEdges());

        $steps = $trail->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $trail->addStep($this->vertices[2]);

        $this->assertIsArray($trail->getSteps());
        $this->assertCount(3, $trail->getSteps());
        $this->assertEquals(3, $trail->countSteps());
        $this->assertEquals($this->vertices[1], $trail->getFirstStep());
        $this->assertEquals($this->vertices[2], $trail->getLastStep());

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(2, $trail->getVertices());
        $this->assertEquals(2, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(1, $trail->getEdges());
        $this->assertEquals(1, $trail->countEdges());

        $steps = $trail->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testAddStepWithThroughEdge()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);

        $this->assertIsArray($trail->getSteps());
        $this->assertCount(1, $trail->getSteps());
        $this->assertEquals(1, $trail->countSteps());
        $this->assertEquals($this->vertices[1], $trail->getFirstStep());
        $this->assertEquals($this->vertices[1], $trail->getLastStep());

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(1, $trail->getVertices());
        $this->assertEquals(1, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(0, $trail->getEdges());
        $this->assertEquals(0, $trail->countEdges());

        $steps = $trail->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $trail->addStep($this->vertices[2], $this->edges['1-2']);

        $this->assertIsArray($trail->getSteps());
        $this->assertCount(3, $trail->getSteps());
        $this->assertEquals(3, $trail->countSteps());
        $this->assertEquals($this->vertices[1], $trail->getFirstStep());
        $this->assertEquals($this->vertices[2], $trail->getLastStep());

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(2, $trail->getVertices());
        $this->assertEquals(2, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(1, $trail->getEdges());
        $this->assertEquals(1, $trail->countEdges());

        $steps = $trail->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testGetTotalWeight()
    {
        $trail = new Trail($this->graph);
        $trail->addStep($this->vertices[1]);

        $this->assertEquals(0, $trail->getTotalWeight());

        $trail->addStep($this->vertices[2]);

        $this->assertEquals(1, $trail->getTotalWeight());

        $trail->addStep($this->vertices[3]);

        $this->assertEquals(2, $trail->getTotalWeight());

        $trail->addStep($this->vertices[4]);

        $this->assertEquals(3, $trail->getTotalWeight());
    }

    public function testGetVertices()
    {
        $trail = new Trail($this->graph);

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(0, $trail->getVertices());
        $this->assertEquals(0, $trail->countVertices());

        $trail->addStep($this->vertices[1]);

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(1, $trail->getVertices());
        $this->assertEquals(1, $trail->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());

        $trail->addStep($this->vertices[2]);

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(2, $trail->getVertices());
        $this->assertEquals(2, $trail->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
    }

    public function testGetEdges()
    {
        $trail = new Trail($this->graph);

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(0, $trail->getEdges());
        $this->assertEquals(0, $trail->countEdges());

        $trail->addStep($this->vertices[1]);

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(0, $trail->getEdges());
        $this->assertEquals(0, $trail->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $trail->getEdges());

        $trail->addStep($this->vertices[2]);

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(1, $trail->getEdges());
        $this->assertEquals(1, $trail->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $trail->getEdges());
    }
}
