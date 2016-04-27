<?php
namespace Wandu\Console;

class Reader
{
    /** @var resource */
    private $filePointer;

    public function __construct($fileName = 'php://stdin')
    {
        $this->filePointer = fopen($fileName, 'r');
    }

    /**
     * @param bool $isSecret
     * @return string
     */
    public function read($isSecret = false)
    {
        if (!$isSecret) {
            return rtrim(fgets($this->filePointer, 255), "\r\n");
        }
        return $this->readFromPassword();
    }

    /**
     * @link http://stackoverflow.com/questions/187736/command-line-password-prompt-in-php
     * @return string
     */
    protected function readFromPassword()
    {
        if (preg_match('/^win/i', PHP_OS)) {
            $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
            file_put_contents($vbscript, 'wscript.echo(InputBox("", "", "password here"))');
            $command = "cscript //nologo " . escapeshellarg($vbscript);
            $password = rtrim(shell_exec($command), "\r\n");
            unlink($vbscript);
            return $password;
        }
        $command = "/usr/bin/env bash -c 'echo OK'";
        if (rtrim(shell_exec($command)) !== 'OK') {
            trigger_error("Can't invoke bash");
            return '';
        }
        $command = "/usr/bin/env bash -c 'read -s secret && echo \$secret'";
        $password = rtrim(shell_exec($command), "\r\n");
        return $password;
    }
}
