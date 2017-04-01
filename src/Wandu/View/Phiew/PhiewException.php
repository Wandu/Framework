<?php
namespace Wandu\View\Phiew;

use RuntimeException;

class PhiewException extends RuntimeException
{
    const CODE_ALREADY_RENDERING = 1;
    const CODE_WRONG_SYNTAX = 2;
}
