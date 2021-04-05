<?php

namespace Martijnvdb\PhpCli\Input;

use Martijnvdb\PhpCli\Writer;

class Text {

    public static function new(string $label, $label_options = [], $input_options = []): string
    {
        $writer = new Writer();
        $writer->currentLine($label, $label_options)->reset();
        $writer->currentLine(' ')->reset();
        $writer->currentLine('', $input_options);
        
        $handle = fopen('php://stdin', 'r');
        $value = fgets($handle);
        $value = trim($value);

        $writer->reset();
        return $value;
    }
}