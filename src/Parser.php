<?php
namespace Wandu\Tempy;

use Wandu\Compiler\LexicalAnalyzer;
use Wandu\Tempy\Exception\SyntaxException;

class Parser
{
    /** @var bool */
    protected $isOpenBracket = false;

    /** @var string */
    protected $textBuffer = '';

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
                $this->isOpenBracket = false;
            },
            '\$[a-zA-Z_][a-zA-Z0-9_]*' => function ($word) {
                if (!$this->isOpenBracket) {
                    $this->textBuffer .= $word;
                } else {
                    $this->result .= "<?php echo {$word} ?>";
                }
            },
            '\n' => function ($word) {
                if (!$this->isOpenBracket) {
                    $this->textBuffer .= $word;
                }
            },
            '.' => function ($word) {
                if (!$this->isOpenBracket) {
                    $this->textBuffer .= $word;
                }
            },
        ]);
    }

    public function parse($code)
    {
        // initialize
        $this->isOpenBracket = false;
        $this->textBuffer = '';
        $this->result = '';

        $this->lexer->analyze($code);
        if ($this->textBuffer) {
            $this->result .= $this->textBuffer;
        }

        return $this->result;
    }
}
