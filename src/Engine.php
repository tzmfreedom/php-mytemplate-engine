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
     * @return false|string|void
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
        $code = $this->compile($filePath);
        return $this->evaluate($code, $params);
    }

    /**
     * @param $src
     * @param array $params
     * @return false|string
     * @throws EofException
     * @throws SyntaxError
     */
    public function renderString($src, $params = [])
    {
        $_code = $this->generateCode($src);
        return $this->evaluate($_code, $params);
    }

    /**
     * @param string $filePath
     * @param string|null $cachePath
     * @return string
     * @throws EofException
     * @throws SyntaxError
     */
    public function compile(string $filePath, ?string $cachePath = null)
    {
        if ($cachePath === null) {
            $cachePath = $this->getCacheFilePath($filePath);
        }
        $src = file_get_contents($filePath);
        $code = $this->generateCode($src);
        file_put_contents($cachePath, $code);
        return $code;
    }

    /**
     * @param string $_code
     * @param array $params
     * @return false|string
     */
    private function evaluate(string $_code, array $params)
    {
        extract($params);
        $context = $this->getContext();
        ob_start();
        eval($_code);
        return ob_get_clean();
    }

    /**
     * @param string $cacheFilePath
     * @param array $params
     * @return false|string
     */
    private function renderFromCache(string $cacheFilePath, array $params)
    {
        $_code = file_get_contents($cacheFilePath);
        return $this->evaluate($_code, $params);
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

    private function getContext(array $nodes): array
    {
        $resolver = new IncludeResolver();
        return $resolver->resolve($nodes);
    }
}