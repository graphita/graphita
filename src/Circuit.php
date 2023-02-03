<?php

namespace Graphita\Graphita;

class Circuit extends Abstracts\AbstractWalk
{
    /**
     * @var bool
     */
    private bool $repeatEdges = false;

    /**
     * @var bool
     */
    private bool $isLoop = true;
}