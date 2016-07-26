<?php
namespace Wandu\Database\Contracts;

interface DumperInterface
{
    /**
     * @param string $dumpTargetFile
     * @param string[] $ignoreTables
     */
    public function dump($dumpTargetFile = null, array $ignoreTables = []);

    /**
     * @param string $dumpTargetFile
     * @param string[] $ignoreTables
     */
    public function restore($dumpTargetFile, array $ignoreTables = []);
}
