<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Circuit;

class CircuitFindingAlgorithm extends WalkFindingAlgorithm
{
    private string $traversType = Circuit::class;
}