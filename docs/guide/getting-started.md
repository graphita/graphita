# Getting Started

Welcome to **Graphita Version 2**. Graphita is a lightweight, strictly-typed PHP package designed to model complex networks, map relationships, and calculate optimal traversal paths.

## Installation

Install Graphita via Composer. Ensure your server is running PHP 7.4 or higher.

```bash
composer require graphita/graphita:^2.0
```

## Quick Start Guide

Creating a graph, connecting nodes, and finding a path is incredibly straightforward:

```php
use Graphita\Graphita\Graph;
use Graphita\Graphita\Algorithms\PathFindingAlgorithm;

// 1. Initialize the Graph
$graph = new Graph();

// 2. Add Vertices
$graph->createVertex('A');
$graph->createVertex('B');
$graph->createVertex('C');

// 3. Connect Vertices with Edges
$graph->createDirectedEdge('A', 'B');
$graph->createDirectedEdge('B', 'C');

// 4. Find Paths
$algo = new PathFindingAlgorithm($graph);
$algo->setSource('A')
     ->setDestination('C')
     ->calculate();

// 5. Retrieve Results
$bestPath = $algo->getShortestResult();
print_r($bestPath->getVertices()); // Output: ['A', 'B', 'C']
```