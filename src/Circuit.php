<?php

namespace Graphita\Graphita;

class Circuit extends Walk
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