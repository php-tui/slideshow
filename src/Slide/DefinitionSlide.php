<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Term\Event;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Color\RgbColor;
use PhpTui\Tui\Extension\Bdf\Shape\TextShape;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\CanvasWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\Paragraph;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\Block;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Position\FloatPosition;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\HorizontalAlignment;
use PhpTui\Tui\Widget\Widget;

final class DefinitionSlide implements Slide
{
    private bool $highlight = false;

    public function __construct(
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
                Constraint::percentage(40),
                Constraint::percentage(60),
            )
            ->widgets(
                BlockWidget::default()
                ->padding(Padding::fromScalars(1, 1, 1, 1))
                ->widget(
                    CanvasWidget::fromIntBounds(0, 200, 0, 50)
                            ->draw(
                                new TextShape(
                                    'default',
                                    $this->title(),
                                    AnsiColor::White,
                                    FloatPosition::at(15, 0),
                                    scaleX: 1.2,
                                    scaleY: 1.2,
                                ),
                            ),
                ),
                BlockWidget::default()
                ->padding(Padding::all(5))
                ->widget(
                    $this->text(),
                )
            );
    }

    public function handle(Tick|Event $event): void
    {
        if ($event instanceof CodedKeyEvent) {
            if ($event->code === KeyCode::Down || $event->code === KeyCode::Up) {
                $this->highlight = !$this->highlight;
            }
        }
    }

    private function text(): Widget
    {
        return ParagraphWidget::fromString(
            $this->text
        )->alignment(HorizontalAlignment::Center)->style(
            Style::default()->fg($this->highlight ? AnsiColor::White : RgbColor::fromRgb(100, 100, 100))
        );
    }
}
