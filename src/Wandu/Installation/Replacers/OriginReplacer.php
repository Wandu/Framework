<?php
namespace Wandu\Installation\Replacers;

use Wandu\Installation\Contracts\ReplacerInterface;
use SplFileInfo;

class OriginReplacer implements ReplacerInterface
{
    /**
     * {@inheritdoc}
     */
    public function replace(string $contents, string $matcher, SplFileInfo $file = null) :string
    {
        return preg_replace(
            "/{$matcher}/",
            $file ? file_get_contents($file) : '',
            $contents
        );
    }
}
