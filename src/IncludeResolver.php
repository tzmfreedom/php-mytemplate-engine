<?php

namespace MyTemplate;

require_once dirname(__FILE__) . '/Node.php';

/**
 * Class IncludeResolver
 * @package MyTemplate
 */
class IncludeResolver
{
    /**
     * @var array
     */
    private $nodes;

    /**
     * @var array
     */
    private $files;

    /**
     * CodeGenerator constructor.
     * @param array $nodes
     */
    public function __construct(array $nodes)
    {
        $this->nodes = $nodes;
        $this->files = [];
    }

    /**
     * @return array
     */
    public function resolve()
    {
        $this->resolveLines($this->nodes);
        return $this->files;
    }

    /**
     * @param array $nodes
     * @return array
     */
    private function resolveLines(array $nodes)
    {
        foreach ($nodes as $node) {
            $this->evaluateNode($node);
        }
    }

    /**
     * @param Node $node
     */
    private function evaluateNode(Node $node)
    {
        switch ($node->getType()) {
            case 'IF':
                /** @var IfNode $node */
                foreach ($node->getIfNodes() as $node) {
                    $this->evaluateNode($node);
                }
                break;
            case 'FOR':
                /** @var ForNode $node */
                foreach ($node->getNodes() as $node) {
                    $this->evaluateNode($node);
                }
                break;
            case 'INCLUDE':
                $this->generateInclude($node);
        }
    }

    /**
     * @param IncludeNode $node
     */
    private function generateInclude(IncludeNode $node)
    {
        $this->files[] = $node->getFile();
    }
}