<?php

namespace Graphita\Graphita;

class Cycle extends Abstracts\AbstractWalk
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