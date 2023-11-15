<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Term\Event;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Extension\Core\Widget\Paragraph;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\Block;
use PhpTui\Tui\Extension\Bdf\Shape\TextShape;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\ImageMagick\Shape\ImageShape;
use PhpTui\Tui\Extension\ImageMagick\Widget\ImageWidget;
use PhpTui\Tui\Model\AnsiColor;
use PhpTui\Tui\Model\Constraint;
use PhpTui\Tui\Model\Direction;
use PhpTui\Tui\Model\Marker;
use PhpTui\Tui\Model\Widget;
use PhpTui\Tui\Model\Widget\FloatPosition;
use PhpTui\Tui\Model\Widget\HorizontalAlignment;

final class TitlePargagraphAndImageDT implements Slide
{
    public function __construct(
        private string $image,
        private string $title,
        private string $text,
    ) {
    }
    public function title(): string
    {
        return $this->title;
    }

    public function build(): Widget
    {
        return Grid::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::percentage(10),
                Constraint::percentage(80),
                Constraint::percentage(10),
            )
            ->widgets(
                Canvas::fromIntBounds(0, 100, 0, 6)
                    ->draw(
                        new TextShape(
                            'default',
                            $this->title(),
                            AnsiColor::Cyan,
                            FloatPosition::at(0, 0),
                            scaleX: 1,
                            scaleY: 1,
                        ),
                    ),
                $this->image(),
                Block::default()
                    ->padding(Padding::fromScalars(1, 1, 1, 1))
                    ->widget(
                        $this->text(),
                    )
            );
    }

    public function handle(Tick|Event $event): void
    {
    }

    private function text(): Widget
    {
        return Paragraph::fromString($this->text)->alignment(HorizontalAlignment::Center);
    }

    private function image(): Widget
    {
        return new ImageWidget($this->image);
    }
}
