<?php
declare(strict_types=1);

namespace W5n\Samples\Dicer;

class DiceRoll implements Token
{
    private $count    = 1;
    private $faces    = 6;
    private $modifier = 0;
    private $label    = null;
    private $lastRoll = [];

    public function __construct(
        int $count = 1,
        int $faces = 6,
        int $modifier = 0,
        ?string $label = null
    ) {
        if ($faces == 0) {
            throw new \Exception('A dice cannot have 0 faces.');
        }

        $this->count = $count;
        $this->faces = $faces;
        $this->modifier = $modifier;
        $this->label = $label;
    }

    public function roll(RandomGenerator $generator): int
    {
        $sum = 0;

        if ($this->count > 0) {
            $this->lastRoll = [
                'count'    => $this->count,
                'faces'    => $this->faces,
                'label'    => null,
                'modifier' => $this->modifier,
                'results'  => [],
                'total'    => 0
            ];
        }

        if (!empty($this->label)) {
            $this->lastRoll['label'] = $this->getLabel();
        }

        for ($i = 0; $i < $this->count; ++$i) {
            $result = $generator->generate(1, $this->faces);

            $this->lastRoll['results'][] = $result;

            $sum += $result;
        }

        if (!empty($this->modifier)) {
            $sum += $this->modifier;
        }

        $this->lastRoll['total'] = $sum;

        return $sum;
    }

    public function getValue(?Context $context = null): int
    {
        return $this->roll($context->getRandomGenerator());
    }

    public function __toString()
    {
        $result = sprintf(
            '%sd%s',
            $this->count,
            $this->faces
        );

        if (!empty($this->modifier)) {
            if ($this->modifier > 0) {
                $result .= '+' . $this->modifier;
            } elseif ($this->modifier < 0) {
                $result .= '-' . abs($this->modifier);
            }
        }

        if (!empty($this->label)) {
            $result .= '['. $this->label . ']';
        }

        return $result;
    }

    public function getLabel() : ?string
    {
        return $this->label;
    }

    public function getDiceCount() : int
    {
        return $this->count;
    }

    public function getDiceFaces(): int
    {
        return $this->faces;
    }

    public function getModifier(): int
    {
        return $this->modifier;
    }
}
