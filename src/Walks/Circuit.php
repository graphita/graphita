<?php

namespace Graphita\Graphita\Walks;

class Circuit extends Walk
{
    /**
     * A circuit cannot repeat edges.
     */
    const REPEAT_EDGES = false;

    /**
     * A circuit must be a closed loop.
     */
    const IS_LOOP = true;
}