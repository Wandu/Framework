<?php
namespace Wandu\Installation\Replacers;

use Wandu\Installation\Contracts\ReplacerInterface;
use SplFileInfo;

class StringReplacer implements ReplacerInterface
{
    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(string $contents, string $matcher, SplFileInfo $dest = null) :string
    {
        return preg_replace("/{$matcher}/", $this->text, $contents);
    }
}
