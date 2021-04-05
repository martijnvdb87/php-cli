<?php

namespace Martijnvdb\PhpCli;

use Martijnvdb\PhpCli\Writer;

class Command {

    /**
     * All the registered triggers.
     * @var array
     */
    protected $triggers = [];

    /**
     * All the registered actions.
     * @var array
     */
    protected $actions = [];

    /**
     * All the registered options.
     * @var array
     */
    protected $options = [];

    /**
     * All the given input.
     * @var array
     */
    protected $input = [];

    public function __construct($trigger) {
        $this->addTrigger($trigger);
    }

    /**
     * Create new Command.
     * 
     * @return Command
     */
    public static function new(string $trigger): Command
    {
        return new self($trigger);
    }

    /**
     * Create new Command.
     * 
     * @return Command
     */
    public static function default(): Command
    {
        return new self('__default__');
    }

    /**
     * Register a new trigger string.
     * 
     * @param  string $trigger
     * @return Command
     */
    public function addTrigger(string $trigger): Command
    {
        $this->triggers[] = $trigger;

        return $this;
    }

    /**
     * Register a new action.
     * 
     * @param  callable $action
     * @return Command
     */
    public function addAction(callable $action): Command
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Register a new option.
     * 
     * @param  string $key
     * @param  string $description
     * @param  string $shorthand
     * @return Command
     */
    public function addOption(string $key, string $description, string $shorthand): Command
    {
        $this->options[] = (object) [
            'key' => $key,
            'description' => $description,
            'shorthand' => $shorthand
        ];

        return $this;
    }

    public function isDefault(): bool
    {
        foreach($this->triggers as $trigger) {
            if($trigger === '__default__') {
                return true;
            }
        }

        return false;
    }

    public function setInput(string $key, string $value): Command
    {
        $this->input[$key] = $value;

        return $this;
    }

    public function forceRun(): bool
    {
        global $argv;

        $this->input = [];

        foreach($this->options as $option) {
            $this->input[$option->key] = false;

            $index = array_search("--$option->key", $argv) ? array_search("--$option->key", $argv) : array_search("-$option->shorthand", $argv);

            if($index !== false) {
                $this->input[$option->key] = true;

                if(sizeof($argv) > $index && substr($argv[$index + 1], 0, 1) !== '-') {
                    $this->input[$option->key] = $argv[$index + 1];
                }

                continue;
            }
        }

        foreach($this->actions as $action) {
            call_user_func($action, (object) $this->input, (new Writer), $this);
        }

        return true;
    }

    public function run(): bool
    {
        global $argv;

        foreach($this->triggers as $trigger) {

            $index = array_search($trigger, $argv);

            if(empty($index)) {
                continue;
            }

            return $this->forceRun();
        }

        return false;
    }
}