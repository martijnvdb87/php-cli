<?php

namespace Martijnvdb\PhpCli;

use Martijnvdb\PhpCli\Command;

class Cli {
    
    protected $name;
    protected $version;

    private $commands = [];

    public function __construct(string $name, string $version = '')
    {
        $this->name = $name;
        $this->version = $version;
    }

    public function addCommand(Command $command): Cli
    {
        $this->commands[] = $command;

        return $this;
    }

    public function run()
    {
        global $argv;

        $arguments = array_filter($argv, function($arg, $index) {
            if($index == 0) return false;

            return substr($arg, 0, 1) !== '-';
        }, ARRAY_FILTER_USE_BOTH);

        if(empty($arguments)) {
            foreach($this->commands as $command) {
                if($command->isDefault()) {
                    $command->forceRun();
                    return $this->close();
                }
            }
            return $this->close();
        }

        foreach($arguments as $argument) {
            foreach($this->commands as $command) {
                if($command->hasTrigger($argument)) {
                    $command->run();
                    return $this->close();
                }
            }
        }

        echo "INVALID COMMAND";
    }

    private function close()
    {
        echo "\033[0A";
    }
}