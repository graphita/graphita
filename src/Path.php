<?php

namespace Graphita\Graphita;

class Path extends Abstracts\AbstractWalk
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