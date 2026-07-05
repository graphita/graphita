# Paths

A **Path** is a strict traversal used to get from Point A to Point B efficiently.

> **Mathematical Rule:** A Path cannot repeat any vertices, and consequently, cannot repeat any edges.

## Usage & Validation

If you attempt to cross a vertex you have already visited, the `Path` class will actively reject the step to protect the mathematical integrity of the route.

```php
use Graphita\Graphita\Walks\Path;
use Exception;

$path = new Path($graph);
$path->start('1');
$path->addStep('2', 'Edge_1');
$path->addStep('3', 'Edge_2');

try {
    // Attempting to return to vertex '1'
    $path->addStep('1', 'Edge_3'); 
} catch (Exception $e) {
    echo $e->getMessage(); // Output: "You can't Repeat Vertex!"
}

$path->finish();

print_r($path->getVertices()); // ['1', '2', '3']
```