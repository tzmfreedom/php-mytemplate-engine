<?php

namespace MyTemplate;

class PlainString
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

    public function getType()
    {
        return 'STRING';
    }

    public function getValue()
    {
        return $this->value;
    }
}

class IfNode
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

    public function getType()
    {
        return 'IF';
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function getIfNodes()
    {
        return $this->ifNodes;
    }

    public function getElseNodes()
    {
        return $this->elseNodes;
    }
}

class ForNode
{
    /**
     * @var string
     */
    private $expression;

    /**
     * @var array
     */
    private $nodes;

    /**
     * ForNode constructor.
     * @param string $expression
     * @param array $nodes
     */
    public function __construct(string $expression, array $nodes)
    {
        $this->expression = $expression;
        $this->nodes = $nodes;
    }

    public function getType()
    {
        return 'FOR';
    }
}
