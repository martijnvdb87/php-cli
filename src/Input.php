<?php

namespace Martijnvdb\PhpCli;

use Martijnvdb\PhpCli\Writer;

class Input {
    private $type;
    private $label;
    private $required = false;
    private $label_options = [];
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

    public function setLabelOption(array $options = []): Input
    {
        $this->label_options = $options;

        return $this;
    }

    public function setInputOption(array $options = []): Input
    {
        $this->input_options = $options;

        return $this;
    }

    public function required(): Input
    {
        $this->required = true;

        return $this;
    }

    private function runText()
    {
        $writer = Writer::new()
            ->currentLine($this->label, $this->label_options)
            ->reset()
            ->currentLine(' ')
            ->reset()
            ->currentLine('', $this->input_options);
        
        $handle = fopen('php://stdin', 'r');
        $value = fgets($handle);
        $value = trim($value);

        $writer->reset();

        if($this->required) {
            if(empty($value)) {
                return $this->runText();
            }
        }
        
        return $value;
    }

    private function runNumber()
    {
        $writer = Writer::new()
            ->currentLine($this->label, $this->label_options)
            ->reset()
            ->currentLine(' ')
            ->reset()
            ->currentLine('', $this->input_options);
        
        $handle = fopen('php://stdin', 'r');
        $value = fgets($handle);
        $value = trim($value);

        $writer->reset();

        if($this->required) {
            if(empty($value) || !is_numeric($value)) {
                return $this->runNumber();
            }
        }

        return $value;
    }

    public function run()
    {
        return $this->{'run' . ucfirst($this->type)}();
    }
}