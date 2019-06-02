<?php

namespace MyTemplate;

class Engine
{
    /**
     * Engine constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $filePath
     * @param array $params
     * @throws EofException
     * @throws SyntaxError
     */
    public function render($filePath, $params = [])
    {
        $src = file_get_contents($filePath);
        $this->renderString($src, $params);
    }

    /**
     * @param $src
     * @param array $params
     * @throws EofException
     * @throws SyntaxError
     */
    public function renderString($src, $params = [])
    {
        $_code = $this->generateCode($src);
        extract($params);
        eval($_code);
    }

    /**
     * @param $src
     * @return string
     * @throws EofException
     * @throws SyntaxError
     */
    private function generateCode($src)
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