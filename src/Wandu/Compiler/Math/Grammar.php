<?php
namespace Wandu\Compiler\Math;

use Wandu\Compiler\Exception\UndefinedNontermException;

class Grammar
{
    /** @var \Wandu\Compiler\Math\Set */
    protected $nonterms;

    /** @var array */
    protected $products;

    /** @var string */
    protected $startSymbol;

    public function __construct(array $nonterms, array $products, $startSymbol)
    {
        $this->nonterms = new Set(...$nonterms);
        $this->products = $this->normalizeProducts($products, $nonterms);
        $this->startSymbol = $startSymbol;
    }

    /**
     * @param array $symbols
     * @return \Wandu\Compiler\Math\Set
     */
    public function first(array $symbols = [])
    {
        if (count($symbols) === 0) {
            return new Set();
        }

        // if $x[0] is terminal symbol, return self.
        if (!$this->nonterms->has($symbols[0])) {
            return new Set($symbols[0]);
        }

        if (!array_key_exists($symbols[0], $this->products)) {
            return new Set();
        }

        $setToReturn = new Set;
        foreach ($this->products[$symbols[0]] as $product) {
            if ($product[0] !== $symbols[0]) {
                $setToReturn->union($this->first($product));
            }
        }

        return $setToReturn;
    }

    /**
     * @example.
     *
     * S -> ABe
     * A -> dB | aS | c
     * B -> AS | b
     * then, $items ares..
     * [
     *   ['S', ['A', 'B', 'e']],
     *   ['A', ['d', 'B']],
     *   ['A', ['a', 'S']],
     *   ['A', ['c']],
     *   ['B', ['A', 'S']],
     *   ['B', ['b']],
     * ]
     * @return array
     */
    public function computeFirsts()
    {
        // G = (V_N, V_T, P, S) 를 기반으로 First라는 매서드를 만들어야 할지도..
        $result = [];
        // for each A in V_N do FIRST(A) := []
        foreach ($this->products as $nonterm) {
            $result[$nonterm] = []; // initialize
        }

        return [];
    }

    protected function normalizeProducts(array $products, array $nonterms)
    {
        $normalizedProducts = [];
        foreach ($products as $product) {
            if (!in_array($product[0], $nonterms, true)) {
                throw new UndefinedNontermException($product[0]);
            }
            if (!array_key_exists($product[0], $normalizedProducts)) {
                $normalizedProducts[$product[0]] = new Set();
            }
            $normalizedProducts[$product[0]]->insert($product[1]);
        }
        return $normalizedProducts;
    }
}
