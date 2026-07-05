# Shortest Path Routing

Graphita's shortest path algorithms are designed for speed. They evaluate the graph and guarantee mathematically optimal point-to-point routing.

---

## Dijkstra's Algorithm
Dijkstra is the industry standard for finding the shortest path in a **weighted** graph (where edges represent distance, time, or cost).

**When to use:** GPS mapping, cheapest flight routing, or logistics planning.

```php
use Graphita\Graphita\Algorithms\DijkstraAlgorithm;

$algo = new DijkstraAlgorithm($graph);
$algo->setSource('Warehouse_A')
     ->setDestination('Customer_Z')
     ->calculate();

$bestRoute = $algo->getShortestResult();
echo "Cheapest Delivery Cost: $" . $bestRoute->getTotalWeight();
```

---

## Breadth-First Search (BFS)
BFS ignores edge weights completely. It expands layer-by-layer to find the path with the **absolute fewest number of edges (hops)**.

**When to use:** Six degrees of separation, social network mutual connections, or IP packet routing.

```php
use Graphita\Graphita\Algorithms\BreadthFirstSearchAlgorithm;

$algo = new BreadthFirstSearchAlgorithm($graph);
$algo->setSource('User_John')
     ->setDestination('User_Sarah')
     ->calculate();

$path = $algo->getShortestResult();
echo "John and Sarah are separated by " . $path->countEdges() . " mutual connections.";
```

---

## A* (A-Star) Search
A* is a "smart" version of Dijkstra. Instead of searching equally in all directions, it uses a custom **Heuristic** (a best-guess mathematical formula) to pull the search algorithm directly toward the destination.

**When to use:** Video game AI movement or grid-based geographical routing.

```php
use Graphita\Graphita\Algorithms\AStarAlgorithm;

$algo = new AStarAlgorithm($graph);
$algo->setSource('City_1')->setDestination('City_2');

// Provide a custom Heuristic calculating straight-line distance
$algo->setHeuristic(function (string $current, string $target) use ($graph) {
    $v1 = $graph->getVertex($current);
    $v2 = $graph->getVertex($target);
    
    // Pythagorean theorem using vertex attributes
    $dx = $v1->getAttribute('x') - $v2->getAttribute('x');
    $dy = $v1->getAttribute('y') - $v2->getAttribute('y');
    
    return sqrt(($dx * $dx) + ($dy * $dy));
});

$algo->calculate();
```

---

## Bellman-Ford Algorithm
Dijkstra's Algorithm mathematically breaks if your graph contains **negative edge weights**. Bellman-Ford is specifically designed to handle negative weights and can detect "Negative Weight Cycles" (infinite loops of descending value).

**When to use:** Complex financial trading algorithms, arbitrage loops, or systems with debt/refund routing.

```php
use Graphita\Graphita\Algorithms\BellmanFordAlgorithm;

// Imagine edge weights represent money lost (positive) or money gained (negative)
$algo = new BellmanFordAlgorithm($graph);

try {
    $algo->setSource('USD_Account')
         ->setDestination('EUR_Account')
         ->calculate();
         
    $optimalTradePath = $algo->getShortestResult();
    
} catch (\LogicException $e) {
    // Catches mathematically impossible negative-weight cycles!
    echo "Arbitrage infinite money loop detected!";
}
```