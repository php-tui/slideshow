<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Term\Event;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Extension\Bdf\Shape\TextShape;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\ItemList;
use PhpTui\Tui\Extension\Core\Widget\ItemList\ListItem;
use PhpTui\Tui\Extension\Core\Widget\Block;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\Core\Widget\ItemList\ItemListState;
use PhpTui\Tui\Extension\ImageMagick\Widget\ImageWidget;
use PhpTui\Tui\Model\AnsiColor;
use PhpTui\Tui\Model\Constraint;
use PhpTui\Tui\Model\Direction;
use PhpTui\Tui\Model\Marker;
use PhpTui\Tui\Model\RgbColor;
use PhpTui\Tui\Model\Style;
use PhpTui\Tui\Model\Widget;
use PhpTui\Tui\Model\Widget\FloatPosition;

final class ListAndImageLR implements Slide
{
    /**
     * @var ItemList\ItemListState
     */
    private ItemListState $state;

    public function __construct(
        private string $image,
        private string $title,
        /**
         * @var string[]
         */
        private array $items,
    ) {
        $this->state = new ItemListState();
    }
    public function title(): string
    {
        return $this->title;
    }

    public function build(): Widget
    {
        return Grid::default()
            ->direction(Direction::Horizontal)
            ->constraints(
                Constraint::percentage(50),
                Constraint::percentage(50),
            )
            ->widgets(
                Block::default()
                ->padding(Padding::fromScalars(1, 1, 1, 1))
                ->widget(
                    Grid::default()
                    ->direction(Direction::Vertical)
                    ->constraints(Constraint::percentage(10), Constraint::percentage(90))
                    ->widgets(
                        Canvas::fromIntBounds(0, 56, 0, 6)
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
        return Block::default()->padding(Padding::fromScalars(5, 5, 5, 5))->widget(
            ItemList::default()
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
