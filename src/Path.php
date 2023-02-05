<?php

namespace Graphita\Graphita;

class Path extends Walk
{
    /**
     * @var bool
     */
    private bool $repeatVertices = false;

    /**
     * @var bool
     */
    private bool $repeatEdges = false;
}