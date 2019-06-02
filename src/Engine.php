<?php

namespace MyTemplate;

class Engine
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * Engine constructor.
     * @param string $cacheDir
     */
    public function __construct(string $cacheDir = './cache')
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param $filePath
     * @param array $params
     * @throws EofException
     * @throws SyntaxError
     */
    public function render($filePath, $params = [])
    {
        $cachePath = $this->getCacheFilePath($filePath);
        if (file_exists($cachePath)) {
            $this->renderFromCache($cachePath, $params);
            return;
        }
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
     * @param string $filePath
     * @param string|null $cachePath
     * @throws EofException
     * @throws SyntaxError
     */
    public function compile(string $filePath, ?string $cachePath = null)
    {
        if ($cachePath === null) {
            $cachePath = $this->getCacheFilePath();
        }
        $src = file_get_contents($filePath);
        $code = $this->generateCode($src);
        file_put_contents($cachePath, $code);
    }

    /**
     * @param string $cacheFilePath
     * @param array $params
     */
    private function renderFromCache(string $cacheFilePath, array $params)
    {
        $_code = file_get_contents($cacheFilePath);
        extract($params);
        eval($_code);
    }

    /**
     * @param $filePath
     * @return string
     */
    private function getCacheFilePath($filePath): string
    {
        return $this->cacheDir . '/' . basename($filePath);
    }

    /**
     * @param $src
     * @return string
     * @throws EofException
     * @throws SyntaxError
     */
    private function generateCode(string $src): string
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