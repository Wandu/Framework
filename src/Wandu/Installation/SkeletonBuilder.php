<?php
namespace Wandu\Installation;

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
    public function __construct($targetPath, $skeletonPath = __DIR__ . '/skeleton')
    {
        $this->targetPath = rtrim($targetPath, '/');
        $this->skeletonPath = rtrim($skeletonPath, '/');
    }
    
    public function build(array $attributes = [])
    {
        $this->buildFile($this->skeletonPath, $attributes);
    }
    
    protected function buildFile($file, array $attributes = [])
    {
        $dest = str_replace($this->skeletonPath, $this->targetPath, $file);
        if (is_file($file)) {
            $contents = file_get_contents($file);
            foreach ($attributes as $key => $value) {
                $contents = str_replace("%%{$key}%%", $value, $contents);
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
                $this->buildFile($item->getRealPath(), $attributes);
            }
        }
    }
}
