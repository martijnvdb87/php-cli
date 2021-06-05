<?php

namespace Martijnvdb\PhpCli;

use Martijnvdb\PhpCli\Output;

class Progress
{
    private $output;
    private $current_progress = 0;
    private $in_progress = false;
    private $time_start = null;

    private $template_foreground = "[bg:white][invisible]|[/invisible][/bg:white]";
    private $template_background = "[bg:240][invisible].[/invisible][/bg:240]";

    private $size = 30;

    public function __construct()
    {
        $this->output = Output::new();
    }

    public static function new()
    {
        return new self;
    }

    public function foreground(string $template): self
    {
        $this->template_foreground = $template;
        return $this;
    }

    public function background(string $template): self
    {
        $this->template_background = $template;
        return $this;
    }

    public function start(): self
    {
        $this->in_progress = true;
        $this->time_start = time();
        $this->update('started');
        return $this;
    }

    public function set(float $current_progress): self
    {
        $current_progress = max(0, $current_progress);
        $current_progress = min(1, $current_progress);

        $this->current_progress = $current_progress;
        $this->update();
        return $this;
    }

    public function stop(): self
    {
        if($this->in_progress) {
            $this->in_progress = false;
            $this->update('stopped');
        }
        return $this;
    }

    public function size(int $size): self
    {
        $size = max(0, $size);
        $size = min(100, $size);

        $this->size = $size;
        return $this;
    }
    
    private function update(string $action = ''): self
    {
        if($this->in_progress || !empty($action)) {
            $current_percentage = floor($this->current_progress * 100);
            $current_blocks = floor($this->size * $this->current_progress);
            $rest_blocks = $this->size - $current_blocks;
    
            $line = str_repeat($this->template_foreground, $current_blocks);
            $line .= str_repeat($this->template_background, $rest_blocks);
            $line .= ' | ';
            $line .= ($this->current_progress == 0 ? ' ' : '') . ($this->current_progress < 1 ? ' ' : '') . $current_percentage . '%';

            $time_delta = time() - $this->time_start;
            
            if($this->current_progress === 0) {
                $line .= ' | ETA: ∞';
    
            } else if($action === 'stopped') {
                $line .= '';
    
            } else {
                $line .= ' | ETA: ' . round($time_delta * (1 - $this->current_progress) / $this->current_progress) . 's';
            }
            
            $line .= ' | Total time: ' . round($time_delta) . 's';
    
            if($action !== 'started') {
                $this->output->moveCursorUp();
            }

            $this->output->clearLine();
            $this->output->line($line);
        }

        return $this;
    }
}