<?php
namespace Wandu\Tempy;

use Wandu\Compiler\LexicalAnalyzer;
use Wandu\Tempy\Exception\SyntaxException;

class Parser
{
    /** @var \Wandu\Compiler\LexicalAnalyzer */
    protected $lexer;

    /** @var string */
    protected $status = "unknown";

    /** @var array */
    protected $arguments = [];

    protected $buffer = '';

    public function __construct()
    {
        $this->lexer = new LexicalAnalyzer([
            '\$[a-zA-Z_][a-zA-Z0-9_]*' => function ($word) {
                $this->buffer .= $word;
                $this->status = 'echo';
            },
            '\?\?' => function () {
                if ($this->buffer) {
                    $this->arguments[] = $this->buffer;
                    $this->buffer = '';
                }
                $this->arguments[] = '??';
            },
            '[\s\n]' => function () {
                if ($this->buffer) {
                    $this->arguments[] = $this->buffer;
                    $this->buffer = '';
                }
            },
            '.' => function ($word) {
                $this->buffer .= $word;
            },
        ]);
    }

    public function parse($code)
    {
        $splitter = new SyntaxSplitter(function ($syntax) {
            $this->status = 'unknown';
            $this->arguments = [];
            $this->lexer->analyze($syntax);
            ;
            switch ($this->status) {
                case 'echo':
                    return "<?php echo " . implode(' ', $this->parseIfsetor($this->arguments)) . " ?>";
            }
        });
        return $splitter->analyze($code);
    }

    protected function parseIfsetor(array $arguments)
    {
        $where = array_search('??', $arguments);
        if ($where === false) {
            return $arguments;
        }
        $variable = $arguments[$where - 1];
        $default = $arguments[$where + 1];
        $replacement = ["isset({$variable})", '?', $variable, ':', $default];
        array_splice($arguments, $where - 1, 3, $replacement);
        return $arguments;
    }
}
