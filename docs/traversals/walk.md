# Walks

In graph theory, a **Walk** is the most foundational type of traversal. It is a sequence of alternating vertices and edges.

> **Mathematical Rule:** A Walk has no restrictions. It is perfectly valid to repeat the same vertices and the same edges infinitely.

## Manual Construction & Data Retrieval

While algorithms typically generate walks for you, you can manually construct and validate a walk. You can easily extract the sequence of steps and the accumulated weight.

```php
use Graphita\Graphita\Walks\Walk;

$walk = new Walk($graph);
$walk->start('A');

// Add steps by providing the next vertex and the edge used to get there
$walk->addStep('B', 'Edge_1'); // Weight: 1.0
$walk->addStep('A', 'Edge_2'); // Bouncing back is valid. Weight: 1.0
$walk->addStep('C', 'Edge_3'); // Weight: 2.5

// Lock the traversal
$walk->finish();

// 1. Structural Data
echo $walk->countEdges();     // 3
echo $walk->getTotalWeight(); // 4.5

// 2. Extracting the Route
$vertices = $walk->getVertices(); 
// ['A', 'B', 'A', 'C']

$edges = $walk->getEdges();       
// ['Edge_1', 'Edge_2', 'Edge_3']

$fullSequence = $walk->getSteps(); 
// ['A', 'Edge_1', 'B', 'Edge_2', 'A', 'Edge_3', 'C']

// 3. Inspecting Endpoints
echo $walk->getFirstStep(); // "A"
echo $walk->getLastStep();  // "C"
```