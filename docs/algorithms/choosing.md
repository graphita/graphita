# Which Algorithm Should I Use?

Graphita Version 2 comes with **11 dedicated algorithms**, separated into three main categories. Depending on the math you need to perform, choosing the right algorithm guarantees maximum performance and accurate results.

## 1. Shortest Path Routing (Point A to Point B)
These algorithms are designed to find the absolute best way to get from a Source to a Destination. They stop searching the moment the optimal route is found.

| Use Case | Recommended Algorithm | Time Complexity |
|---|---|---|
| Finding the shortest physical distance/cost. | **Dijkstra** | $O(E \log V)$ |
| Finding the fewest "hops" or edges (Unweighted). | **Breadth-First Search (BFS)** | $O(V + E)$ |
| Finding shortest path using GPS coordinates/heuristics. | **A* (A-Star)** | $O(E)$ (approx) |
| Finding shortest path where weights might be negative. | **Bellman-Ford** | $O(V \times E)$ |

## 2. Structural Analysis (Graph-Wide)
These algorithms look at the entire graph as a whole to resolve macro-level problems, rather than finding a path between two specific points.

| Use Case | Recommended Algorithm | Output |
|---|---|---|
| Resolving dependency order (e.g., Task A before Task B). | **Topological Sort** | `Array` (Sequential) |
| Connecting all nodes using the absolute minimum cost. | **Kruskal's MST** | `Graph` Object |

## 3. Exhaustive Traversals (DFS Engine)
These algorithms use an intensely optimized Depth-First Search to find **every single mathematically possible route** between two endpoints.

* **Walk Finding:** Can repeat edges and vertices infinitely.
* **Path Finding:** Strictly cannot repeat anything.
* **Trail Finding:** Can repeat vertices, cannot repeat edges.
* **Circuit Finding:** A closed Trail (loop).
* **Cycle Finding:** A closed Path (clean ring).

> **Warning:** Because these algorithms find *all* routes, they are slower on massive graphs than the Shortest Path algorithms. Use them only when you explicitly need exhaustive data or strict mathematical loop detection.