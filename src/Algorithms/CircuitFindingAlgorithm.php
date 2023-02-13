<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Circuit;

class CircuitFindingAlgorithm extends WalkFindingAlgorithm
{
    const TRAVERSE_TYPE = Circuit::class;
}