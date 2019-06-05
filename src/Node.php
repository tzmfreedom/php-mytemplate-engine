<?php

namespace MyTemplate;

interface Node {
    public function getType(): string;
}

class Identifier implements Node
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getType(): string
    {
        return 'IDENTIFIER';
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

class PlainString implements Node
{
    /**
     * @var string
     */
    private $value;

    /**
     * PlainString constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'STRING';
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}

class IfNode implements Node
{
    /**
     * @var string
     */
    private $condition;

    /**
     * @var array
     */
    private $ifNodes;

    /**
     * @var
     */
    private $elseNodes;

    /**
     * IfNode constructor.
     * @param string $condition
     * @param array $ifNodes
     * @param array $elseNodes
     */
    public function __construct(string $condition, array $ifNodes, array $elseNodes)
    {
        $this->condition = $condition;
        $this->ifNodes = $ifNodes;
        $this->elseNodes = $elseNodes;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'IF';
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return array
     */
    public function getIfNodes()
    {
        return $this->ifNodes;
    }

    /**
     * @return array
     */
    public function getElseNodes()
    {
        return $this->elseNodes;
    }
}

class ForNode implements Node
{
    /**
     * @var string
     */
    private $expression;

    /**
     * @var string
     */
    private $variable;

    /**
     * @var array
     */
    private $nodes;

    /**
     * ForNode constructor.
     * @param string $expression
     * @param string $variable
     * @param array $nodes
     */
    public function __construct(string $expression, string $variable, array $nodes)
    {
        $this->expression = $expression;
        $this->variable = $variable;
        $this->nodes = $nodes;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'FOR';
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getVariable(): string
    {
        return $this->variable;
    }

    /**
     * @return array
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }
}

class IncludeNode implements Node
{
    /**
     * @var N
     */
    private $expression;

    /**
     * ForNode constructor.
     * @param Node $expression
     */
    public function __construct(Node $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'INCLUDE';
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }
}