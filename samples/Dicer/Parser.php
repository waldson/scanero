<?php
namespace W5n\Samples\Dicer;

interface Parser
{
    public function parse(string $roll): Token;
}
