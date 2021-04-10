<?php

namespace Martijnvdb\PhpCli;

use Martijnvdb\PhpCli\Output;

class Input {
    private $type;
    private $label;
    private $required = false;
    private $input_options = [];

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

    public function required(?string $message = null): Input
    {
        if(!empty($message)) {
            $this->setRequiredMessage($message);
        }

        $this->required = true;

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
        $output = Output::new()->line($this->label);
        $value = $this->getInputValue();

        if($this->required) {
            if(empty($value)) {
                $output->moveCursorUp()->error($this->required_message);
                return $this->get();
            }
            
            if($this->type == 'number' && !is_numeric($value)) {
                $output->error($this->invalid_message);
                return $this->get();
            }
        }

        return $value;
    }
}