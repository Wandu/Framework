<?php
namespace Wandu\Installation\Replacers;

use Wandu\Installation\Contracts\ReplacerInterface;
use SplFileInfo;

class OriginReplacer implements ReplacerInterface
{
    /**
     * {@inheritdoc}
     */
    public function replace($contents, $matcher, SplFileInfo $file = null)
    {
        return preg_replace(
            "/{$matcher}/",
            $file ? file_get_contents($file) : '',
            $contents
        );
    }
}
