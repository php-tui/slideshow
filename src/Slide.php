<?php

namespace PhpTui\Slideshow;

use PhpTui\Term\Event;
use PhpTui\Tui\Widget\Widget;

interface Slide
{
    public function title(): string;
    public function build(): Widget;
    public function handle(Tick|Event $event): void;
}
