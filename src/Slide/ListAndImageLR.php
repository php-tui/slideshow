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
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\CanvasWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\Block;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\ImageMagick\Widget\ImageWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Position\FloatPosition;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;

final class ListAndImageLR implements Slide
{
    /**
     * @var List\ListState
     */
    private ListState $state;

    public function __construct(
        private string $image,
        private string $title,
        /**
         * @var string[]
         */
        private array $items,
    ) {
        $this->state = new ListState();
    }
    public function title(): string
    {
        return $this->title;
    }

    public function build(): Widget
    {
        return GridWidget::default()
            ->direction(Direction::Horizontal)
            ->constraints(
                Constraint::percentage(50),
                Constraint::percentage(50),
            )
            ->widgets(
                BlockWidget::default()
                ->padding(Padding::fromScalars(1, 1, 1, 1))
                ->widget(
                    GridWidget::default()
                    ->direction(Direction::Vertical)
                    ->constraints(Constraint::percentage(10), Constraint::percentage(90))
                    ->widgets(
                        CanvasWidget::fromIntBounds(0, 56, 0, 6)
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
                        $this->text(),
                    )
                ),
                $this->me(),
            );
    }

    public function handle(Tick|Event $event): void
    {
        if ($event instanceof CodedKeyEvent) {
            if ($event->code === KeyCode::Up) {
                $this->state->selected--;
            }
            if ($event->code === KeyCode::Down) {
                if (null === $this->state->selected) {
                    $this->state->selected = 0;
                    return;
                }
                $this->state->selected++;
            }
        }
    }

    private function text(): Widget
    {
        return BlockWidget::default()->padding(Padding::fromScalars(5, 5, 5, 5))->widget(
            ListWidget::default()
            ->select(0)
            ->highlightSymbol('')
            ->highlightStyle(Style::default()->fg(AnsiColor::White))
            ->state($this->state)
            ->items(...array_map(
                fn (string $item) => ListItem::fromString($item)->style(
                    Style::default()->fg(RgbColor::fromRgb(100, 100, 100))
                ),
                $this->items
            ))
        );
    }

    private function me(): Widget
    {
        return new ImageWidget($this->image);
    }
}
