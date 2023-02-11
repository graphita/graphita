<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Path;

class PathFindingAlgorithm extends WalkFindingAlgorithm
{
    private string $traversType = Path::class;
}