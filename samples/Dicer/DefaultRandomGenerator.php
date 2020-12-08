<?php
namespace W5n\Samples\Dicer;

class DefaultRandomGenerator implements RandomGenerator
{
    public function generate(int $min, int $max)
    {
        return mt_rand($min, $max);
    }
}
