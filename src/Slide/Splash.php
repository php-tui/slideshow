<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Term\Event;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Extension\Bdf\Shape\TextShape;
use PhpTui\Tui\Extension\Core\Shape\Line as PhpTuiLine;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Model\AnsiColor;
use PhpTui\Tui\Model\AxisBounds;
use PhpTui\Tui\Model\Canvas\CanvasContext;
use PhpTui\Tui\Model\Style;
use PhpTui\Tui\Model\Widget;
use PhpTui\Tui\Model\Widget\FloatPosition;
use PhpTui\Tui\Model\Widget\Line;

class Splash implements Slide
{
    public function title(): string
    {
        return 'Building a better world';
    }

    public function build(): Widget
    {
        return Canvas::default()
            ->xBounds(AxisBounds::new(0, 320))
            ->yBounds(AxisBounds::new(0, 240))
            ->paint(function (CanvasContext $context): void {
                $title = new TextShape(
                    'default',
                    'PHP-TUI',
                    AnsiColor::Cyan,
                    FloatPosition::at(10, 200),
                    scaleX: 4,
                    scaleY: 4,
                );
                $subTitle = new TextShape(
                    'default',
                    'Building better TUIs!',
                    AnsiColor::White,
                    FloatPosition::at(10, 180),
                    scaleX: 2,
                    scaleY: 2,
                );
                $context->draw($title);
                $context->draw($subTitle);
                $context->draw(PhpTuiLine::fromScalars(0, 160, 320, 160)->color(AnsiColor::Gray));
                $context->print(12, 140, Line::fromString('Daniel Leech 2024'));
                $context->print(12, 130, Line::fromString('@dantleech@fosstodon.org')->patchStyle(Style::default()->fg(AnsiColor::LightMagenta)));
                $context->print(12, 120, Line::fromString('https://www.dantleech.com')->patchStyle(Style::default()->fg(AnsiColor::LightYellow)));
            });
    }

    public function handle(Tick|Event $event): void
    {
    }
}
