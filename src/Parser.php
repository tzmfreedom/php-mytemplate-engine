<?php

require_once dirname(__FILE__) . '/Token.php';
require_once dirname(__FILE__) . '/Node.php';

class Parser
{
    /**
     * @var array
     */
    private $tokens;

    /**
     * @var int
     */
    private $index;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->index = 0;
    }

    public function parse()
    {
        $nodes = [];
        while (true) {
            try {
                $token = $this->current();
                $node = $this->parseNode($token);
            } catch (EofException $e) {
                break;
            }
            if ($node !== null) {
                $nodes[] = $node;
            } else {
                break;
            }
            $this->index++;
        }
        return $nodes;
    }

    public function parseNode($token)
    {
        switch ($token->getType()) {
            case Token::TYPE_STRING:
                return new PlainString($token->getValue());
            case Token::TYPE_IF:
                return $this->parseIf();
            default:
                return null;
        }
    }

    private function parseIf()
    {
        $token = $this->current();
        if (!$token->isType(Token::TYPE_IF)) {
            throw new Exception('logic exception');
        }
        $this->index++;
        $token = $this->current();
        if (!$token->isType(Token::TYPE_IDENT)) {
            throw new SyntaxError('token `ident` expected');
        }
        $ident = $token->getValue();
        $this->index++;
        $ifNodes = $this->parse();
        $token = $this->current();
        if ($token->isType(Token::TYPE_ELSE)) {
            $this->index++;
            $elseNodes = $this->parse();
            $token = $this->current();
            if ($token->isType(Token::TYPE_END)) {
                return new IfNode($ident, $ifNodes, $elseNodes);
            } else {
                throw new SyntaxError('token `end` expected');
            }
        } else if ($token->isType(Token::TYPE_END)) {
            return new IfNode($ident, $ifNodes, []);
        } else {
            throw new SyntaxError('token `end` expected');
        }
    }

    private function parseFor()
    {

    }

    private function current(): Token
    {
        if ($this->index >= count($this->tokens)) {
            throw new EofException();
        }
        return $this->tokens[$this->index];
    }

    private function next(): Token
    {
        return $this->tokens[$this->index+1];
    }
}