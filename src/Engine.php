<?php

namespace MyTemplate;

require_once dirname(__FILE__) . '/CodeGenerator.php';
require_once dirname(__FILE__) . '/IncludeResolver.php';
require_once dirname(__FILE__) . '/Lexer.php';
require_once dirname(__FILE__) . '/Parser.php';

/**
 * Class Engine
 * @package MyTemplate
 */
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
    public function __construct(?string $cacheDir = './cache')
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
        if ($cachePath !== null && file_exists($cachePath)) {
            return $this->renderFromCache($cachePath, $params);
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
        if ($this->cacheDir !== null && $cachePath === null) {
            $cachePath = $this->getCacheFilePath($filePath);
        }
        $src = file_get_contents($filePath);
        $code = $this->generateCode($src);
        if ($this->cacheDir !== null) {
            if (!file_exists(dirname($cachePath))) {
                mkdir(dirname($cachePath), 0777, true);
            }
            file_put_contents($cachePath, $code);
        }
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

        $resolver = new IncludeResolver($nodes);
        $files = $resolver->resolve();
        $context = [];
        foreach ($files as $file) {
            $context[$file] = $this->render($file, []);
        }

        $generator = new CodeGenerator($nodes, $context);
        $lines = $generator->generate();
        return implode(PHP_EOL, $lines);
    }
}