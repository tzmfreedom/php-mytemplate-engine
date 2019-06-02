<?php

namespace MyTemplate;

class Token {
    const TYPE_IF = 1;
    const TYPE_ELSE = 2;
    const TYPE_END = 3;
    const TYPE_FOR = 4;
    const TYPE_IDENT = 5;
    const TYPE_STRING = 6;

    const TYPE_MAPPER = [
        self::TYPE_IF => 'IF',
        self::TYPE_ELSE=> 'ELSE',
        self::TYPE_END => 'END',
        self::TYPE_FOR => 'FOR',
        self::TYPE_IDENT => 'IDENT',
        self::TYPE_STRING => 'STRING',
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
}

