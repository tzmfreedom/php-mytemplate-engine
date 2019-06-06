<?php

namespace MyTemplate;

require_once dirname(__FILE__) . '/Token.php';
require_once dirname(__FILE__) . '/EofException.php';
require_once dirname(__FILE__) . '/SyntaxError.php';

class Lexer
{
    const ASCII_TOKENS = [
        '+',
        '-',
        '*',
        '/',
        '%',
        ':'
    ];
    /**
     * @var int
     */
    public $index;

    /**
     * @var string
     */
    public $src;

    /**
     * Lexer constructor.
     * @param string $src
     */
    public function __construct(string $src)
    {
        $this->index = 0;
        $this->src = $src;
    }

    /**
     * @return array
     * @throws EofException
     * @throws SyntaxError
     */
    public function parse(): array
    {
        $expressions = [];
        while (true) {
            try {
                $char = $this->current();
            } catch (EofException $e) {
                break;
            }
            switch ($char) {
                case '{':
                    $tokens = $this->parseExpression();
                    foreach ($tokens as $token) {
                        $expressions[] = $token;
                    }
                    $this->index++;
                    break;
                default:
                    $expressions[] = $this->parseHtml();
                    break;
            }
        }
        return $expressions;
    }

    /**
     * @return Token
     */
    private function parseHtml()
    {
        $value = '';
        while (true) {
            try {
                $char = $this->current();
            } catch (EofException $e) {
                return new Token(Token::TYPE_STRING, $value);
            }
            if (in_array($char, ['{', '}'])) {
                return new Token(Token::TYPE_STRING, $value);
            }
            $value .= $char;
            $this->index++;
        }
    }

    /**
     * @return array
     * @throws EofException
     * @throws SyntaxError
     */
    private function parseExpression(): array
    {
        $char = $this->current();
        if ($char !== '{') {
            throw new SyntaxError();
        }
        $char = $this->next();
        if ($char === '%') {
            $token = $this->parseIdentifier();
            if ($this->current() !== '%') {
                throw new SyntaxError();
            }
            $char = $this->next();
            if ($char !== '}') {
                throw new SyntaxError();
            }
            return [$token];
        }
        if ($char !== '{') {
            throw new SyntaxError();
        }
        $this->index++;
        $this->skipSpaces();
        $tokens = [];
        while (true) {
            $token = $this->parseToken();
            $this->skipSpaces();
            $tokens[] = $token;
            $char = $this->current();
            if ($char === '}') {
                $this->index++;
                $char = $this->current();
                if ($char !== '}') {
                    throw new SyntaxError();
                }
                return $tokens;
            }
        }
    }

    private function skipSpaces()
    {
        while (true) {
            $char = mb_substr($this->src, $this->index, 1);
            if ($char !== ' ') {
                break;
            }
            $this->index++;
        }
    }

    /**
     * @return Token
     */
    private function parseToken()
    {
        $value = '';
        while (true) {
            $char = mb_substr($this->src, $this->index, 1);
            if (in_array($char, self::ASCII_TOKENS)) {
                if ($value === '') {
                    $type = ord($char);
                    $this->index++;
                    return new Token($type, $type);
                }
                return $this->reserveToken($value);
            } else if (in_array($char, [' ', '}'])) {
                return $this->reserveToken($value);
            }
            $value .= $char;
            $this->index++;
        }
    }

    private function parseIdentifier()
    {
        $this->next();
        $this->skipSpaces();
        $char = $this->current();
        $value = '';
        while (true) {
            if (in_array($char, [' ', '%', '}'])) {
                $this->skipSpaces();
                return new Token(Token::TYPE_IDENT, $value);
            } else if (in_array($char, self::ASCII_TOKENS)) {
                throw new SyntaxError();
            }
            $value .= $char;
            $char = $this->next();
        }
    }

    private function reserveToken(string $value): Token
    {
        switch (mb_strtoupper($value)) {
            case 'IF':
                return new Token(Token::TYPE_IF, $value);
            case 'ELSE':
                return new Token(Token::TYPE_ELSE, $value);
            case 'FOR':
                return new Token(Token::TYPE_FOR, $value);
            case 'END':
                return new Token(Token::TYPE_END, $value);
            case 'INCLUDE':
                return new Token(Token::TYPE_INCLUDE, $value);
            default:
                if (preg_match('/\A\'([^\']*)\'\z/', $value, $match)) {
                    return new Token(Token::TYPE_STRING_LITERAL, $match[1]);
                }
                return new Token(Token::TYPE_IDENT, $value);
        }
    }

    /**
     * @return EofException|string
     * @throws EofException
     */
    private function current()
    {
        if ($this->index >= mb_strlen($this->src)) {
            throw new EofException();
        }
        return mb_substr($this->src, $this->index, 1);
    }

    private function next()
    {
        $this->index++;
        return $this->current();
    }
}