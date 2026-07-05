# Exhaustive Traversals (DFS)

Graph theory has strict definitions for different types of movement. Graphita provides five specialized Depth-First Search (DFS) algorithms designed to find **every possible mathematical route** matching these rules.

## The Infinity Guard

When using traversals that allow repeating elements (`Walk`, `Trail`, `Circuit`), there are mathematically infinite ways to bounce around a graph. To protect your server from stack-overflows, **Graphita requires strict boundaries for these algorithms**.

```php
use Graphita\Graphita\Algorithms\WalkFindingAlgorithm;

$algo = new WalkFindingAlgorithm($graph);
$algo->setSource('A')->setDestination('B');

// This will throw a LogicException to protect your memory!
// $algo->calculate(); 

// Correct: Apply exact or bounding constraints
$algo->setMinSteps(2);
$algo->setMaxSteps(10); // Stops searching branches deeper than 10 steps
$algo->calculate();
```

## 1. Path Finding (Strict Non-Repeating)
Finds all routes where **no vertices and no edges** are ever repeated.
```php
use Graphita\Graphita\Algorithms\PathFindingAlgorithm;

$algo = new PathFindingAlgorithm($graph);
$algo->setSource('A')->setDestination('B')->calculate();

$allCleanRoutes = $algo->getResults();
```

## 2. Walk Finding (Unrestricted)
Finds all routes, allowing infinite bouncing between vertices and edges. *(Requires step limits).*
```php
use Graphita\Graphita\Algorithms\WalkFindingAlgorithm;

$algo = new WalkFindingAlgorithm($graph);
$algo->setSource('A')->setDestination('B');
$algo->setMaxSteps(5)->calculate();
```

## 3. Trail Finding (Edge-Strict)
Finds routes where you can cross the same vertex multiple times, but you can **never use the same edge twice**. *(Requires step limits).*
```php
use Graphita\Graphita\Algorithms\TrailFindingAlgorithm;

$algo = new TrailFindingAlgorithm($graph);
$algo->setSource('Warehouse')->setDestination('Store');
$algo->setMaxSteps(10)->calculate();
```

## Finding Loops (Circuits & Cycles)

When searching for a closed loop, the Source and Destination must be mathematically identical.

* **Circuit:** A closed Trail. Starts/ends on the same node, can repeat internal nodes (making Figure-8s). *(Requires step limits).*
* **Cycle:** A closed Path. Starts/ends on the same node, cannot repeat internal nodes (a clean ring).

```php
use Graphita\Graphita\Algorithms\CycleFindingAlgorithm;

$algo = new CycleFindingAlgorithm($graph);

// For loop traversals, Source and Destination MUST be the same
$algo->addSource('A');
$algo->addDestination('A');
$algo->calculate();

echo "Found " . $algo->countResults() . " valid cycles!";
```