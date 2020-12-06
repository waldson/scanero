<?php
declare(strict_types=1);

namespace W5n;

class Scanner
{
    private $text;
    private $cursor;
    private $lastPosition;

    public function __construct($text)
    {
        $this->text         = $text;
        $this->cursor       = 0;
        $this->lastPosition = strlen($text);
    }

    public function peek(int $count = 1): ?string
    {
        if ($this->consumed()) {
            return null;
        }

        $maxSize = $this->lastPosition - $this->cursor;

        if ($maxSize <= 0) {
            return null;
        }

        return substr($this->text, $this->cursor, min($count, $maxSize));
    }

    public function consumed(): bool
    {
        return $this->cursor >= $this->lastPosition;
    }

    public function matches(string $testString): bool
    {
        return $this->peek(strlen($testString)) == $testString;
    }

    public function consumeWhitespaces(): void
    {
        $this->consumeWhile('#\s#');
    }

    public function getCursorPosition()
    {
        return $this->cursor;
    }

    public function getLastPosition()
    {
        return $this->lastPosition;
    }

    public function consume(?string $expected = null): ?string
    {
        if ($this->consumed()) {
            return null;
        }

        $peek = $this->peek();

        if (empty($expected)) {
            $this->cursor++;
            return $peek;
        }

        if (!$this->matches($expected)) {
            throw new ScannerException(
                sprintf(
                    "Expected '%s'. Got '%s'.",
                    $expected,
                    $peek
                )
            );
        }
        $this->cursor += strlen($expected);
        return $expected;
    }

    public function consumeWhile(string $regex): ?string
    {
        $buffer = '';

        while (preg_match($regex, $this->peek())) {
            $buffer .= $this->consume();
        }

        if (empty($buffer)) {
            return null;
        }

        return $buffer;
    }

    public function consumeUnless(string $regex): ?string
    {
        $buffer = '';

        while (!preg_match($regex, $this->peek())) {
            $buffer .= $this->consume();
        }

        if (empty($buffer)) {
            return null;
        }

        return $buffer;
    }

    public function consumeWhileCallback(callable $predicate): ?string
    {
        $buffer = '';

        while (call_user_func($predicate, $this->peek())) {
            $buffer .= $this->consume();
        }

        if (empty($buffer)) {
            return null;
        }

        return $buffer;
    }

    public function consumeUnlessCallback(callable $predicate): ?string
    {
        $buffer = '';

        while (!call_user_func($predicate, $this->peek())) {
            $buffer .= $this->consume();
        }

        if (empty($buffer)) {
            return null;
        }

        return $buffer;
    }
}
