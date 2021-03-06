<?php

namespace Martijnvdb\PhpCli;

class Cli
{
    private $name;
    private $version;

    private $error_message = 'Command "[command]" is not defined.';

    const DEFAULT = '__DEFAULT__';

    private $commands = [];

    public function __construct(string $name, string $version = '')
    {
        $this->name = $name;
        $this->version = $version;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setErrorMessage(string $error): Cli
    {
        $this->error_message = $error;

        return $this;
    }

    public function add(): Cli
    {
        $command = self::DEFAULT;
        $callback = null;

        foreach (func_get_args() as $arg) {
            if (is_callable($arg)) {
                $callback = $arg;
            } else if (is_string($arg)) {
                $command = $arg;
            }
        }

        if (isset($callback)) {
            $this->commands[$command] = $callback;
        }

        return $this;
    }

    public function forceRun(string $command, array $args = []): void
    {
        $this->commands[$command](Argument::new($this, $args), Output::new($this));
    }

    public function run(): void
    {
        global $argv;

        $command = !empty($argv[1]) ? $argv[1] : null;

        if (substr($command, 0, 1) === '-') {
            $command = null;
        }

        if (empty($command) && isset($this->commands[self::DEFAULT])) {
            $command = self::DEFAULT;
        }

        if (!in_array($command, array_keys($this->commands))) {
            $message = str_replace('[command]', $command, $this->error_message);

            Output::new()->error($message, 'large');
            return;
        }

        if (empty($command)) {
            return;
        }

        $this->commands[$command](Argument::new($this), Output::new($this));
    }
}
