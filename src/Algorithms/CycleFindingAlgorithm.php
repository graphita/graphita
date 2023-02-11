<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Cycle;

class CycleFindingAlgorithm extends WalkFindingAlgorithm
{
    private string $traversType = Cycle::class;
}