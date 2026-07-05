# Circuits & Cycles

Loops are traversals that start and end on the exact same vertex. Graphita provides two distinct loop types depending on your strictness requirements.

## 1. Circuit
A **Circuit** is a closed Trail.
* Must start and end on the same vertex.
* **Cannot** repeat edges.
* **CAN** repeat vertices (meaning the loop can make a "Figure-8").

```php
use Graphita\Graphita\Walks\Circuit;

$circuit = new Circuit($graph);
$circuit->start('Center');
$circuit->addStep('North', 'Edge_1');
$circuit->addStep('Center', 'Edge_2'); // Valid: Inner loop crossing
$circuit->addStep('South', 'Edge_3');
$circuit->addStep('Center', 'Edge_4'); // Valid: Closes the outer loop

$circuit->finish(); // Success
```

## 2. Cycle
A **Cycle** is a closed Path (a Simple Cycle).
* Must start and end on the same vertex.
* **Cannot** repeat edges.
* **CANNOT** repeat vertices (it must be a single, clean ring).

```php
use Graphita\Graphita\Walks\Cycle;
use Exception;

$cycle = new Cycle($graph);
$cycle->start('A');
$cycle->addStep('B', 'Edge_1');
$cycle->addStep('C', 'Edge_2');

try {
    // This would create a Figure-8, which is invalid for a Cycle!
    $cycle->addStep('B', 'Edge_3'); 
} catch (Exception $e) {
    echo $e->getMessage(); // "You can't Repeat Vertex!"
}

// Closing the clean ring back to the start node
$cycle->addStep('A', 'Edge_4');
$cycle->finish(); // Success
```