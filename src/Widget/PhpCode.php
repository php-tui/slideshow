<?php

namespace PhpTui\Slideshow\Widget;

use PhpTui\Tui\Widget\Widget;


class PhpCode implements Widget
{
    public function __construct(
        public string $code,
    ) {
    }
}
