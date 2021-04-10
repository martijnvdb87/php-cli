<?php

namespace Martijnvdb\PhpCli;

class Writer {
    private $prefix = "\033[";
    private $reset = "0m";

    private $tags = [        
        // Style
        'b'             => "1m",
        'bold'          => "1m",
        'dim'           => "2m",
        'dim'           => "2m",
        'i'             => "3m",
        'italic'        => "3m",
        'u'             => "4m",
        'underline'     => "4m",
        'blink'         => "5m",
        'reverse'       => "7m",
        'invisible'     => "8m",
        's'             => "9m",
        'strikethrough' => "9m",

        // Colors
        'black'         => "30m",
        'red'           => "31m",
        'green'         => "32m",
        'yellow'        => "33m",
        'blue'          => "34m",
        'magenta'       => "35m",
        'cyan'          => "36m",
        'white'         => "37m",

        // Background colors
        'bg:black'      => "40m",
        'bg:red'        => "41m",
        'bg:green'      => "42m",
        'bg:yellow'     => "43m",
        'bg:blue'       => "44m",
        'bg:magenta'    => "45m",
        'bg:cyan'       => "46m",
        'bg:white'      => "47m",
    ];
    
    public function __construct() {}

    public static function new()
    {
        return new self();
    }

    private function parseTags(string $text): string
    {
        $parts = preg_split('/(\[\/?.+?\])/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $output = '';
        $options = [];

        foreach($parts as $part) {
            // Check if part is text or tag
            preg_match('/\[(\/?)(.+?)\]/', $part, $matches);

            // Append text to output with styled options
            if(empty($matches)) {

                // First reset all styles
                $output .= $this->prefix . $this->reset;

                foreach($options as $option) {
                    $option = strtolower($option);

                    if(!isset($this->tags[$option])) {
                        continue;
                    }

                    $output .= $this->prefix . $this->tags[$option];
                }

                $output .= $part;

                continue;
            }

            // Option change
            $is_opening = $matches[1] === '/' ? false : true;
            $tag = $matches[2];

            if(!isset($this->tags[$tag])) {
                $output .= $part;
                continue;
            }

            if($is_opening) {
                $options[] = $tag;

            } else {
                $found = false;

                // Remove tags from options list
                for($i = sizeof($options) - 1; $i >= 0; $i--) {
                    if($options[$i] === $tag) {
                        array_splice($options, $i, 1);
                        $found = true;
                        break;
                    }
                }

                // Show closing tag if no opening tag is found
                if(!$found) {
                    $output .= $part;
                    continue;
                }
            }
        }

        return $output;
    }

    public function setStyle($options = []): Writer
    {
        $options = is_array($options) ? $options : [$options];

        foreach($options as $option) {
            if(isset($this->tags[$option])) {
                echo $this->prefix . $this->tags[$option];
            }
        }
        
        return $this;
    }

    public function resetStyle(): Writer
    {
        echo $this->prefix . $this->reset;

        return $this;
    }

    public function line(string $value = ''): Writer
    {
        $value = $this->parseTags($value);
        echo $value . "\n";

        return $this;
    }

    public function lines(array $lines = []): Writer
    {
        foreach($lines as $line) {
            $this->line($line);
        }

        return $this;
    }

    public function clearLine($value = ''): Writer
    {
        echo "\033[0G"; // Move to begin of line
        echo "\033[K"; // Clear current line

        return $this;
    }

    public function sleep($seconds = 0): Writer
    {
        sleep($seconds);

        return $this;
    }
}