<?php

class Engine
{
    public function __construct()
    {
    }

    public function render($filePath, $params = [])
    {
        $src = file_get_contents($filePath);
        $this->renderString($src, $params);
    }

    public function renderString($src, $params = [])
    {
        $_code = $this->generateCode($src);
        extract($params);
        eval($_code);
    }

    public function generateCode($src)
    {
        $lexer = new Lexer($src);
        $tokens = $lexer->parse();
        $parser = new Parser($tokens);
        $nodes = $parser->parse();

        $generator = new CodeGenerator($nodes);
        $lines = $generator->generate();
        return implode(PHP_EOL, $lines);
    }
}