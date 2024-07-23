<?php

namespace Graphita\Graphita\Tests\Algorithms;

use Exception;
use Graphita\Graphita\Algorithms\TrailFindingAlgorithm;
use Graphita\Graphita\Graph;
use Graphita\Graphita\Trail;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TrailFindingAlgorithmTest extends TestCase
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
        $this->edges['4-1'] = $this->graph->createUndirectedEdge($this->vertices[4], $this->vertices[1]);
        $this->edges['1-3'] = $this->graph->createUndirectedEdge($this->vertices[1], $this->vertices[3]);
        $this->edges['2-4'] = $this->graph->createUndirectedEdge($this->vertices[2], $this->vertices[4]);
    }

    public function testEmptyTrailFindingAlgorithm()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);

        $this->assertEquals($this->graph, $algorithm->getGraph());
        $this->assertEmpty($algorithm->getSource());
        $this->assertEmpty($algorithm->getDestination());
        $this->assertEmpty($algorithm->getSteps());
        $this->assertEquals(1, $algorithm->getMinSteps());
        $this->assertEquals($this->graph->countVertices(), $algorithm->getMaxSteps());
        $this->assertIsArray($algorithm->getResults());
        $this->assertEmpty($algorithm->getResults());
        $this->assertCount(0, $algorithm->getResults());
        $this->assertEquals(0, $algorithm->countResults());
        $this->assertEmpty($algorithm->getShortestResult());
        $this->assertEmpty($algorithm->getLongestResult());
    }

    public function testSetSourceOutsideGraph()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $algorithm = new TrailFindingAlgorithm($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Source Vertex must be in Graph !');

        $algorithm->setSource($anotherVertex);
    }

    public function testGetAndSetSource()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);

        $this->assertEquals($this->vertices[1], $algorithm->getSource());
    }

    public function testSetDestinationOutsideGraph()
    {
        $anotherGraph = new Graph();
        $anotherVertex = $anotherGraph->createVertex(1);

        $algorithm = new TrailFindingAlgorithm($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Destination Vertex must be in Graph !');

        $algorithm->setDestination($anotherVertex);
    }

    public function testGetAndSetDestination()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setDestination($this->vertices[4]);

        $this->assertEquals($this->vertices[4], $algorithm->getDestination());
    }

    public function testSetStepsLessThanOne()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Steps must be Positive Integer Number equal or bigger than 1 !');

        $algorithm->setSteps(0);
    }

    public function testGetAndSetSteps()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setSteps(2);

        $this->assertEquals(2, $algorithm->getSteps());
    }

    public function testSetMinStepsLessThanOne()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Min Steps must be Positive Integer Number equal or bigger than 1 !');

        $algorithm->setMinSteps(0);
    }

    public function testSetMinStepsBiggerThanMaxSteps()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setMaxSteps(3);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Min Steps must be Positive Integer Number equal or less than Max Steps !');

        $algorithm->setMinSteps(4);
    }

    public function testGetAndSetMinSteps()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setMinSteps(2);

        $this->assertEquals(2, $algorithm->getMinSteps());
    }

    public function testSetMaxStepsLessThanOne()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Max Steps must be Positive Integer Number equal or bigger than 1 !');

        $algorithm->setMaxSteps(0);
    }

    public function testSetMaxStepsLessThanMinSteps()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setMinSteps(3);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Max Steps must be Positive Integer Number equal or bigger than Min Steps !');

        $algorithm->setMaxSteps(2);
    }

    public function testGetAndSetMaxSteps()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setMaxSteps(2);

        $this->assertEquals(2, $algorithm->getMaxSteps());
    }

    public function testCalculateWithoutSource()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Source must be set, before calculate !');

        $algorithm->calculate();
    }

    public function testCalculateWithoutDestination()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Destination must be set, before calculate !');

        $algorithm->calculate();
    }

    public function testCalculateWithOneStep()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);
        $algorithm->setDestination($this->vertices[3]);
        $algorithm->setSteps(1);
        $algorithm->calculate();

        $this->assertIsArray($algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Trail::class, $algorithm->getResults());
        $this->assertCount(1, $algorithm->getResults());
        $this->assertEquals(1, $algorithm->countResults());
        $this->assertInstanceOf(Trail::class, $algorithm->getShortestResult());
        $this->assertEquals(1, $algorithm->getShortestResult()->getTotalWeight());
        $this->assertInstanceOf(Trail::class, $algorithm->getLongestResult());
        $this->assertEquals(1, $algorithm->getLongestResult()->getTotalWeight());

        $results = $algorithm->getResults();

        foreach ($results as $result) {
            $this->assertEquals($this->vertices[1], $result->getFirstStep());
            $this->assertEquals($this->vertices[3], $result->getLastStep());
            $this->assertEquals(1, $result->countEdges());
        }

        $this->assertEquals($this->vertices[1], $results[0]->getFirstStep());
        $this->assertEquals($this->edges['1-3'], $results[0]->getSteps()[1]);
        $this->assertEquals($this->vertices[3], $results[0]->getLastStep());
    }

    public function testCalculateWithTwoSteps()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);
        $algorithm->setDestination($this->vertices[3]);
        $algorithm->setSteps(2);
        $algorithm->calculate();

        $this->assertIsArray($algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Trail::class, $algorithm->getResults());
        $this->assertCount(2, $algorithm->getResults());
        $this->assertEquals(2, $algorithm->countResults());
        $this->assertInstanceOf(Trail::class, $algorithm->getShortestResult());
        $this->assertEquals(2, $algorithm->getShortestResult()->getTotalWeight());
        $this->assertInstanceOf(Trail::class, $algorithm->getLongestResult());
        $this->assertEquals(2, $algorithm->getLongestResult()->getTotalWeight());

        $results = $algorithm->getResults();

        foreach ($results as $result) {
            $this->assertEquals($this->vertices[1], $result->getFirstStep());
            $this->assertEquals($this->vertices[3], $result->getLastStep());
            $this->assertEquals(2, $result->countEdges());
        }

        $this->assertEquals($this->vertices[1], $results[0]->getFirstStep());
        $this->assertEquals($this->edges['4-1'], $results[0]->getSteps()[1]);
        $this->assertEquals($this->vertices[4], $results[0]->getSteps()[2]);
        $this->assertEquals($this->edges['3-4'], $results[0]->getSteps()[3]);
        $this->assertEquals($this->vertices[3], $results[0]->getLastStep());

        $this->assertEquals($this->vertices[1], $results[1]->getFirstStep());
        $this->assertEquals($this->edges['1-2'], $results[1]->getSteps()[1]);
        $this->assertEquals($this->vertices[2], $results[1]->getSteps()[2]);
        $this->assertEquals($this->edges['2-3'], $results[1]->getSteps()[3]);
        $this->assertEquals($this->vertices[3], $results[1]->getLastStep());
    }

    public function testCalculateWithThreeSteps()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);
        $algorithm->setDestination($this->vertices[3]);
        $algorithm->setSteps(3);
        $algorithm->calculate();

        $this->assertIsArray($algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Trail::class, $algorithm->getResults());
        $this->assertCount(2, $algorithm->getResults());
        $this->assertEquals(2, $algorithm->countResults());
        $this->assertInstanceOf(Trail::class, $algorithm->getShortestResult());
        $this->assertEquals(3, $algorithm->getShortestResult()->getTotalWeight());
        $this->assertInstanceOf(Trail::class, $algorithm->getLongestResult());
        $this->assertEquals(3, $algorithm->getLongestResult()->getTotalWeight());

        $results = $algorithm->getResults();

        foreach ($results as $result) {
            $this->assertEquals($this->vertices[1], $result->getFirstStep());
            $this->assertEquals($this->vertices[3], $result->getLastStep());
            $this->assertEquals(3, $result->countEdges());
        }
    }

    public function testCalculateWithFourSteps()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);
        $algorithm->setDestination($this->vertices[3]);
        $algorithm->setSteps(4);
        $algorithm->calculate();

        $this->assertIsArray($algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Trail::class, $algorithm->getResults());
        $this->assertCount(4, $algorithm->getResults());
        $this->assertEquals(4, $algorithm->countResults());
        $this->assertInstanceOf(Trail::class, $algorithm->getShortestResult());
        $this->assertEquals(4, $algorithm->getShortestResult()->getTotalWeight());
        $this->assertInstanceOf(Trail::class, $algorithm->getLongestResult());
        $this->assertEquals(4, $algorithm->getLongestResult()->getTotalWeight());

        $results = $algorithm->getResults();

        foreach ($results as $result) {
            $this->assertEquals($this->vertices[1], $result->getFirstStep());
            $this->assertEquals($this->vertices[3], $result->getLastStep());
            $this->assertEquals(4, $result->countEdges());
        }
    }

    public function testCalculateWithWithoutSteps()
    {
        $algorithm = new TrailFindingAlgorithm($this->graph);
        $algorithm->setSource($this->vertices[1]);
        $algorithm->setDestination($this->vertices[3]);
        $algorithm->calculate();

        $this->assertIsArray($algorithm->getResults());
        $this->assertContainsOnlyInstancesOf(Trail::class, $algorithm->getResults());
        $this->assertCount(9, $algorithm->getResults());
        $this->assertEquals(9, $algorithm->countResults());
        $this->assertInstanceOf(Trail::class, $algorithm->getShortestResult());
        $this->assertEquals(1, $algorithm->getShortestResult()->getTotalWeight());
        $this->assertInstanceOf(Trail::class, $algorithm->getLongestResult());
        $this->assertEquals(4, $algorithm->getLongestResult()->getTotalWeight());

        $results = $algorithm->getResults();

        foreach ($results as $result) {
            $this->assertEquals($this->vertices[1], $result->getFirstStep());
            $this->assertEquals($this->vertices[3], $result->getLastStep());
            $this->assertGreaterThanOrEqual(1, $result->countEdges());
            $this->assertLessThanOrEqual(4, $result->countEdges());
        }
    }
}
