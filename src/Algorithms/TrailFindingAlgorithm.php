<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Trail;

class TrailFindingAlgorithm extends WalkFindingAlgorithm
{
    private string $traversType = Trail::class;
}