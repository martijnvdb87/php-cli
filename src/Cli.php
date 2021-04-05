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

    public function run(): void
    {
        global $argv;

        $arguments = array_filter($argv, function($arg) {
            return substr($arg, 0, 1) !== '-';
        });

        if(sizeof($arguments) == 1) {
            foreach($this->commands as $command) {
                if($command->isDefault()) {
                    $command->forceRun();
                    return;
                }
            }

            return;
        }

        foreach($this->commands as $command) {
            if($command->run()) {
                return;
            }
        }

        echo "INVALID COMMAND";
    }
}