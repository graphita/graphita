<?php

namespace Graphita\Graphita\Walks;

class Trail extends Walk
{
    /**
     * A trail cannot repeat edges, but can repeat vertices.
     */
    const REPEAT_EDGES = false;
}