# Graphita

[![Latest Packagist Version](https://img.shields.io/packagist/v/graphita/graphita?logo=github&logoColor=white&style=flat-square)](https://packagist.org/packages/graphita/graphita)
[![Total Downloads](https://img.shields.io/packagist/dt/graphita/graphita.svg?logo=github&logoColor=white&style=flat-square)](https://packagist.org/packages/graphita/graphita)
[![GitHub Checks Status](https://img.shields.io/github/actions/workflow/status/graphita/graphita/php.yml?logo=github-actions&logoColor=white&style=flat-square)](https://github.com/graphita/graphita/actions)
[![Quality Score](https://img.shields.io/scrutinizer/quality/g/graphita/graphita.svg?logo=scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/graphita/graphita)

An enterprise-grade, high-performance PHP Graph Theory and Pathfinding Engine.

**Version 2.0** is a complete architectural rewrite utilizing flat-relational mapping, strict string IDs, and $O(1)$ hash map lookups. It is designed to navigate massive graph topologies and resolve complex mathematical routing with almost zero RAM footprint.

## Enterprise Feature Set

Graphita provides **11 strictly-typed algorithms** divided into three domains to solve any network mapping, dependency, or routing problem:

* **Shortest Path Routing:** Instantaneous point-to-point pathfinding using **Dijkstra** (weighted), **Breadth-First Search** (fewest hops), **A\*** (heuristics/GPS), and **Bellman-Ford** (negative weight detection).
* **Structural Analysis:** Resolve entire network architectures using **Topological Sorting** (dependency resolution) and **Kruskal's Minimum Spanning Tree** (network cost optimization).
* **Exhaustive Traversals:** A custom Recursive Depth-First Search (DFS) engine that mathematically validates strict Graph Theory movements, including **Walks**, **Paths**, **Trails**, **Circuits**, and **Cycles**.

## Installation

You can install the package via composer:

```bash
composer require graphita/graphita
```

## Quick Start Example

Building a network and calculating the optimal shortest path takes just a few lines of code:

```php
use Graphita\Graphita\Graph;
use Graphita\Graphita\Algorithms\DijkstraAlgorithm;

// 1. Initialize the Graph
$graph = new Graph();

// 2. Add Vertices
$graph->createVertex('NewYork');
$graph->createVertex('Chicago');
$graph->createVertex('LosAngeles');

// 3. Connect Vertices with Edges
$graph->createDirectedEdge('NewYork', 'Chicago')->setWeight(800);
$graph->createDirectedEdge('Chicago', 'LosAngeles')->setWeight(2000);
$graph->createDirectedEdge('NewYork', 'LosAngeles')->setWeight(2900); // More expensive direct flight

// 4. Find the absolute best Route
$algo = new DijkstraAlgorithm($graph);
$algo->setSource('NewYork')
     ->setDestination('LosAngeles')
     ->calculate();

// 5. Retrieve Results
$bestPath = $algo->getShortestResult();

print_r($bestPath->getVertices()); 
// Output: ['NewYork', 'Chicago', 'LosAngeles']

echo "Total Cost: $" . $bestPath->getTotalWeight(); 
// Output: Total Cost: $2800
```

## Documentation

Comprehensive documentation, tutorials, architecture explanations, and API examples are available on our official VitePress documentation site:

**📚 [Read the Graphita Documentation](https://graphita.github.io/graphita/)**

If you are upgrading from `v1.x`, please consult the [Upgrading to V2 Guide](https://graphita.github.io/graphita/guide/upgrading) to familiarize yourself with the new String ID architecture and algorithm classes.

*(Note: The old GitHub Wiki pages have been deprecated in favor of the new documentation site).*

## Requirements

The current package requirements are:

- PHP >= 7.4

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.