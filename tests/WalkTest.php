<?php

namespace Graphita\Graphita\Tests;

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

        $this->assertIsArray($walk->getVertices());
        $this->assertCount(0, $walk->getVertices());
        $this->assertEquals(0, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(0, $walk->getEdges());
        $this->assertEquals(0, $walk->countEdges());

        $this->assertEquals(0, $walk->getTotalWeight());

        $this->assertTrue($walk->canRepeatVertices());
        $this->assertTrue($walk->canRepeatEdges());
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

    public function testAddVerticesWithArrayOfNonVertex()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be array of Vertex !');

        $walk = new Walk($this->graph);
        $walk->addVertices([1, 2, 3]);
    }

    public function testAddVerticesWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $walk = new Walk($this->graph);
        $walk->addVertices([$anotherVertex]);
    }

    public function testAddVerticesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! Vertex ' . $this->vertices[1]->getId() . ' does not have Neighbor Vertex ' . $this->vertices[3]->getId() . ' !');

        $walk = new Walk($this->graph);
        $walk->addVertices([
            $this->vertices[1],
            $this->vertices[3],
        ]);
    }

    public function testAddVerticesWithUnknownSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown steps ! There are more than one Edges from ' . $this->vertices[4]->getId() . ' to ' . $this->vertices[1]->getId() . ' !');

        $walk = new Walk($this->graph);
        $walk->addVertices([
            $this->vertices[4],
            $this->vertices[1],
        ]);
    }

    public function testAddVertices()
    {
        $walk = new Walk($this->graph);
        $walk->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[4],
        ]);

        $this->assertIsArray($walk->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
        $this->assertCount(4, $walk->getVertices());
        $this->assertEquals(4, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(3, $walk->getEdges());
        $this->assertEquals(3, $walk->countEdges());
        $this->assertEquals($this->edges['1-2'], $walk->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $walk->getEdges()[1]);
        $this->assertEquals($this->edges['3-4'], $walk->getEdges()[2]);
    }

    public function testAddVertex()
    {
        $walk = new Walk($this->graph);
        $walk->addVertex($this->vertices[1]);

        $this->assertIsArray($walk->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
        $this->assertCount(1, $walk->getVertices());
        $this->assertEquals(1, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertCount(0, $walk->getEdges());
        $this->assertEquals(0, $walk->countEdges());

        $walk->addVertex($this->vertices[2]);

        $this->assertIsArray($walk->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
        $this->assertCount(2, $walk->getVertices());
        $this->assertEquals(2, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $walk->getEdges());
        $this->assertCount(1, $walk->getEdges());
        $this->assertEquals(1, $walk->countEdges());
        $this->assertEquals($this->edges['1-2'], $walk->getEdges()[0]);

        $walk->addVertex($this->vertices[3]);

        $this->assertIsArray($walk->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
        $this->assertCount(3, $walk->getVertices());
        $this->assertEquals(3, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $walk->getEdges());
        $this->assertCount(2, $walk->getEdges());
        $this->assertEquals(2, $walk->countEdges());
        $this->assertEquals($this->edges['1-2'], $walk->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $walk->getEdges()[1]);

        $walk->addVertex($this->vertices[4]);

        $this->assertIsArray($walk->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
        $this->assertCount(4, $walk->getVertices());
        $this->assertEquals(4, $walk->countVertices());

        $this->assertIsArray($walk->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $walk->getEdges());
        $this->assertCount(3, $walk->getEdges());
        $this->assertEquals(3, $walk->countEdges());
        $this->assertEquals($this->edges['1-2'], $walk->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $walk->getEdges()[1]);
        $this->assertEquals($this->edges['3-4'], $walk->getEdges()[2]);
    }

    public function testAddEdgesWithArrayOfNonEdge()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be array of AbstractEdge !');

        $walk = new Walk($this->graph);
        $walk->addEdges([1, 2, 3]);
    }

    public function testAddEdgesWithOutsideOfGraphEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $walk = new Walk($this->graph);
        $walk->addEdges([$anotherEdge]);
    }

    public function testAddEdgesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! There is no common Vertex between Edge ' . $this->edges['1-2']->getId() . ' and ' . $this->edges['3-4']->getId() . ' !');

        $walk = new Walk($this->graph);
        $walk->addEdges([
            $this->edges['1-2'],
            $this->edges['3-4'],
        ]);
    }

    public function testAddEdges()
    {
        $walk = new Walk($this->graph);
        $walk->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['3-4'],
        ]);

        $this->assertIsArray($walk->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $walk->getEdges());
        $this->assertCount(3, $walk->getEdges());
        $this->assertEquals(3, $walk->countEdges());

        $this->assertIsArray($walk->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
        $this->assertCount(4, $walk->getVertices());
        $this->assertEquals(4, $walk->countVertices());
        $this->assertEquals($this->vertices[1], $walk->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $walk->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $walk->getVertices()[2]);
        $this->assertEquals($this->vertices[4], $walk->getVertices()[3]);
    }

    public function testAddEdge()
    {
        $walk = new Walk($this->graph);
        $walk->addEdge($this->edges['1-2']);

        $this->assertIsArray($walk->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $walk->getEdges());
        $this->assertCount(1, $walk->getEdges());
        $this->assertEquals(1, $walk->countEdges());

        $this->assertIsArray($walk->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
        $this->assertCount(2, $walk->getVertices());
        $this->assertEquals(2, $walk->countVertices());
        $this->assertEquals($this->vertices[1], $walk->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $walk->getVertices()[1]);

        $walk->addEdge($this->edges['2-3']);

        $this->assertIsArray($walk->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $walk->getEdges());
        $this->assertCount(2, $walk->getEdges());
        $this->assertEquals(2, $walk->countEdges());

        $this->assertIsArray($walk->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
        $this->assertCount(3, $walk->getVertices());
        $this->assertEquals(3, $walk->countVertices());
        $this->assertEquals($this->vertices[1], $walk->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $walk->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $walk->getVertices()[2]);

        $walk->addEdge($this->edges['3-4']);

        $this->assertIsArray($walk->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $walk->getEdges());
        $this->assertCount(3, $walk->getEdges());
        $this->assertEquals(3, $walk->countEdges());

        $this->assertIsArray($walk->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $walk->getVertices());
        $this->assertCount(4, $walk->getVertices());
        $this->assertEquals(4, $walk->countVertices());
        $this->assertEquals($this->vertices[1], $walk->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $walk->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $walk->getVertices()[2]);
        $this->assertEquals($this->vertices[4], $walk->getVertices()[3]);
    }

    public function testGetTotalWeight()
    {
        $walk = new Walk($this->graph);
        $walk->addVertex($this->vertices[1]);

        $this->assertEquals(0, $walk->getTotalWeight());

        $walk->addVertex($this->vertices[2]);

        $this->assertEquals(1, $walk->getTotalWeight());

        $walk->addVertex($this->vertices[3]);

        $this->assertEquals(2, $walk->getTotalWeight());

        $walk->addVertex($this->vertices[4]);

        $this->assertEquals(3, $walk->getTotalWeight());
    }
}
