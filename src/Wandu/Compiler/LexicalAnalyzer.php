<?php
namespace Wandu\Compiler;

use Wandu\Compiler\Exception\UnknownTokenException;

class LexicalAnalyzer
{
    /** @var callable[] */
    protected $tokens;

    /**
     * @param array $tokens
     */
    public function __construct(array $tokens = [])
    {
        $this->tokens = $this->tokens();
        foreach ($tokens as $token => $handler) {
            $this->addToken($token, $handler);
        }
    }

    /**
     * @override
     * @return array
     */
    public function tokens()
    {
        return [];
    }

    /**
     * @param string $token
     * @param callable|null $handler
     * @return self
     */
    public function addToken($token, callable $handler = null)
    {
        $this->tokens[$token] = $handler;
        return $this;
    }

    /**
     * @param string $context
     * @return array
     */
    public function analyze($context)
    {
        $tokenMap = array_values($this->tokens);
        $regExp = '~(' . implode(')|(', array_keys($this->tokens)). ')~';
        $tail = $context;
        $resultToReturn = [];
        while (preg_match($regExp, $tail, $matches)) {
            if (!isset($matches) || count($matches) === 0) {
                break;
            }
            $word = array_shift($matches);
            $idx = count($matches) - 1;
            if ($idx < 0) {
                throw new UnknownTokenException($tail);
            }
            if (isset($tokenMap[$idx])) {
                $resultToReturn[] = call_user_func($tokenMap[$idx], $word);
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
