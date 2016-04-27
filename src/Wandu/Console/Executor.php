<?php
namespace Wandu\Wuild\Console;

use Symfony\Component\Console\Output\OutputInterface;

class Executor
{
    public function __construct(OutputInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param string $command
     * @return string
     */
    public function execute($command)
    {
        return trim(shell_exec($command));
    }

    /**
     * @param string $command
     * @return int
     */
    public function executeWithWriter($command)
    {
        $filePointer = popen($command, 'r');

        if ($filePointer) {
            while (!feof($filePointer)) {
                $this->writer->write(fread($filePointer, 1024));
                flush(); // you have to flush buffer
            }
            return pclose($filePointer);
        }
        return -1;
    }

    /**
     * @param string $command
     * @param string $insertContents
     * @return string
     */
    public function executeWithPipe($command, $insertContents)
    {
        $process = proc_open($command, [
            ['pipe', 'r'],
            ['pipe', 'w']
        ], $pipes);

        if (is_resource($process)) {
            list($outPipe, $inPipe) = $pipes;

            fwrite($outPipe, $insertContents);
            $result = stream_get_contents($inPipe);
            return $result;
        }
    }
}
