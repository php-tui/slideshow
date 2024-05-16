<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Term\Event;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Canvas\Painter;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Color\RgbColor;
use PhpTui\Tui\Extension\Bdf\Shape\TextShape;
use PhpTui\Tui\Extension\Core\Shape\ClosureShape;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\CanvasWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ItemList;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\Core\Widget\Paragraph;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\Block;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\Core\Widget\ItemList\ItemListState;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Position\FloatPosition;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;

final class FontSlide implements Slide
{
    private int $mainPercentage = 50;

    private int $headerLength = 2;

    /**
     * @var ItemList\ItemListState
     */
    private ListState $state;

    public function __construct(
        private string $title,
        /**
         * @var string[]
         */
        private array $items,
        private string $subTitle,
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
                    ->constraints(
                        Constraint::percentage(10),
                        Constraint::percentage(10),
                        Constraint::percentage(80),
                    )
                    ->widgets(
                        CanvasWidget::fromIntBounds(0, 56, 0, 7)
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
                        BlockWidget::default()->padding(Padding::all(1))->widget(ParagraphWidget::fromString($this->subTitle)),
                        $this->text(),
                    )
                ),
                $this->diagram(),
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

    private function diagram(): Widget
    {
        return BlockWidget::default()->borders(Borders::ALL)->widget(
        CanvasWidget::fromIntBounds(0, 6, 0, 6)
            ->draw(new ClosureShape(function (Painter $painter): void {
                for ($x = 0; $x < 6; $x++) {
                    $painter->context->print($x, 0, Line::fromString((string)($x + 1)));
                }
                for ($y = 0; $y < 6; $y++) {
                    $painter->context->print(0, $y, Line::fromString((string)($y + 1)));
                }
            }))
            ->draw(new TextShape(
                'default',
                'e',
                AnsiColor::White,
                FloatPosition::at(-1, 0),
                scaleX: 1,
                scaleY: 1,
           ))
        );
    }
}
