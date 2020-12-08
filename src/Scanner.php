<?php
declare(strict_types=1);

namespace W5n;

class Scanner
{
    private $text;
    private $cursor;
    private $lastPosition;
    private $savedPositions = [];

    public function __construct($text)
    {
        $this->reset($text);
    }

    public function savePosition()
    {
        $this->savedPositions[] = $this->cursor;
    }

    public function clearSavedPositions()
    {
        $this->savedPositions = [];
    }

    public function popSavedPosition(): int
    {
        if (empty($this->savedPositions)) {
            throw new ScannerException('There is not any saved position available.');
        }

        return array_pop($this->savedPositions);
    }

    public function loadPosition(): void
    {
        $this->cursor = $this->popSavedPosition();
    }

    public function reset($text)
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

    public function matchesAny(...$testStrings)
    {
        foreach ($testStrings as $testString) {
            if ($this->matches($testString)) {
                return true;
            }
        }

        return false;
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

    public function consume(?string $expected = null): string
    {
        if ($this->consumed()) {
            throw new ScannerException('Unexpected end of file.');
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

    public function consumeAny(...$expected): string
    {
        foreach ($expected as $exp) {
            if ($this->matches($exp)) {
                return $this->consume($exp);
            }
        }

        throw new ScannerException(
            sprintf(
                "Expected any of [%s]. Got '%s'.",
                implode(', ', $expected),
                $this->peek()
            )
        );
    }

    public function consumeWhile(string $regex): ?string
    {
        $buffer = '';

        while (!$this->consumed() && preg_match($regex, $this->peek())) {
            $buffer .= $this->consume();
        }

        if (strlen($buffer) == 0) {
            return null;
        }

        return $buffer;
    }

    public function consumeUnless(string $regex): ?string
    {
        $buffer = '';

        while (!$this->consumed() && !preg_match($regex, $this->peek())) {
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

        while (!$this->consumed() && call_user_func($predicate, $this->peek())) {
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

        while (!$this->consumed() && !call_user_func($predicate, $this->peek())) {
            $buffer .= $this->consume();
        }

        if (empty($buffer)) {
            return null;
        }

        return $buffer;
    }
}
