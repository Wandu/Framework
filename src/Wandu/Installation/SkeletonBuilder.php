<?php
namespace Wandu\Installation;

use Wandu\Installation\Contracts\ReplacerInterface;
use SplFileInfo;
use Wandu\Installation\Replacers\CallableReplacer;
use Wandu\Installation\Replacers\StringReplacer;

class SkeletonBuilder
{
    /** @var string */
    protected $targetPath;
    
    /** @var string */
    protected $skeletonPath;
    
    /**
     * @param string $targetPath
     * @param string $skeletonPath
     */
    public function __construct($targetPath, $skeletonPath)
    {
        $this->targetPath = rtrim($targetPath, '/');
        $this->skeletonPath = rtrim($skeletonPath, '/');
    }
    
    public function build(array $replacers = [])
    {
        $this->buildFile($this->skeletonPath, $this->normalizeReplacers($replacers));
    }

    /**
     * @param string $file
     * @param \Wandu\Installation\Contracts\ReplacerInterface[] $replaces
     */
    protected function buildFile($file, array $replaces = [])
    {
        $dest = str_replace($this->skeletonPath, $this->targetPath, $file);
        if (is_file($file)) {
            $contents = file_get_contents($file);
            foreach ($replaces as $matcher => $replacer) {
                $contents = $replacer->replace(
                    $contents,
                    $matcher,
                    file_exists($dest) ? new SplFileInfo($dest) : null
                );
            }
            file_put_contents($dest, $contents);
            return;
        }
        if (!is_dir($dest)) {
            mkdir($dest);
        }
        foreach (new \DirectoryIterator($file) as $item) {
            if ($item->getFilename() === '.' || $item->getFilename() === '..' || $item->getFilename() === '.gitkeep') {
                continue;
            } else {
                $this->buildFile($item->getRealPath(), $replaces);
            }
        }
    }

    /**
     * @param array $replacers
     * @return \Wandu\Installation\Contracts\ReplacerInterface[]
     */
    protected function normalizeReplacers(array $replacers = [])
    {
        foreach ($replacers as $matcher => $replacer) {
            if ($replacer instanceof ReplacerInterface) {
                continue;
            }
            if (is_callable($replacer)) {
                $replacers[$matcher] = new CallableReplacer($replacer);
            } else {
                $replacers[$matcher] = new StringReplacer($replacer);
            }
        }
        return $replacers;
    }
}
