<?php
namespace Wandu\Installation\Replacers;

use Wandu\Installation\Contracts\ReplacerInterface;
use SplFileInfo;

class StringReplacer implements ReplacerInterface
{
    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function replace($contents, $matcher, SplFileInfo $dest = null)
    {
        return preg_replace("/{$matcher}/", $this->text, $contents);
    }
}
