<?php

namespace Graphita\Graphita\Walks;

class Cycle extends Walk
{
    /**
     * A cycle cannot repeat vertices (except start/end).
     */
    const REPEAT_VERTICES = false;

    /**
     * A cycle cannot repeat edges.
     */
    const REPEAT_EDGES = false;

    /**
     * A cycle must be a closed loop.
     */
    const IS_LOOP = true;
}