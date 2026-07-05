# The Graph

The `Graph` class is the central repository and relationship manager for your entire network. It acts as the primary factory for creating and managing vertices and edges.

## Instantiation & Meta Data

You can initialize a Graph with optional meta-attributes. Since the Graph uses the `AttributesHandlerTrait`, you can store and retrieve data on it at any time.

```php
use Graphita\Graphita\Graph;

// Initialize with attributes
$graph = new Graph([
    'name' => 'Transport Network',
    'version' => '2.0',
    'active' => true
]);

// Update or add attributes later
$graph->setAttribute('last_updated', '2026-07-04');

// Retrieve attributes
echo $graph->getAttribute('name'); // "Transport Network"
```

## Managing Vertices & Edges

Instead of instantiating Vertex or Edge objects manually, you use the Graph as a factory. This guarantees that relationships are properly tracked in the background.

```php
// 1. Create Vertices
$graph->createVertex('NewYork');
$graph->createVertex('London');
$graph->createVertex('Paris');

// 2. Create Edges
// Directed: One-way from New York to London
$flight1 = $graph->createDirectedEdge('NewYork', 'London');

// Undirected: Two-way train between London and Paris
$train1 = $graph->createUndirectedEdge('London', 'Paris');
```

## Retrieval & Existence Checks

You can quickly check if elements exist or retrieve them. Trying to retrieve an element that doesn't exist will throw an `OutOfBoundsException`.

```php
if ($graph->hasVertex('Tokyo')) {
    $vertex = $graph->getVertex('Tokyo');
}

// Get all vertices or edges in the network
$allVertices = $graph->getVertices(); // Returns array of Vertex objects
$allEdges = $graph->getEdges();       // Returns array of AbstractEdge objects

// Count elements
echo $graph->countVertices(); // 3
echo $graph->countEdges();    // 2
```

## Neighbor Lookups

Because the Graph maps relationships natively, retrieving adjacent nodes is instantaneous ($O(1)$ hash map lookups).

```php
// Get ALL connected vertices (ignores directionality)
$allNeighbors = $graph->getNeighbors('London'); 
// Returns ['NewYork' => Vertex, 'Paris' => Vertex]

// Get vertices where edges point FROM 'London' TO the neighbor
$outgoing = $graph->getOutgoingNeighbors('London');
// Returns ['Paris' => Vertex] (Because NewYork -> London is one-way)

// Get vertices where edges point FROM the neighbor TO 'London'
$incoming = $graph->getIncomingNeighbors('London');
// Returns ['NewYork' => Vertex, 'Paris' => Vertex]
```

## Removing Elements

Removing a vertex automatically cascades and cleans up all edges attached to it, preventing orphaned data.

```php
$graph->removeVertex('Paris'); 
// The 'London' <-> 'Paris' edge is automatically destroyed
```