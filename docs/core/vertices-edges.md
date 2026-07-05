# Vertices & Edges

Vertices (nodes) represent the entities in your network, while Edges represent the paths between them. Both utilize Graphita's `AttributesHandlerTrait`, allowing you to store rich, custom data payloads directly on the graph elements.

## Vertices

Vertices are explicitly identified by a unique String ID. If you attempt to create a vertex with an ID that already exists, Graphita will throw a `LogicException`.

### Creation and Attributes

```php
$vertex = $graph->createVertex('NYC', [
    'type' => 'city', 
    'population' => 8000000,
    'is_hub' => true
]);

// Accessing the ID
echo $vertex->getId(); // "NYC"

// Manipulating Attributes
$vertex->setAttribute('mayor', 'John Doe');

if ($vertex->hasAttribute('population')) {
    echo $vertex->getAttribute('population'); // 8000000
}

// Retrieve the entire payload
$payload = $vertex->getAttributes();
```

## Edges

Graphita natively supports multigraphs and mixed graphs (graphs containing both directed and undirected edges). Edges are automatically assigned a generated ID (e.g., `Edge_1`, `Edge_2`).

### Creation and Weights

By default, every edge has a weight of `1.0`. You can adjust this to represent distance, cost, time, or resistance for pathfinding computations.

```php
// A Directed Edge (One-way street from A to B)
$directed = $graph->createDirectedEdge('A', 'B', ['type' => 'highway']);
$directed->setWeight(250.5); // Set custom weight (e.g., 250.5 miles)

// An Undirected Edge (Two-way street between B and C)
$undirected = $graph->createUndirectedEdge('B', 'C', ['type' => 'local_road']);
$undirected->setWeight(15.0);

// Reading edge data
echo $directed->getId();     // "Edge_1"
echo $directed->getWeight(); // 250.5
```

### Inspecting Edge Endpoints

You can inspect the structural connections of any edge directly.

```php
// Get the endpoints
$endpoints = $directed->getEndpointIds(); // ['A', 'B']

// Check if a specific vertex is part of this edge
$isConnected = $directed->hasVertexId('A'); // true
$isConnected = $directed->hasVertexId('C'); // false

// Directed edges have specific source/destination methods
echo $directed->getSourceId();      // "A"
echo $directed->getDestinationId(); // "B"
```