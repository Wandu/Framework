<?php
namespace Wandu\Installation\Replacers;

use Wandu\Installation\Contracts\ReplacerInterface;
use SplFileInfo;

class CallableReplacer implements ReplacerInterface
{
    /**
     * @param callable $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(string $contents, string $matcher, SplFileInfo $dest = null) :string
    {
        return preg_replace_callback("/{$matcher}/", $this->handler, $contents);
    }
}
