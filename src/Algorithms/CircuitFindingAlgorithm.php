<?php

namespace Graphita\Graphita\Algorithms;

use Graphita\Graphita\Walks\Circuit;

class CircuitFindingAlgorithm extends WalkFindingAlgorithm
{
    const TRAVERSE_TYPE = Circuit::class;
}