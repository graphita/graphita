<?php

namespace Graphita\Graphita\Tests;

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

        $this->assertIsArray($trail->getVertices());
        $this->assertCount(0, $trail->getVertices());
        $this->assertEquals(0, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(0, $trail->getEdges());
        $this->assertEquals(0, $trail->countEdges());

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

    public function testAddVerticesWithArrayOfNonVertex()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be array of Vertex !');

        $trail = new Trail($this->graph);
        $trail->addVertices([1, 2, 3]);
    }

    public function testAddVerticesWithOutsideOfGraphVertex()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vertices must be in a same Graph !');

        $trail = new Trail($this->graph);
        $trail->addVertices([$anotherVertex]);
    }

    public function testAddVerticesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! Vertex ' . $this->vertices[1]->getId() . ' does not have Neighbor Vertex ' . $this->vertices[3]->getId() . ' !');

        $trail = new Trail($this->graph);
        $trail->addVertices([
            $this->vertices[1],
            $this->vertices[3],
        ]);
    }

    public function testAddVerticesWithUnknownSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown steps ! There are more than one Edges from ' . $this->vertices[4]->getId() . ' to ' . $this->vertices[1]->getId() . ' !');

        $trail = new Trail($this->graph);
        $trail->addVertices([
            $this->vertices[4],
            $this->vertices[1],
        ]);
    }

    public function testAddVertices()
    {
        $trail = new Trail($this->graph);
        $trail->addVertices([
            $this->vertices[1],
            $this->vertices[2],
            $this->vertices[3],
            $this->vertices[4],
        ]);

        $this->assertIsArray($trail->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
        $this->assertCount(4, $trail->getVertices());
        $this->assertEquals(4, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(3, $trail->getEdges());
        $this->assertEquals(3, $trail->countEdges());
        $this->assertEquals($this->edges['1-2'], $trail->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $trail->getEdges()[1]);
        $this->assertEquals($this->edges['3-4'], $trail->getEdges()[2]);
    }

    public function testAddVertex()
    {
        $trail = new Trail($this->graph);
        $trail->addVertex($this->vertices[1]);

        $this->assertIsArray($trail->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
        $this->assertCount(1, $trail->getVertices());
        $this->assertEquals(1, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertCount(0, $trail->getEdges());
        $this->assertEquals(0, $trail->countEdges());

        $trail->addVertex($this->vertices[2]);

        $this->assertIsArray($trail->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
        $this->assertCount(2, $trail->getVertices());
        $this->assertEquals(2, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $trail->getEdges());
        $this->assertCount(1, $trail->getEdges());
        $this->assertEquals(1, $trail->countEdges());
        $this->assertEquals($this->edges['1-2'], $trail->getEdges()[0]);

        $trail->addVertex($this->vertices[3]);

        $this->assertIsArray($trail->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
        $this->assertCount(3, $trail->getVertices());
        $this->assertEquals(3, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $trail->getEdges());
        $this->assertCount(2, $trail->getEdges());
        $this->assertEquals(2, $trail->countEdges());
        $this->assertEquals($this->edges['1-2'], $trail->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $trail->getEdges()[1]);

        $trail->addVertex($this->vertices[4]);

        $this->assertIsArray($trail->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
        $this->assertCount(4, $trail->getVertices());
        $this->assertEquals(4, $trail->countVertices());

        $this->assertIsArray($trail->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $trail->getEdges());
        $this->assertCount(3, $trail->getEdges());
        $this->assertEquals(3, $trail->countEdges());
        $this->assertEquals($this->edges['1-2'], $trail->getEdges()[0]);
        $this->assertEquals($this->edges['2-3'], $trail->getEdges()[1]);
        $this->assertEquals($this->edges['3-4'], $trail->getEdges()[2]);
    }

    public function testAddEdgesWithArrayOfNonEdge()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be array of AbstractEdge !');

        $trail = new Trail($this->graph);
        $trail->addEdges([1, 2, 3]);
    }

    public function testAddEdgesWithOutsideOfGraphEdge()
    {
        $anotherGraph = new Graph();
        $anotherVertex1 = $anotherGraph->createVertex(1);
        $anotherVertex2 = $anotherGraph->createVertex(2);
        $anotherEdge = $anotherGraph->createUndirectedEdge($anotherVertex1, $anotherVertex2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be in a same Graph !');

        $trail = new Trail($this->graph);
        $trail->addEdges([$anotherEdge]);
    }

    public function testAddEdgesWithInvalidSteps()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid steps ! There is no common Vertex between Edge ' . $this->edges['1-2']->getId() . ' and ' . $this->edges['3-4']->getId() . ' !');

        $trail = new Trail($this->graph);
        $trail->addEdges([
            $this->edges['1-2'],
            $this->edges['3-4'],
        ]);
    }

    public function testAddDuplicateEdges()
    {
        $trail = new Trail($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edges must be unique !');

        $trail->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['1-2'],
        ]);
    }

    public function testAddEdges()
    {
        $trail = new Trail($this->graph);
        $trail->addEdges([
            $this->edges['1-2'],
            $this->edges['2-3'],
            $this->edges['3-4'],
        ]);

        $this->assertIsArray($trail->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $trail->getEdges());
        $this->assertCount(3, $trail->getEdges());
        $this->assertEquals(3, $trail->countEdges());

        $this->assertIsArray($trail->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
        $this->assertCount(4, $trail->getVertices());
        $this->assertEquals(4, $trail->countVertices());
        $this->assertEquals($this->vertices[1], $trail->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $trail->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $trail->getVertices()[2]);
        $this->assertEquals($this->vertices[4], $trail->getVertices()[3]);
    }

    public function testAddEdge()
    {
        $trail = new Trail($this->graph);
        $trail->addEdge($this->edges['1-2']);

        $this->assertIsArray($trail->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $trail->getEdges());
        $this->assertCount(1, $trail->getEdges());
        $this->assertEquals(1, $trail->countEdges());

        $this->assertIsArray($trail->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
        $this->assertCount(2, $trail->getVertices());
        $this->assertEquals(2, $trail->countVertices());
        $this->assertEquals($this->vertices[1], $trail->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $trail->getVertices()[1]);

        $trail->addEdge($this->edges['2-3']);

        $this->assertIsArray($trail->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $trail->getEdges());
        $this->assertCount(2, $trail->getEdges());
        $this->assertEquals(2, $trail->countEdges());

        $this->assertIsArray($trail->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
        $this->assertCount(3, $trail->getVertices());
        $this->assertEquals(3, $trail->countVertices());
        $this->assertEquals($this->vertices[1], $trail->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $trail->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $trail->getVertices()[2]);

        $trail->addEdge($this->edges['3-4']);

        $this->assertIsArray($trail->getEdges());
        $this->assertContainsOnlyInstancesOf(AbstractEdge::class, $trail->getEdges());
        $this->assertCount(3, $trail->getEdges());
        $this->assertEquals(3, $trail->countEdges());

        $this->assertIsArray($trail->getVertices());
        $this->assertContainsOnlyInstancesOf(Vertex::class, $trail->getVertices());
        $this->assertCount(4, $trail->getVertices());
        $this->assertEquals(4, $trail->countVertices());
        $this->assertEquals($this->vertices[1], $trail->getVertices()[0]);
        $this->assertEquals($this->vertices[2], $trail->getVertices()[1]);
        $this->assertEquals($this->vertices[3], $trail->getVertices()[2]);
        $this->assertEquals($this->vertices[4], $trail->getVertices()[3]);
    }

    public function testGetTotalWeight()
    {
        $trail = new Trail($this->graph);
        $trail->addVertex($this->vertices[1]);

        $this->assertEquals(0, $trail->getTotalWeight());

        $trail->addVertex($this->vertices[2]);

        $this->assertEquals(1, $trail->getTotalWeight());

        $trail->addVertex($this->vertices[3]);

        $this->assertEquals(2, $trail->getTotalWeight());

        $trail->addVertex($this->vertices[4]);

        $this->assertEquals(3, $trail->getTotalWeight());
    }
}
