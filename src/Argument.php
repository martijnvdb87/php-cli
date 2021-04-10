<?php

namespace Martijnvdb\PhpCli;

class Argument {
    private $arguments = [];
    
    public function __construct() {
        global $argv;

        $arguments = [];

        foreach($argv as $index => $arg) {
            if(substr($arg, 0, 1) !== '-') {
                continue;
            }

            $arguments[$arg] = true;

            if(isset($argv[$index + 1]) && substr($argv[$index + 1], 0, 1) !== '-') {
                $arguments[$arg] = $argv[$index + 1];
            }
        }

        $this->arguments = $arguments;
    }

    public static function new()
    {
        return new self();
    }

    public function get()
    {
        $value = null;

        foreach(func_get_args() as $arg) {
            if(isset($this->arguments[$arg])) {
                $value = $this->arguments[$arg];
                break;
            }
        }

        return $value;
    }
}