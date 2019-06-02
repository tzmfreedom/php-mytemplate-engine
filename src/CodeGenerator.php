<?php

require_once dirname(__FILE__) . '/Node.php';

class CodeGenerator
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var array
     */
    private $nodes;

    public function __construct(array $nodes)
    {
        $this->index = 0;
        $this->nodes = $nodes;
    }

    public function generate()
    {
        return $this->generateLines($this->nodes);
    }

    private function generateLines(array $nodes)
    {
        $lines = [];
        foreach ($nodes as $node) {
            switch ($node->getType()) {
                case 'IF':
                    $line = $this->generateIf($node);
                    break;
                case 'FOR':
                    $line = $this->generateFor($node);
                    break;
                case 'STRING':
                    $line = $this->generateString($node);
                    break;
                default:
                    $line = null;
            }
            if ($line === null) {
                throw new SyntaxError(sprintf('unexpected token: %s', $node->getType()));
            }
            $lines[] = $line;
        }
        return $lines;
    }

    private function generateString(PlainString $node): string
    {
        $format = <<<FORMAT
echo <<<EOS
%s
EOS;

FORMAT;
        return sprintf($format, $node->getValue());
    }

    private function generateIf(IfNode $ifNode): string
    {
        $format = <<<FORMAT
if ($%s) {
%s
} else {
%s
}
FORMAT;
        $condition = $ifNode->getCondition();
        $ifNodes = $this->generateLines($ifNode->getIfNodes());
        $elseNodes = $this->generateLines($ifNode->getIfNodes());
        return sprintf($format, $condition, implode("", $ifNodes), implode("", $elseNodes));
    }

    private function generateFor(ForNode $node): string
    {

    }

    private function current()
    {
        return $this->nodes[$this->index];
    }
}