<?php

namespace Martijnvdb\PhpCli;

class Output
{
    private $cli;
    private $prefix = "\033[";
    private $reset = "0m";

    private $spacing = 2;

    private $tags = [
        // Style
        'b'             => "1m",
        'bold'          => "1m",
        'dim'           => "2m",
        'i'             => "3m",
        'italic'        => "3m",
        'u'             => "4m",
        'underline'     => "4m",
        'blink'         => "5m",
        'inverse'       => "7m",
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

    public function __construct(?Cli $cli = null)
    {
        for($i = 0; $i <= 255; $i++) {
            $this->tags[$i] = "38;5;{$i}m";
            $this->tags["bg:$i"] = "48;5;{$i}m";
        }
        
        $this->cli = $cli;
    }

    public static function new(?Cli $cli = null): Output
    {
        return new self($cli);
    }

    public static function error(string $error, ?string $type = null): Output
    {
        return (new self())->showError($error, $type);
    }

    public function showError(string $error, ?string $type = null): Output
    {
        if ($type === 'large') {
            $space = str_repeat('-', $this->spacing);
            $length = strlen($error);
            $block = str_repeat('-', $length + ($this->spacing * 2));

            $this->lines([
                "",
                "[bg:red][invisible]{$block}[/invisible][/bg:red]",
                "[bg:red][invisible]{$space}[/invisible][white]{$error}[/white][invisible]{$space}[/invisible][/bg:red]",
                "[bg:red][invisible]{$block}[/invisible][/bg:red]"
            ]);
        } else {
            $this->lines([
                "",
                "[bg:red][white]{$error}[/white][/bg:red]",
                ""
            ]);
        }

        return $this;
    }

    private function parseTags(string $text): string
    {
        $parts = preg_split('/(\[\/?.+?\])/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $output = '';
        $options = [];

        foreach ($parts as $part) {
            // Check if part is text or tag
            preg_match('/\[(\/?)(.+?)\]/', $part, $matches);

            // Append text to output with styled options
            if (empty($matches)) {

                // First reset all styles
                $output .= $this->prefix . $this->reset;

                foreach ($options as $option) {
                    $option = strtolower($option);

                    if (!isset($this->tags[$option])) {
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

            if (!isset($this->tags[$tag])) {
                $output .= $part;
                continue;
            }

            if ($is_opening) {
                $options[] = $tag;
            } else {
                $found = false;

                // Remove tags from options list
                for ($i = sizeof($options) - 1; $i >= 0; $i--) {
                    if ($options[$i] === $tag) {
                        array_splice($options, $i, 1);
                        $found = true;
                        break;
                    }
                }

                // Show closing tag if no opening tag is found
                if (!$found) {
                    $output .= $part;
                    continue;
                }
            }
        }

        return $output;
    }

    public function setStyle($options = []): Output
    {
        $options = is_array($options) ? $options : [$options];

        foreach ($options as $option) {
            if (isset($this->tags[$option])) {
                echo $this->prefix . $this->tags[$option];
            }
        }

        return $this;
    }

    public function resetStyle(): Output
    {
        echo $this->prefix . $this->reset;

        return $this;
    }

    public function version(): Output
    {
        $name = $this->cli->getName();
        $version = $this->cli->getVersion();

        $this->line("{$name} [green]{$version}[/green]\n");

        return $this;
    }

    public function paragraph(string $value = ''): Output
    {
        $value = $this->parseTags($value);
        echo $value . "\n\n";

        return $this;
    }

    public function echo(string $value = ''): Output
    {
        $value = $this->parseTags($value);
        echo $value;

        return $this;
    }

    public function line(string $value = ''): Output
    {
        $value = $this->parseTags($value);
        echo $value . "\n";

        return $this;
    }

    public function lines(array $lines = []): Output
    {
        foreach ($lines as $line) {
            $this->line($line);
        }

        return $this;
    }

    public function columns(string $label, array $rows = [], array $column_styles = []): Output
    {
        $column_lengths = [];

        foreach ($rows as $row) {
            foreach ($row as $index => $value) {
                if (!isset($column_lengths[$index])) {
                    $column_lengths[$index] = 0;
                }

                $length = strlen($value);

                if ($column_lengths[$index] < $length) {
                    $column_lengths[$index] = $length;
                }
            }
        }

        $tags = [];

        foreach ($column_styles as &$column_style) {
            if (empty($column_style)) {
                $column_style = [];
            } else {
                $column_style = is_array($column_style) ? $column_style : [$column_style];
            }
        }

        $this->line("[yellow]{$label}[/yellow]");
        $indent = str_repeat(' ', $this->spacing);

        foreach ($rows as $row) {
            $line = '';

            foreach ($row as $index => $value) {
                $space = str_repeat(' ', ($column_lengths[$index] - strlen($value)));

                if (isset($column_styles[$index])) {
                    foreach ($column_styles[$index] as $tag) {
                        $line .= "[{$tag}]";
                    }
                }

                $line .= "{$indent}{$value}{$space}";

                if (isset($column_styles[$index])) {
                    foreach ($column_styles[$index] as $tag) {
                        $line .= "[/{$tag}]";
                    }
                }
            }

            $this->line($line);
        }

        return $this;
    }

    public function sleep($seconds = 0): Output
    {
        sleep($seconds);

        return $this;
    }

    public function moveCursorUp($lines = 0): Output
    {
        echo "\033[{$lines}A";

        return $this;
    }

    public function moveCursorDown($lines = 0): Output
    {
        echo "\033[{$lines}B";

        return $this;
    }

    public function hideCursor(bool $hide_cursor = true): Output
    {
        if($hide_cursor) {
            echo "\033[?25l";
            
        } else {
            echo "\033[?25h";
        }

        return $this;
    }
}
