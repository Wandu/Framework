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
    public function replace($contents, $matcher, SplFileInfo $dest = null)
    {
        return preg_replace_callback("/{$matcher}/", $this->handler, $contents);
    }
}
