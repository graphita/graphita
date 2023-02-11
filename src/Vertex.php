<?php

namespace Graphita\Graphita;

use Exception;
use Graphita\Graphita\Abstracts\AbstractEdge;
use Graphita\Graphita\Traits\AttributesHandlerTrait;

class Vertex
{
    /**
     * @var mixed
     */
    private mixed $id;

    /**
     * @var array
     */
    private array $edges = array();

    /**
     * @var array
     */
    private array $incomingEdges = array();

    /**
     * @var array
     */
    private array $outgoingEdges = array();

    /**
     * @var array
     */
    private array $neighbors = array();

    /**
     * @var array
     */
    private array $incomingNeighbors = array();

    /**
     * @var array
     */
    private array $outgoingNeighbors = array();

    /**
     * @var Graph
     */
    private Graph $graph;

    use AttributesHandlerTrait;

    /**
     * @param $id
     * @param Graph $graph
     * @param array $attributes
     */
    public function __construct($id, Graph &$graph, array $attributes = array())
    {
        $this->id = $id;
        $this->graph = $graph;
        $this->setAttributes($attributes);
    }

    /**
     * @return string
     */
    function __toString()
    {
        return 'Vertex Id:' . $this->getId() . PHP_EOL .
            'Information:' . json_encode($this->getAttributes()) . PHP_EOL .
            'Edges:' . implode(',', array_map(function ($edge) {
                return $edge->getId() . ':' . json_encode($edge->getAttributes());
            }, $this->getEdges()));
    }

    /**
     * @return int|null
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @return Graph
     */
    public function getGraph(): Graph
    {
        return $this->graph;
    }

    /**
     * @return array
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @return array
     */
    public function getIncomingEdges(): array
    {
        return $this->incomingEdges;
    }

    /**
     * @param Vertex $sourceVertex
     * @return array
     * @throws Exception
     */
    public function getIncomingEdgesFrom(Vertex $sourceVertex): array
    {
        if (!$this->hasIncomingNeighbors($sourceVertex->getId()))
            throw new Exception('Vertex ' . $this->getId() . ' has no Edge from Vertex ' . $sourceVertex->getId());
        return array_filter($this->getIncomingEdges(), function ($edge) use ($sourceVertex) {
            if ($edge instanceof UndirectedEdge) {
                return $edge->hasVertex($sourceVertex->getId());
            } else if ($edge instanceof DirectedEdge) {
                return $edge->getSource() === $sourceVertex;
            }
            return false;
        });
    }

    /**
     * @param Vertex $destinationVertex
     * @return array
     * @throws Exception
     */
    public function getOutgoingEdgesTo(Vertex $destinationVertex): array
    {
        if (!$this->hasOutgoingNeighbors($destinationVertex->getId()))
            throw new Exception('Vertex ' . $this->getId() . ' has no Edge to Vertex ' . $destinationVertex->getId());
        return array_filter($this->getOutgoingEdges(), function ($edge) use ($destinationVertex) {
            if ($edge instanceof UndirectedEdge) {
                return $edge->hasVertex($destinationVertex->getId());
            } else if ($edge instanceof DirectedEdge) {
                return $edge->getDestination() === $destinationVertex;
            }
            return false;
        });
    }

    /**
     * @return array
     */
    public function getOutgoingEdges(): array
    {
        return $this->outgoingEdges;
    }

    /**
     * @param AbstractEdge $edge
     * @return void
     * @throws Exception
     */
    public function addEdge(AbstractEdge &$edge): void
    {
        if ($edge->getGraph() !== $this->getGraph())
            throw new Exception('Edge & Vertex have to be within the same graph !');
        $this->edges[$edge->getId()] = $edge;
        if ($edge instanceof UndirectedEdge && $edge->hasVertex($this->getId())) {
            $this->incomingEdges[$edge->getId()] = $edge;
            $this->outgoingEdges[$edge->getId()] = $edge;
        } else if ($edge instanceof DirectedEdge && $edge->getDestination()->getId() == $this->getId()) {
            $this->incomingEdges[$edge->getId()] = $edge;
        } else if ($edge instanceof DirectedEdge && $edge->getSource()->getId() == $this->getId()) {
            $this->outgoingEdges[$edge->getId()] = $edge;
        } else {
            throw new Exception('Can\'t add Edge to Vertex !');
        }
        $this->calculateNeighbors();
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasEdge($id): bool
    {
        return array_key_exists($id, $this->edges);
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasIncomingEdge($id): bool
    {
        return array_key_exists($id, $this->incomingEdges);
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasOutgoingEdges($id): bool
    {
        return array_key_exists($id, $this->outgoingEdges);
    }

    /**
     * @return int
     */
    public function countEdges(): int
    {
        return count($this->edges);
    }

    /**
     * @return int
     */
    public function countIncomingEdges(): int
    {
        return count($this->incomingEdges);
    }

    /**
     * @return int
     */
    public function countOutgoingEdges(): int
    {
        return count($this->outgoingEdges);
    }

    /**
     * @param $id
     * @return bool
     */
    public function removeEdge($id): bool
    {
        if (!$this->hasEdge($id))
            return false;
        unset($this->edges[$id]);
        unset($this->incomingEdges[$id]);
        unset($this->outgoingEdges[$id]);
        if ($this->getGraph()->hasEdge($id)) {
            $this->getGraph()->removeEdge($id);
        }
        $this->calculateNeighbors();
        return true;
    }

    /**
     * @return void
     */
    private function calculateNeighbors(): void
    {
        $this->neighbors = [];
        array_map(function ($edge) {
            $neighbor = current(array_filter($edge->getVertices(), function (Vertex $vertex) {
                return $vertex->getId() != $this->getId();
            }));
            $this->neighbors[$neighbor->getId()] = $neighbor;
            if ($edge instanceof UndirectedEdge) {
                $this->incomingNeighbors[$neighbor->getId()] = $neighbor;
                $this->outgoingNeighbors[$neighbor->getId()] = $neighbor;
            } else if ($edge instanceof DirectedEdge && $edge->getDestination()->getId() == $this->getId()) {
                $this->incomingNeighbors[$neighbor->getId()] = $neighbor;
            } else if ($edge instanceof DirectedEdge && $edge->getSource()->getId() == $this->getId()) {
                $this->outgoingNeighbors[$neighbor->getId()] = $neighbor;
            }
        }, $this->getEdges());
    }

    /**
     * @return array
     */
    public function getNeighbors(): array
    {
        return $this->neighbors;
    }

    /**
     * @return array
     */
    public function getIncomingNeighbors(): array
    {
        return $this->incomingNeighbors;
    }

    /**
     * @return array
     */
    public function getOutgoingNeighbors(): array
    {
        return $this->outgoingNeighbors;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasNeighbor($id): bool
    {
        return array_key_exists($id, $this->neighbors);
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasIncomingNeighbors($id): bool
    {
        return array_key_exists($id, $this->incomingNeighbors);
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasOutgoingNeighbors($id): bool
    {
        return array_key_exists($id, $this->outgoingNeighbors);
    }

    /**
     * @return int
     */
    public function countNeighbors(): int
    {
        return count($this->neighbors);
    }

    /**
     * @return int
     */
    public function countIncomingNeighbors(): int
    {
        return count($this->incomingNeighbors);
    }

    /**
     * @return int
     */
    public function countOutgoingNeighbors(): int
    {
        return count($this->outgoingNeighbors);
    }
}