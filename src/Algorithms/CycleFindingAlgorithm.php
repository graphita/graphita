<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Walks\Cycle;

class CycleFindingAlgorithm extends WalkFindingAlgorithm
{
    const TRAVERSE_TYPE = Cycle::class;
}