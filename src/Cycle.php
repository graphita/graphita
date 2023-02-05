<?php

namespace Graphita\Graphita;

class Cycle extends Walk
{
    /**
     * @var bool
     */
    private bool $repeatVertices = false;

    /**
     * @var bool
     */
    private bool $repeatEdges = false;

    /**
     * @var bool
     */
    private bool $isLoop = true;
}