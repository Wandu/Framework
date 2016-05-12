<?php
namespace Wandu\Compiler;

use Closure;
use Wandu\Compiler\Exception\UnknownTokenException;
use Wandu\Compiler\Exception\UnknownTokenFromLexException;

/**
 * Use LALR Parsing Table.
 */
class SyntaxAnalyzer
{
    /** @var \Wandu\Compiler\LexicalAnalyzer */
    protected $lexer;

    protected $tokens = [];

    protected $syntaxes;

    /**
     * @param \Wandu\Compiler\LexicalAnalyzer $lexer
     */
    public function __construct(LexicalAnalyzer $lexer)
    {
        $this->lexer = $lexer;
    }

    public function setTokens(array $tokens) // Nonterminal
    {
        $this->tokens = array_unique_union($this->tokens, $tokens);
    }

    /**
     * @param $nonTermName
     * @param array $tokens
     * @param \Closure $handler
     * @return self
     */
    public function addSyntax($nonTermName, array $tokens, Closure $handler)
    {
        $this->syntaxes[] = [$nonTermName, $tokens, ];//$handler];
        return $this;
    }

    public function analyze($context)
    {
        // get lex tokens
        $predefinedTokens = array_flip($this->tokens);
        $lexTokens = array_map(function ($lexToken) use ($predefinedTokens) {
            if ($lexToken instanceof Token) {
                return $lexToken;
            }
            if (isset($predefinedTokens[$lexToken])) {
                return new Token($lexToken);
            }
            throw new UnknownTokenFromLexException($lexToken);
        }, $this->lexer->analyze($context));

        // get FIRST
//        print_r($this->syntaxes);

        // get FOLLOW

        // get LOOKAHEAD

        // with null

        //

        $parsingTables = []; // [NO_STATES][NO_SYMBOLS + 1]
//        print_r($lexTokens);
        return '';
    }
}
