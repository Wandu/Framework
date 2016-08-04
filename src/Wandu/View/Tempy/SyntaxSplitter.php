<?php
namespace Wandu\View\Tempy;

use Closure;
use Wandu\Compiler\LexicalAnalyzer;
use Wandu\View\Tempy\Exception\SyntaxException;

class SyntaxSplitter
{
    /** @var bool */
    protected $isOpenBracket = false;

    /** @var string */
    protected $codeBuffer = '';

    /** @var string */
    protected $result = '';

    public function __construct(Closure $syntaxHandler = null)
    {
        if (!isset($syntaxHandler)) {
            $syntaxHandler = function () {};
        }
        $this->lexer = new LexicalAnalyzer([
            '\{\{' => function () {
                if ($this->isOpenBracket) {
                    throw new SyntaxException();
                }
                $this->isOpenBracket = true;
            },
            '\}\}' => function () use ($syntaxHandler) {
                if (!$this->isOpenBracket) {
                    throw new SyntaxException();
                }
                $this->result .= $syntaxHandler($this->codeBuffer);
                $this->codeBuffer = '';
                $this->isOpenBracket = false;
            },
            '.' => function ($word) {
                if (!$this->isOpenBracket) {
                    $this->result .= $word;
                } else {
                    $this->codeBuffer .= $word;
                }
            },
        ]);
    }

    public function analyze($context)
    {
        // initialize
        $this->isOpenBracket = false;

        $this->codeBuffer = '';

        $this->result = '';

        $this->lexer->analyze($context);
        if ($this->isOpenBracket) {
            throw new SyntaxException();
        }

        return $this->result;
    }
}
