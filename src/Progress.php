<?php

namespace Martijnvdb\PhpCli;

use Martijnvdb\PhpCli\Output;

class Progress
{
    private $output;
    private $current_progress = 0;
    private $in_progress = false;
    private $time_start = null;

    private $size = 30;

    public function __construct()
    {
        $this->output = Output::new();
        $this->time_start = time();
        $this->update();
    }

    public static function new()
    {
        return new self;
    }

    public function set(float $current_progress): self
    {
        $current_progress = max(0, $current_progress);
        $current_progress = min(1, $current_progress);

        $this->current_progress = $current_progress;
        $this->update();
        return $this;
    }

    public function size(int $size): self
    {
        $size = max(0, $size);
        $size = min(100, $size);

        $this->size = $size;
        return $this;
    }
    
    private function update(): self
    {
        $current_block_template = "[bg:white][invisible]|[/invisible][/bg:white]";
        $rest_block_template = "[bg:240][invisible].[/invisible][/bg:240]";

        $current_percentage = ceil($this->current_progress * 100);
        $current_blocks = ceil($this->size * $this->current_progress);
        $rest_blocks = $this->size - $current_blocks;

        $line = str_repeat($current_block_template, $current_blocks);
        $line .= str_repeat($rest_block_template, $rest_blocks);
        $line .= ' | ';
        $line .= ($this->current_progress == 0 ? ' ' : '') . ($this->current_progress < 1 ? ' ' : '') . $current_percentage . '%';

        $time_delta = time() - $this->time_start;
        
        if($this->current_progress === 0) {
            $line .= ' | ETA: ∞';

        } else if($this->current_progress === 1) {
            $line .= ' | Finished';

        } else {
            $line .= ' | ETA: ' . round($time_delta * (1 - $this->current_progress) / $this->current_progress) . 's';
        }
        
        $line .= ' | Total time: ' . round($time_delta) . 's';

        if($this->in_progress) {
            $this->output->moveCursorUp();
        }

        $this->output->clearLine();
        $this->output->line($line);

        $this->in_progress = true;

        if($this->current_progress >= 1) {
            $this->in_progress = false;
        }

        return $this;
    }
}