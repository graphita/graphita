# Structural Analysis

These algorithms look at the entire graph as a whole to resolve macro-level architecture problems.

---

## Topological Sorting
Topological sorting takes a Directed Acyclic Graph (DAG) and untangles it into a flat, sequential list. It determines the exact order in which dependent nodes must be processed.

**When to use:** Task runners, compiling code, resolving software dependencies (like Composer packages), or university prerequisite mapping.

```php
use Graphita\Graphita\Algorithms\TopologicalSortAlgorithm;

// 1. Create dependencies
$graph->createDirectedEdge('Install_PHP', 'Install_Composer');
$graph->createDirectedEdge('Install_Composer', 'Install_Graphita');

$algo = new TopologicalSortAlgorithm($graph);
$algo->calculate();

// Returns: ['Install_PHP', 'Install_Composer', 'Install_Graphita']
print_r($algo->getResults());
```

> **Circular Dependencies:** If you accidentally create a loop (e.g., Task A requires B, and B requires A), the algorithm instantly throws a `LogicException`.

---

## Kruskal's Minimum Spanning Tree (MST)
Instead of finding a path from A to B, an MST finds the cheapest possible way to connect **every single node** in the graph together without creating any loops.

Instead of returning an array of paths, it returns a completely new, optimized `Graph` object.

**When to use:** Laying fiber-optic cables between cities, designing electrical grids, or clustering data sets where minimizing total material/cost is critical.

```php
use Graphita\Graphita\Algorithms\KruskalAlgorithm;

$algo = new KruskalAlgorithm($graph);
$algo->calculate();

// Extract the optimized tree
$mstGraph = $algo->getResultGraph();

echo "Original Edges: " . $graph->countEdges();
echo "Optimized Tree Edges: " . $mstGraph->countEdges();

// Iterate over the finalized, optimized network
foreach ($mstGraph->getEdges() as $edge) {
    echo "Keep connection: " . $edge->getId() . "\n";
}
```