<?php

namespace Graphita\Graphita\Tests;

use Graphita\Graphita\Graph;
use PHPUnit\Framework\TestCase;

class GraphTest extends TestCase
{
    public function testEmptyGraph()
    {
        $graph = new Graph();

        $this->assertIsArray($graph->getVertices());
        $this->assertEmpty($graph->getVertices());

        $this->assertIsArray($graph->getEdges());
        $this->assertEmpty($graph->getEdges());

        $this->assertIsArray($graph->getAttributes());
        $this->assertEmpty($graph->getAttributes());
    }

    public function testUseGraphAttributes()
    {
        $graph = new Graph(['name' => 'Euler Graph']);

        $this->assertIsArray($graph->getAttributes());
        $this->assertCount(1, $graph->getAttributes());
        $this->assertEquals('Euler Graph', $graph->getAttribute('name'));
        $this->assertEquals('Euler Graph', $graph->getAttribute('name', 'Leonhard Graph'));
        $this->assertNull($graph->getAttribute('color'));
        $this->assertEquals('Red', $graph->getAttribute('color', 'Red'));
    }

    public function testSetGraphAttribute()
    {
        $graph = new Graph();
        $graph->setAttribute('name', 'Euler Graph');

        $this->assertIsArray($graph->getAttributes());
        $this->assertCount(1, $graph->getAttributes());
        $this->assertEquals('Euler Graph', $graph->getAttribute('name'));
    }

    public function testSetGraphAttributes()
    {
        $graph = new Graph();
        $graph->setAttributes(['name' => 'Euler Graph']);

        $this->assertIsArray($graph->getAttributes());
        $this->assertCount(1, $graph->getAttributes());
        $this->assertEquals('Euler Graph', $graph->getAttribute('name'));
    }

    public function testRemoveGraphAttribute()
    {
        $graph = new Graph(['name' => 'Euler Graph']);
        $graph->removeAttribute('name');

        $this->assertIsArray($graph->getAttributes());
        $this->assertCount(0, $graph->getAttributes());
        $this->assertNull($graph->getAttribute('name'));
    }
}
