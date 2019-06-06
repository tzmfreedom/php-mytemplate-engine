<?php

namespace MyTemplate;

/**
 * Class Token
 * @package MyTemplate
 */
class Token {
    const TYPE_IF = 256;
    const TYPE_ELSE = self::TYPE_IF+1;
    const TYPE_END = self::TYPE_ELSE+1;
    const TYPE_FOR = self::TYPE_END+1;
    const TYPE_IDENT = self::TYPE_FOR+1;
    const TYPE_STRING = self::TYPE_IDENT+1;
    const TYPE_INCLUDE = self::TYPE_STRING+1;
    const TYPE_STRING_LITERAL = self::TYPE_INCLUDE+1;

    const TYPE_MAPPER = [
        self::TYPE_IF => 'IF',
        self::TYPE_ELSE=> 'ELSE',
        self::TYPE_END => 'END',
        self::TYPE_FOR => 'FOR',
        self::TYPE_IDENT => 'IDENT',
        self::TYPE_STRING => 'STRING',
        self::TYPE_INCLUDE => 'INCLUDE',
        self::TYPE_STRING_LITERAL => 'STRING_LITERAL',
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @var string
     */
    private $debugString;

    /**
     * Token constructor.
     * @param int $type
     * @param string|null $value
     */
    public function __construct(int $type, ?string $value = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->debugString = self::TYPE_MAPPER[$type];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param int $type
     * @return bool
     */
    public function isType(int $type): bool
    {
        return $this->type === $type;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isAsciiType(string $type): bool
    {
        return $this->isType(ord($type));
    }
}

