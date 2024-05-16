<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Term\Event;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\Paragraph;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\Block;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Extension\ImageMagick\Shape\ImageShape;
use PhpTui\Tui\Extension\ImageMagick\Widget\ImageWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\HorizontalAlignment;
use PhpTui\Tui\Widget\Widget;

final class PargagraphAndImageDT implements Slide
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
        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::percentage(80),
                Constraint::percentage(20),
            )
            ->widgets(
                $this->image(),
                BlockWidget::default()
                ->padding(Padding::fromScalars(1, 1, 1, 1))
                ->widget(
                    $this->text(),
                ),
            );
    }

    public function handle(Tick|Event $event): void
    {
    }

    private function text(): Widget
    {
        return ParagraphWidget::fromString($this->text)->alignment(HorizontalAlignment::Center);
    }

    private function image(): Widget
    {
        return new ImageWidget($this->image);
    }
}
