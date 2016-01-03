<?php
namespace Wandu\Compiler;

use Closure;
use Wandu\Compiler\Exception\UnknownTokenException;

class LexicalAnalyzer
{
    /** @var \Closure[] */
    protected $tokenMap = [];

    /** @var string */
    protected $regExp;

    /**
     * @param array $tokens
     */
    public function __construct(array $tokens)
    {
        $tokensToJoin = [];
        $idx = 0;
        foreach ($tokens as $token => $handler) {
            $tokensToJoin[] = $token;
            if ($handler instanceof Closure) {
                $this->tokenMap[$idx] = $handler;
            } else {
                $this->tokenMap[$idx] = null;
            }
            $idx++;
        }
        $this->regExp = '%^(?:(' . implode(')|(', $tokensToJoin). '))%';
    }


    public function analyze($context)
    {
        $tail = $context;
        $resultToReturn = [];
        while (preg_match($this->regExp, $tail, $matches)) {
            if (!isset($matches) || count($matches) === 0) {
                break;
            }
            $word = array_shift($matches);
            $idx = count($matches) - 1;
            if ($idx < 0) {
                throw new UnknownTokenException($tail);
            }
            if (isset($this->tokenMap[$idx])) {
                $resultToReturn[] = call_user_func($this->tokenMap[$idx], $word);
            }
            unset($matches);
            $tail = substr($tail, strlen($word));
        }
        if ($tail) {
            throw new UnknownTokenException($tail);
        }
        return $resultToReturn;
    }
}
