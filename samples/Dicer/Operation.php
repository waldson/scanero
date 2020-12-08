<?php
namespace W5n\Samples\Dicer;

class Operation implements Token
{
    private $operator;
    private $left;
    private $right;

    public function __construct(Operator $operator, Token $left, Token $right)
    {
        $this->operator = $operator;
        $this->left     = $left;
        $this->right    = $right;
    }

    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function getLeft(): Token
    {
        return $this->left;
    }

    public function getRight(): Token
    {
        return $this->right;
    }

    public function getValue(?Context $context = null): int
    {
        $operator = $this->getOperator()->getSymbol();

        switch ($operator) {
            case '+':
                return $this->getLeft()->getValue($context) + $this->getRight()->getValue($context);
            case '-':
                return $this->getLeft()->getValue($context) - $this->getRight()->getValue($context);
            case '*':
                return $this->getLeft()->getValue($context) * $this->getRight()->getValue($context);
            case '/':
                return $this->getLeft()->getValue($context) / $this->getRight()->getValue($context);
            case '%':
                return $this->getLeft()->getValue($context) % $this->getRight()->getValue($context);
            case '^':
                return pow(
                    $this->getLeft()->getValue($context),
                    $this->getRight()->getValue($context)
                );
        }

        throw new \Exception(
            sprintf('Invalid operator "%s".', $operator)
        );
    }
}
