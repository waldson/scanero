<?php
declare(strict_types=1);

namespace W5n\Samples\Dicer;

interface RandomGenerator
{
    public function generate(int $min, int $max);
}
