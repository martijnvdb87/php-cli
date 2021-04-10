<?php

namespace Martijnvdb\PhpCli;

use Martijnvdb\PhpCli\Writer;

class Input {
    private $type;
    private $label;
    private $required = false;
    private $input_options = [];

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

    public function required(): Input
    {
        $this->required = true;

        return $this;
    }

    public function inputStyling($options = []): Input
    {
        $this->input_options = is_array($options) ? $options : [$options];

        return $this;
    }

    private function getInputValue()
    {
        $writer = Writer::new()->setStyle($this->input_options);

        $handle = fopen('php://stdin', 'r');
        $value = fgets($handle);
        
        $writer->resetStyle();

        return trim($value);
    }

    public function run()
    {
        $writer = Writer::new()->line($this->label);
        $value = $this->getInputValue();

        if($this->required) {
            if(empty($value)) {
                return $this->run();
            }
            
            if($this->type == 'number' && !is_numeric($value)) {
                return $this->run();
            }
        }

        return $value;
    }
}