<?php
namespace Wandu\Installation\Contracts;

use SplFileInfo;

interface ReplacerInterface
{
    /**
     * @param string $contents
     * @param string $matcher
     * @param \SplFileInfo $dest
     * @return string
     */
    public function replace($contents, $matcher, SplFileInfo $dest = null);
}
