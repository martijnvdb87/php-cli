<?php

namespace Martijnvdb\PhpCli;

use Martijnvdb\PhpCli\Argument;

class Cli {
    
    protected $name;
    protected $version;

    const DEFAULT = '__DEFAULT__';

    private $commands = [];

    public function __construct(string $name, string $version = '')
    {
        $this->name = $name;
        $this->version = $version;
    }

    public function add(): Cli
    {
        $command = self::DEFAULT;
        $callback = null;

        foreach(func_get_args() as $arg) {
            if(is_callable($arg)) {
                $callback = $arg;

            } else if(is_string($arg)) {
                $command = $arg;
            }
        }

        if(isset($callback)) {
            $this->commands[$command] = $callback;
        }

        return $this;
    }

    public function run(): void
    {
        global $argv;

        $command = null;

        foreach($argv as $arg) {
            // If options are found, stop searching for the command
            if(substr($arg, 0, 1) === '-') {
                break;
            }

            if(in_array($arg, array_keys($this->commands))) {
                $command = $arg;
                break;
            }
        }

        if(empty($command) && isset($this->commands[self::DEFAULT])) {
            $command = self::DEFAULT;
        }

        if(empty($command)) {
            return;
        }

        $this->commands[$command](Argument::new(), Writer::new());
    }

    private function close()
    {
        echo "\033[0A";
    }
}