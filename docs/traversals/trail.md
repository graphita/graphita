# Trails

A **Trail** is a specific type of traversal common in routing and Eulerian mathematics.

> **Mathematical Rule:** A Trail allows you to cross the same vertex multiple times, but you can NEVER use the same edge twice.

## Usage & Validation

Trails are highly useful when mapping delivery routes, garbage collection, or network packets that cross through a central hub multiple times without retracing the exact same physical wire or road.

```php
use Graphita\Graphita\Walks\Trail;
use Exception;

$trail = new Trail($graph);
$trail->start('Hub');

// Go Hub -> Node_A (via Edge_1)
$trail->addStep('Node_A', 'Edge_1');

// Return Node_A -> Hub (via Edge_2)
// VALID: We are reusing the 'Hub' vertex, but using a different edge.
$trail->addStep('Hub', 'Edge_2'); 

try {
    // Attempt to go Hub -> Node_A again using the SAME Edge_1
    $trail->addStep('Node_A', 'Edge_1');
} catch (Exception $e) {
    echo $e->getMessage(); // Output: "You can't Repeat Edge!"
}

$trail->finish();
```