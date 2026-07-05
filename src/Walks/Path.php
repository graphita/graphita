<?php

namespace Graphita\Graphita\Walks;

class Path extends Walk
{
    /**
     * A path cannot repeat vertices.
     */
    const REPEAT_VERTICES = false;

    /**
     * A path cannot repeat edges.
     */
    const REPEAT_EDGES = false;
}