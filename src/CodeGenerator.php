<?php

namespace MyTemplate;

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

    /**
     * @var array
     */
    private $context;

    /**
     * CodeGenerator constructor.
     * @param array $nodes
     * @param array $context
     */
    public function __construct(array $nodes, array $context)
    {
        $this->index = 0;
        $this->nodes = $nodes;
        $this->context = $context;
    }

    /**
     * @return array
     * @throws SyntaxError
     */
    public function generate()
    {
        return $this->generateLines($this->nodes);
    }

    /**
     * @param array $nodes
     * @return array
     * @throws SyntaxError
     */
    private function generateLines(array $nodes)
    {
        $lines = [];
        foreach ($nodes as $node) {
            $lines[] = $this->evaluateNode($node);
        }
        return $lines;
    }

    private function evaluateNode($node)
    {
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
            case 'IDENTIFIER':
                $line = $this->generateIdentifier($node);
                break;
            case 'INCLUDE':
                $line = $this->generateInclude($node);
                break;
            default:
                $line = null;
        }
        if ($line === null) {
            throw new SyntaxError(sprintf('unexpected token: %s', $node->getType()));
        }
        return $line;
    }

    /**
     * @param PlainString $node
     * @return string
     */
    private function generateString(PlainString $node): string
    {
        $format = <<<FORMAT
echo <<<EOS
%s
EOS;

FORMAT;
        return sprintf($format, trim($node->getValue()));
    }

    /**
     * @param IfNode $ifNode
     * @return string
     * @throws SyntaxError
     */
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
        $elseNodes = $this->generateLines($ifNode->getElseNodes());
        return sprintf($format, $condition, implode("", $ifNodes), implode("", $elseNodes));
    }

    /**
     * @param ForNode $node
     * @return string
     */
    private function generateFor(ForNode $node): string
    {
        $format = <<<FORMAT
foreach ($%s as $%s) {
%s
}
FORMAT;

        $expression = $node->getExpression();
        $loopVar = $node->getVariable();
        $nodes = $this->generateLines($node->getNodes());
        return sprintf($format, $expression, $loopVar, implode('', $nodes));
    }

    /**
     * @param Identifier $node
     * @return string
     */
    private function generateIdentifier(Identifier $node): string
    {
        $values = explode('.', $node->getValue());
        if (count($values) === 1) {
            $value = $values[0];
        } else {
            $tmp = [$values[0]];
            for ($i = 1; $i < count($values); $i++) {
                $first = mb_strtoupper(mb_substr($values[$i], 0, 1));
                $remain = mb_substr($values[$i], 1);
                $tmp[] = 'get' . $first . $remain . '()';
            }
            $value = implode('->', $tmp);
        }
        return sprintf('echo $%s;', $value);
    }


    /**
     * @param IncludeNode $node
     * @return string
     * @throws SyntaxError
     */
    private function generateInclude(IncludeNode $node): string
    {
        $format = <<<FORMAT
echo <<<EOS
%s
EOS;

FORMAT;

        return sprintf($format, $this->context[$node->getFile()]);
    }
}