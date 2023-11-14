<?php

namespace PhpTui\Slideshow\Widget;

use PhpTui\Tui\Model\Canvas\ShapeSet;

class SlideshowSet implements ShapeSet
{
    public function shapes(): array
    {
        return [
            new PhpCodeRenderer(),
        ];
    }
}
