<?php
namespace Wandu\Tempy;

use Wandu\Compiler\LexicalAnalyzer;
use Wandu\Tempy\Exception\SyntaxException;

class Parser
{
    /** @var bool */
    protected $isOpenBracket = false;

    /** @var bool */
    protected $isAllowedCode = false;

    /** @var string */
    protected $textBuffer = '';

    /** @var string */
    protected $codeStatus;

    /** @var string */
    protected $codeBuffer = '';

    /** @var string */
    protected $result = '';

    public function __construct()
    {
        $this->lexer = new LexicalAnalyzer([
            '\{\{' => function () {
                if ($this->isOpenBracket) {
                    throw new SyntaxException();
                }
                $this->result .= $this->textBuffer;
                $this->textBuffer = '';
                $this->isOpenBracket = true;
            },
            '\}\}' => function () {
                if (!$this->isOpenBracket) {
                    throw new SyntaxException();
                }
                $this->result .= "<?php ";
                switch ($this->codeStatus[0]) {
                    case 'echo' :
                        $this->result .= "echo {$this->codeStatus[1]}";
                        break;
                    case 'echo-default' :
                        $variable = $this->codeStatus[1];
                        $default = $this->codeBuffer;
                        $this->result .= "echo isset({$variable}) ? {$variable} :{$default}";
                        break;
                    case 'if' :
                        $default = $this->codeBuffer;
                        $this->result .= "if{$default}:";
                        break;
                    case 'elseif' :
                        $default = $this->codeBuffer;
                        $this->result .= "elseif{$default}:";
                        break;
                    case 'else' :
                        $this->result .= "else :";
                        break;
                    case 'endif' :
                        $this->result .= "endif";
                        break;
                }
                $this->result .= " ?>";

                $this->codeBuffer = '';

                $this->isAllowedCode = false;
                $this->isOpenBracket = false;
            },
            '\$[a-zA-Z_][a-zA-Z0-9_]*' => function ($word) {
                if (!$this->isOpenBracket) {
                    $this->textBuffer .= $word;
                } elseif ($this->isAllowedCode) {
                    $this->codeBuffer .= $word;
                } else {
                    $this->codeStatus = ['echo', $word];
                }
            },
            '[a-zA-Z_][a-zA-Z0-9_]*' => function ($word) {
                if (!$this->isOpenBracket) {
                    $this->textBuffer .= $word;
                } elseif ($this->isAllowedCode) {
                    $this->codeBuffer .= $word;
                } else {
                    $this->codeStatus = [$word];
                    $this->isAllowedCode = true;
                }
            },
            '\?\?' => function ($word) {
                if (!$this->isOpenBracket) {
                    $this->textBuffer .= $word;
                } else {
                    if ($this->codeStatus[0] === 'echo') {
                        $this->codeStatus[0] = 'echo-default';
                    } else {
                        throw new SyntaxException();
                    }
                    $this->isAllowedCode = true;
                }
            },
            '[\s\n]' => function ($word) {
                if (!$this->isOpenBracket) {
                    $this->textBuffer .= $word;
                } elseif ($this->isAllowedCode) {
                    $this->codeBuffer .= $word;
                }
            },
            '.' => function ($word) {
                if (!$this->isOpenBracket) {
                    $this->textBuffer .= $word;
                } elseif ($this->isAllowedCode) {
                    $this->codeBuffer .= $word;
                } else {
                    throw new SyntaxException($word);
                }
            },
        ]);
    }

    public function parse($code)
    {
        // initialize
        $this->isOpenBracket = false;
        $this->isAllowedCode = false;

        $this->textBuffer = '';
        $this->codeBuffer = '';

        $this->codeStatus = null;

        $this->result = '';

        $this->lexer->analyze($code);
        if ($this->textBuffer) {
            $this->result .= $this->textBuffer;
        }

        return $this->result;
    }
}
