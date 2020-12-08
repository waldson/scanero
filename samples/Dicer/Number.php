<?php
namespace W5n\Samples\Dicer;

class Number implements Token
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue(?Context $context = null): int
    {
        return $this->value;
    }
}
