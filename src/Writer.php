<?php

namespace Martijnvdb\PhpCli;

class Writer {
    const COLOR_BLACK	= 30;
    const COLOR_RED	    = 31;
    const COLOR_GREEN	= 32;
    const COLOR_YELLOW  = 33;
    const COLOR_BLUE	= 34;
    const COLOR_MAGENTA = 35;
    const COLOR_CYAN	= 36;
    const COLOR_WHITE	= 37;
    
    public function __construct() {}

    public function hideCursor()
    {
        echo "\033[?25l";
        
        return $this;
    }

    public function showCursor()
    {
        echo "\033[?25h";

        return $this;
    }

    private function cursorColor($color)
    {
        $value = $this->{$color};
        echo "\033[{$value}m";
    }

    private function cursorBackground($color)
    {
        $value = $this->{$color};
        $value = $value + 10;
        echo "\033[{$value}m";
    }

    public function reset(): Writer
    {
        echo "\033[0m";

        return $this;
    }

    private function parseOptions($options = [])
    {
        $options = is_array($options) ? $options : [$options];

        foreach($options as $option) {
            $option = strtoupper($option);

            if(substr($option, 0, 3) === 'BG:') {
                $option = substr($option, 3);
                $value = constant('self::COLOR_' . $option);
    
                if(empty($value)) {
                    continue;
                }

                $value += 10;
    
                echo "\033[{$value}m";

            } else {
                $value = constant('self::COLOR_' . $option);
    
                if(empty($value)) {
                    continue;
                }
    
                echo "\033[{$value}m";
            }

        }
    }

    public function currentLine(string $value = '', $options = []): Writer
    {
        $this->parseOptions($options);
        echo $value;

        return $this;
    }

    public function newLine(string $value = '', $options = []): Writer
    {
        $this->parseOptions($options);
        echo "\n" . $value;

        return $this;
    }

    public function clearLine($value = '')
    {
        echo "\033[0G"; // Move to begin of line
        echo "\033[K"; // Clear current line

        return $this;
    }

    public function sleep($seconds = 0)
    {
        sleep($seconds);

        return $this;
    }
}