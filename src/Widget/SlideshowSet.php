<?php

namespace PhpTui\Slideshow\Widget;

use PhpTui\Tui\Model\WidgetSet;

class SlideshowSet implements WidgetSet
{
    public function renderers(): array
    {
        return [
            new PhpCodeRenderer(),
        ];
    }
}
