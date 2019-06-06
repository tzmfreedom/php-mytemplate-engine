<?php

namespace MyTemplate;

require_once dirname(__FILE__) . '/Token.php';
require_once dirname(__FILE__) . '/Node.php';

/**
 * Class Parser
 * @package MyTemplate
 */
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

    /**
     * Parser constructor.
     * @param array $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->index = 0;
    }

    /**
     * @return array
     * @throws EofException
     * @throws SyntaxError
     */
    public function parse()
    {
        $nodes = [];
        $token = $this->current();
        while (true) {
            try {
                $node = $this->parseNode($token);
            } catch (EofException $e) {
                break;
            }
            if ($node !== null) {
                $nodes[] = $node;
            } else {
                break;
            }
            try {
                $token = $this->next();
            } catch (EofException $e) {
                break;
            }
        }
        return $nodes;
    }

    /**
     * @param $token
     * @return ForNode|Identifier|IfNode|IncludeNode|PlainString|null
     * @throws SyntaxError
     * @throws EofException
     */
    public function parseNode($token)
    {
        switch ($token->getType()) {
            case Token::TYPE_STRING:
                return new PlainString($token->getValue());
            case Token::TYPE_IF:
                return $this->parseIf();
            case Token::TYPE_FOR:
                return $this->parseFor();
            case Token::TYPE_IDENT:
                return new Identifier($token->getValue());
            case Token::TYPE_INCLUDE:
                return $this->parseInclude();
            default:
                return null;
        }
    }

    /**
     * @return IfNode
     * @throws EofException
     * @throws SyntaxError
     */
    private function parseIf()
    {
        $token = $this->current();
        if (!$token->isType(Token::TYPE_IF)) {
            throw new \Exception('logic exception');
        }
        $token = $this->next();
        if (!$token->isType(Token::TYPE_IDENT)) {
            throw new SyntaxError('token `ident` expected');
        }
        $ident = $token->getValue();
        $this->next();
        $ifNodes = $this->parse();
        $token = $this->current();
        if ($token->isType(Token::TYPE_ELSE)) {
            $this->next();
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

    /**
     * @return ForNode
     * @throws EofException
     * @throws SyntaxError
     */
    private function parseFor()
    {
        $token = $this->current();
        if (!$token->isType(Token::TYPE_FOR)) {
            throw new Exception('logic exception');
        }
        $variable = $this->next();
        if (!$variable->isType(Token::TYPE_IDENT)) {
            throw new SyntaxError('token `ident` expected');
        }
        $token = $this->next();
        if (!$token->isAsciiType(':')) {
            throw new SyntaxError('token `ident` expected');
        }
        $expression = $this->next();
        if (!$expression->isType(Token::TYPE_IDENT)) {
            throw new SyntaxError('token `ident` expected');
        }
        $this->next();
        $nodes = $this->parse();
        $token = $this->current();
        if (!$token->isType(Token::TYPE_END)) {
            throw new SyntaxError('token `end` expected');
        }
        return new ForNode($expression->getValue(), $variable->getValue(), $nodes);
    }

    /**
     * @return IncludeNode
     * @throws EofException
     * @throws SyntaxError
     */
    private function parseInclude()
    {
        $token = $this->current();
        if (!$token->isType(Token::TYPE_INCLUDE)) {
            throw new \Exception('logic exception');
        }
        $token = $this->next();
        if (!$token->isType(Token::TYPE_STRING_LITERAL)) {
            var_dump($token);
            throw new SyntaxError('token `STRING_LITERAL` expected');
        }
        return new IncludeNode($token->getValue());
    }

    /**
     * @return Token
     * @throws EofException
     */
    private function current(): Token
    {
        if ($this->index >= count($this->tokens)) {
            throw new EofException();
        }
        return $this->tokens[$this->index];
    }

    /**
     * @return Token
     * @throws EofException
     */
    private function next(): Token
    {
        $this->index++;
        return $this->current();
    }
}