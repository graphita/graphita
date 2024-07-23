<?php

namespace Graphita\Graphita\Tests;

use Exception;
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
        $this->edges['4-2'] = $this->graph->createUndirectedEdge($this->vertices[4], $this->vertices[2]);
    }

    public function testEmptyPath()
    {
        $path = new Path($this->graph);

        $this->assertEquals($this->graph, $path->getGraph());

        $this->assertIsArray($path->getSteps());
        $this->assertCount(0, $path->getSteps());
        $this->assertEquals(0, $path->countSteps());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $firstStep = $path->getFirstStep();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is not Started !');
        $lastStep = $path->getLastStep();

        $this->assertIsArray($path->getVertices());
        $this->assertCount(0, $path->getVertices());
        $this->assertEquals(0, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertCount(0, $path->getEdges());
        $this->assertEquals(0, $path->countEdges());

        $this->assertFalse($path->isStarted());
        $this->assertFalse($path->isFinished());

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

    public function testStarting()
    {
        $path = new Path($this->graph);
        $path->start($this->vertices[1]);

        $this->assertEquals($this->graph, $path->getGraph());

        $this->assertIsArray($path->getSteps());
        $this->assertCount(1, $path->getSteps());
        $this->assertEquals(1, $path->countSteps());
        $this->assertEquals($this->vertices[1], $path->getFirstStep());
        $this->assertEquals($this->vertices[1], $path->getLastStep());

        $this->assertIsArray($path->getVertices());
        $this->assertCount(1, $path->getVertices());
        $this->assertEquals(1, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertCount(0, $path->getEdges());
        $this->assertEquals(0, $path->countEdges());

        $this->assertTrue($path->isStarted());
        $this->assertFalse($path->isFinished());

        $this->assertEquals(0, $path->getTotalWeight());
    }

    public function testDuplicateStarting()
    {
        $path = new Path($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk started before !');

        $path->start($this->vertices[1]);
        $path->start($this->vertices[2]);
    }

    public function testFinishing()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);
        $path->addStep($this->vertices[2]);
        $path->addStep($this->vertices[3]);

        $this->assertFalse($path->isFinished());

        $path->finish();

        $this->assertTrue($path->isFinished());
    }

    public function testAddStepWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $path = new Path($this->graph);
        $path->addStep($anotherVertex);
    }

    public function testAddStepWithOutsideOfGraphThroughEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $path = new Path($this->graph);
        $path->addStep($this->vertices[1], $anotherEdge);
    }

    public function testAddStepWhenFinished()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);
        $path->addStep($this->vertices[2]);
        $path->finish();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Walk is finished before !');

        $path->addStep($this->vertices[3]);
    }

    public function testStartingByAddStep()
    {
        $path = new Path($this->graph);

        $this->assertFalse($path->isStarted());

        $path->addStep($this->vertices[1]);

        $this->assertTrue($path->isStarted());
    }

    public function testAddStepNotNeighborVertices()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex has no Edges to new Vertex !');

        $path->addStep($this->vertices[3]);
    }

    public function testAddStepMultiEdgesVertices()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There is many Edges between Prev Vertex and Next Vertex !');

        $path->addStep($this->vertices[4]);
    }

    public function testAddStepThroughEdgeThatNotConnectedToPrevVertex()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Prev Vertex is not connected to Through Edge !');

        $path->addStep($this->vertices[2], $this->edges['3-4']);
    }

    public function testAddStepThroughEdgeThatNotConnectedToNextVertex()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Next Vertex is not connected to Through Edge !');

        $path->addStep($this->vertices[2], $this->edges['4-1-1']);
    }

    public function testAddStepWithRepeatVertices()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);
        $path->addStep($this->vertices[2]);
        $path->addStep($this->vertices[3]);
        $path->addStep($this->vertices[4]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You can\'t Repeat Vertex !');

        $path->addStep($this->vertices[2]);
    }

    public function testAddStepWithoutThroughEdge()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);

        $this->assertIsArray($path->getSteps());
        $this->assertCount(1, $path->getSteps());
        $this->assertEquals(1, $path->countSteps());
        $this->assertEquals($this->vertices[1], $path->getFirstStep());
        $this->assertEquals($this->vertices[1], $path->getLastStep());

        $this->assertIsArray($path->getVertices());
        $this->assertCount(1, $path->getVertices());
        $this->assertEquals(1, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertCount(0, $path->getEdges());
        $this->assertEquals(0, $path->countEdges());

        $steps = $path->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $path->addStep($this->vertices[2]);

        $this->assertIsArray($path->getSteps());
        $this->assertCount(3, $path->getSteps());
        $this->assertEquals(3, $path->countSteps());
        $this->assertEquals($this->vertices[1], $path->getFirstStep());
        $this->assertEquals($this->vertices[2], $path->getLastStep());

        $this->assertIsArray($path->getVertices());
        $this->assertCount(2, $path->getVertices());
        $this->assertEquals(2, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertCount(1, $path->getEdges());
        $this->assertEquals(1, $path->countEdges());

        $steps = $path->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testAddStepWithThroughEdge()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);

        $this->assertIsArray($path->getSteps());
        $this->assertCount(1, $path->getSteps());
        $this->assertEquals(1, $path->countSteps());
        $this->assertEquals($this->vertices[1], $path->getFirstStep());
        $this->assertEquals($this->vertices[1], $path->getLastStep());

        $this->assertIsArray($path->getVertices());
        $this->assertCount(1, $path->getVertices());
        $this->assertEquals(1, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertCount(0, $path->getEdges());
        $this->assertEquals(0, $path->countEdges());

        $steps = $path->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);

        $path->addStep($this->vertices[2], $this->edges['1-2']);

        $this->assertIsArray($path->getSteps());
        $this->assertCount(3, $path->getSteps());
        $this->assertEquals(3, $path->countSteps());
        $this->assertEquals($this->vertices[1], $path->getFirstStep());
        $this->assertEquals($this->vertices[2], $path->getLastStep());

        $this->assertIsArray($path->getVertices());
        $this->assertCount(2, $path->getVertices());
        $this->assertEquals(2, $path->countVertices());

        $this->assertIsArray($path->getEdges());
        $this->assertCount(1, $path->getEdges());
        $this->assertEquals(1, $path->countEdges());

        $steps = $path->getSteps();
        $this->assertEquals($this->vertices[1], $steps[0]);
        $this->assertEquals($this->edges['1-2'], $steps[1]);
        $this->assertEquals($this->vertices[2], $steps[2]);
    }

    public function testGetTotalWeight()
    {
        $path = new Path($this->graph);
        $path->addStep($this->vertices[1]);

        $this->assertEquals(0, $path->getTotalWeight());

        $path->addStep($this->vertices[2]);

        $this->assertEquals(1, $path->getTotalWeight());

        $path->addStep($this->vertices[3]);

        $this->assertEquals(2, $path->getTotalWeight());

        $path->addStep($this->vertices[4]);

        $this->assertEquals(3, $path->getTotalWeight());
    }

    public function testGetVertices()
    {
        $path = new Path($this->graph);

        $this->assertIsArray($path->getVertices());
        $this->assertCount(0, $path->getVertices());
        $this->assertEquals(0, $path->countVertices());

        $path->addStep($this->vertices[1]);

        $this->assertIsArray($path->getVertices());
        $this->assertCount(1, $path->getVertices());
        $this->assertEquals(1, $path->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());

        $path->addStep($this->vertices[2]);

        $this->assertIsArray($path->getVertices());
        $this->assertCount(2, $path->getVertices());
        $this->assertEquals(2, $path->countVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $path->getVertices());
    }

    public function testGetEdges()
    {
        $path = new Path($this->graph);

        $this->assertIsArray($path->getEdges());
        $this->assertCount(0, $path->getEdges());
        $this->assertEquals(0, $path->countEdges());

        $path->addStep($this->vertices[1]);

        $this->assertIsArray($path->getEdges());
        $this->assertCount(0, $path->getEdges());
        $this->assertEquals(0, $path->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $path->getEdges());

        $path->addStep($this->vertices[2]);

        $this->assertIsArray($path->getEdges());
        $this->assertCount(1, $path->getEdges());
        $this->assertEquals(1, $path->countEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $path->getEdges());
    }
}
