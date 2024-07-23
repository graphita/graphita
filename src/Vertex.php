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
    private string $id;

    /**
     * @var Graph
     */
    private Graph $graph;

    /**
     * @var array
     */
    private array $edges = array();

    use AttributesHandlerTrait;

    /**
     * @param string $id
     * @param Graph $graph
     * @param array $attributes
     */
    public function __construct(string $id, Graph &$graph, array $attributes = array())
    {
        $this->id = $id;
        $this->graph = $graph;
        $this->setAttributes($attributes);
    }

    /**
     * @return string
     */
    public function getId(): string
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
        return array_filter($this->getEdges(), function (AbstractEdge $edge) {
            if(
                $edge instanceof UndirectedEdge ||
                (
                    $edge instanceof DirectedEdge && $edge->getDestination()->getId() == $this->getId()
                )
            ){
                return true;
            }
            return false;
        });
    }

    /**
     * @param Vertex $sourceVertex
     * @return array
     */
    public function getIncomingEdgesFrom(Vertex $sourceVertex): array
    {
        return array_filter($this->getIncomingEdges(), function (AbstractEdge $edge) use ($sourceVertex) {
            if(
                (
                    $edge instanceof UndirectedEdge && $edge->hasVertex($sourceVertex->getId())
                ) ||
                (
                    $edge instanceof DirectedEdge && $edge->getSource()->getId() == $sourceVertex->getId()
                )
            ){
                return true;
            }
            return false;
        });
    }

    /**
     * @return array
     */
    public function getOutgoingEdges(): array
    {
        return array_filter($this->getEdges(), function (AbstractEdge $edge) {
            if(
                $edge instanceof UndirectedEdge ||
                (
                    $edge instanceof DirectedEdge && $edge->getSource()->getId() == $this->getId()
                )
            ){
                return true;
            }
            return false;
        });
    }

    /**
     * @param Vertex $destinationVertex
     * @return array
     */
    public function getOutgoingEdgesTo(Vertex $destinationVertex): array
    {
        return array_filter($this->getOutgoingEdges(), function (AbstractEdge $edge) use ($destinationVertex) {
            if(
                (
                    $edge instanceof UndirectedEdge && $edge->hasVertex($destinationVertex->getId())
                ) ||
                (
                    $edge instanceof DirectedEdge && $edge->getDestination()->getId() == $destinationVertex->getId()
                )
            ){
                return true;
            }
            return false;
        });
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

        if (array_key_exists($edge->getId(), $this->edges))
            throw new Exception('Vertex ' . $this->getId() . ' has connected to Edge ' . $edge->getId() . ' before !');

        $this->edges[$edge->getId()] = $edge;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasEdge($id): bool
    {
        return array_key_exists($id, $this->getEdges());
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasIncomingEdge($id): bool
    {
        return array_key_exists($id, $this->getIncomingEdges());
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasOutgoingEdges($id): bool
    {
        return array_key_exists($id, $this->getOutgoingEdges());
    }

    /**
     * @return int
     */
    public function countEdges(): int
    {
        return count($this->getEdges());
    }

    /**
     * @return int
     */
    public function countIncomingEdges(): int
    {
        return count($this->getIncomingEdges());
    }

    /**
     * @return int
     */
    public function countOutgoingEdges(): int
    {
        return count($this->getOutgoingEdges());
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
        if ($this->getGraph()->hasEdge($id)) {
            $this->getGraph()->removeEdge($id);
        }

        return true;
    }

    /**
     * @return array
     */
    public function getNeighbors(): array
    {
        return array_map(function (AbstractEdge $edge) {
            $vertices = $edge->getVertices();
            $verticesId = array_keys($vertices);
            return $vertices[$verticesId[0]]->getId() == $this->getId() ? $vertices[$verticesId[1]] : $vertices[$verticesId[0]];
        }, $this->getEdges());
    }

    /**
     * @return array
     */
    public function getIncomingNeighbors(): array
    {
        return array_map(function (AbstractEdge $edge) {
            $vertices = $edge->getVertices();
            $verticesId = array_keys($vertices);
            return $vertices[$verticesId[0]]->getId() == $this->getId() ? $vertices[$verticesId[1]] : $vertices[$verticesId[0]];
        }, $this->getIncomingEdges());
    }

    /**
     * @return array
     */
    public function getOutgoingNeighbors(): array
    {
        return array_map(function (AbstractEdge $edge) {
            $vertices = $edge->getVertices();
            $verticesId = array_keys($vertices);
            return $vertices[$verticesId[0]]->getId() == $this->getId() ? $vertices[$verticesId[1]] : $vertices[$verticesId[0]];
        }, $this->getOutgoingEdges());
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasNeighbor($id): bool
    {
        foreach ($this->getNeighbors() as $neighbor) {
            if ($neighbor->getId() == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasIncomingNeighbors($id): bool
    {
        foreach ($this->getIncomingNeighbors() as $neighbor) {
            if ($neighbor->getId() == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasOutgoingNeighbors($id): bool
    {
        foreach ($this->getOutgoingNeighbors() as $neighbor) {
            if ($neighbor->getId() == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function countNeighbors(): int
    {
        return count($this->getNeighbors());
    }

    /**
     * @return int
     */
    public function countIncomingNeighbors(): int
    {
        return count($this->getIncomingNeighbors());
    }

    /**
     * @return int
     */
    public function countOutgoingNeighbors(): int
    {
        return count($this->getOutgoingNeighbors());
    }
}