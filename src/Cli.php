<?php

namespace Martijnvdb\PhpCli;

class Cli {
    public function __construct() {}

    public function hideCursor()
    {
        echo "\033[?25l";
        
        return $this;
    }

    public function showCursor()
    {
        echo "\033[?25h";

        return $this;
    }

    public function currentLine($value = '')
    {
        echo $value;

        return $this;
    }

    public function newLine($value = '')
    {
        echo "\n" . $value;

        return $this;
    }

    public function clearLine($value = '')
    {
        echo "\033[0G"; // Move to begin of line
        echo "\033[K"; // Clear current line

        return $this;
    }

    public function sleep($seconds = 0)
    {
        sleep($seconds);

        return $this;
    }
}