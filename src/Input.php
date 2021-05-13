<?php

namespace Martijnvdb\PhpCli;

class Input
{
    private $type;
    private $label;
    private $required = false;
    private $default;
    private $input_options = [];
    private $choices = [];

    private $required_message = 'This value is required.';
    private $invalid_message = 'This value is invalid.';

    public function __construct(string $label, string $type)
    {
        $this->label = $label;
        $this->type = $type;
    }

    public static function text(string $label): Input
    {
        return new self($label, 'text');
    }

    public static function number(string $label): Input
    {
        return new self($label, 'number');
    }

    public static function url(string $label): Input
    {
        return new self($label, 'url');
    }

    public static function choice(string $label, array $choices): Input
    {
        $input = new self($label, 'choice');
        $input->choices = $choices;
        return $input;
    }

    public function required(?string $message = null): Input
    {
        if (!empty($message)) {
            $this->setRequiredMessage($message);
        }

        $this->required = true;

        return $this;
    }

    public function setDefault($default): Input
    {
        $this->default = $default;

        return $this;
    }

    public function setRequiredMessage(string $message): Input
    {
        $this->required_message = $message;

        return $this;
    }

    public function setInvalidMessage(string $message): Input
    {
        $this->invalid_message = $message;

        return $this;
    }

    public function inputStyling($options = []): Input
    {
        $this->input_options = is_array($options) ? $options : [$options];

        return $this;
    }

    private function getInputValue()
    {
        $output = Output::new()->setStyle($this->input_options);

        $handle = fopen('php://stdin', 'r');
        $value = fgets($handle);

        $output->resetStyle();

        return trim($value);
    }

    public function get()
    {
        $output = Output::new();

        if ($this->type === 'choice') {
            foreach ($this->choices as $key => $value) {
                $output->line("{$key}) {$value}");
            }
        }

        $output->echo($this->label . " ");

        $value = $this->getInputValue();

        if ($value === '' && isset($this->default)) {
            $value = $this->default;
            $output->moveCursorUp();
            $output->echo("\033[2K");

            $output->echo("{$this->label} ");

            $output->setStyle($this->input_options);
            echo "$this->default\n";
            $output->resetStyle();
        }

        if ($this->type === 'choice') {
            $valid_options = array_map(function ($option) {
                return strval($option);
            }, array_keys($this->choices));

            if (!in_array($value, $valid_options)) {
                $output->error($this->invalid_message);
                return $this->get();
            }
        }

        if ($this->type === 'url') {
            if(!filter_var($value, FILTER_VALIDATE_URL)) {
                $output->error($this->invalid_message);
                return $this->get();
            }
        }

        if ($this->required) {
            if (empty($value)) {
                $output->moveCursorUp()->error($this->required_message);
                return $this->get();
            }

            if ($this->type == 'number' && !is_numeric($value)) {
                $output->error($this->invalid_message);
                return $this->get();
            }
        }

        return $value;
    }
}
