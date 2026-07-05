# Upgrading to Version 2.0

Graphita V2 is a complete architectural rewrite designed for enterprise performance. If you are upgrading from V1, here are the breaking changes you need to address.

## 1. String IDs vs. Object Passing
In V1, you passed heavy `Vertex` objects directly into algorithms and methods. In V2, Graphita uses a highly optimized flat-relational model. **You must now pass String IDs.**

**V1 (Deprecated):**
```php
$algo->addSource($vertexObject);
```

**V2 (Current):**
```php
$algo->addSource('vertex_id_1');
```

## 2. No Pass-By-Reference Dependencies
The V1 constructors heavily relied on `&` pass-by-reference configurations (`new Algorithm(&$graph)`). V2 utilizes strict object handling, eliminating the need for reference pointers.

## 3. Weight Calculation Moved
The `calculateTotalWeight()` method has been removed from the `Walk` object. Weights are now strictly aggregated continuously during object creation, and sorting logic is natively handled by the Algorithm classes via `$algo->sortResults()`.

## 4. Strict Loop Pre-flight Checks
In V2, attempting to run a loop-based algorithm (`CircuitFindingAlgorithm` or `CycleFindingAlgorithm`) with mismatched Source and Destination IDs will instantly throw a `LogicException`.