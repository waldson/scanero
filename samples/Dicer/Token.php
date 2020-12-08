<?php
namespace W5n\Samples\Dicer;

interface Token
{
    public function getValue(?Context $context = null): int;
}
