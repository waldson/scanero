<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use W5n\Samples\Dicer\DiceEngine;
use W5n\Samples\Dicer\DiceParser;
use W5n\Samples\Dicer\DefaultRandomGenerator;

class SampleTest extends TestCase
{
    /** @test */
    public function test_name()
    {
        $parser = new DiceParser();

        $engine = new DiceEngine(
            $parser,
            new DefaultRandomGenerator()
        );

        $result = $engine->roll('2d4+3d6+2');
        dd($engine);
        dd($result);
    }
}
