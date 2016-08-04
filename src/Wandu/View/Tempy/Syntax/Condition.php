<?php
namespace Wandu\View\Tempy\Syntax;

use Wandu\View\Tempy\Exception\SyntaxException;
use Wandu\View\Tempy\Syntax;

class Condition extends Syntax
{
    /** @var string */
    protected $syntaxOpen = "if";

    /** @var array */
    protected $syntaxMiddles = ['elseif', 'else'];

    /** @var string */
    protected $syntaxClose = "/if";

    public function open(array $arguments, array $namespaces = [])
    {
        return "<?php if (" . implode(' ', $arguments) . ") : ?>";
    }

    public function close(array $arguments, array $namespace = [])
    {
        return "<?php endif; ?>";
    }

    public function middle($index, array $arguments, array $namespace = [])
    {
        switch ($index) {
            case 0:
                return "<?php elseif (" . implode(' ', $arguments) . ") : ?>";
            case 1:
                return "<?php else : ?>";
        }
        throw new SyntaxException("not allowed \"index\" in Condition Syntax!");
    }
}
