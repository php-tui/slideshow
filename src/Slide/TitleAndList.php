<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Term\Event;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Canvas\CanvasContext;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Color\RgbColor;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\CanvasWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ItemList;
use PhpTui\Tui\Extension\Core\Widget\ItemList\ListItem;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem as PhpTuiListItem;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\Core\Widget\Block;
use PhpTui\Tui\Extension\Bdf\Shape\TextShape;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Position\FloatPosition;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;

final class TitleAndList implements Slide
{
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
        private string $subTitle = '',
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
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::length(6),
                Constraint::length(3),
                Constraint::min(10),
            )
            ->widgets(
                CanvasWidget::fromIntBounds(0, 80, 0, 10)
                    ->paint(function (CanvasContext $context) {
                        $context->draw(new TextShape(
                            'default',
                            $this->title(),
                            AnsiColor::Cyan,
                            FloatPosition::at(0, 0),
                            scaleX: 1,
                            scaleY: 1,
                        ));
                    }),
                BlockWidget::default()->padding(Padding::all(1))->widget(ParagraphWidget::fromString($this->subTitle)),
                $this->text(),
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
        return Block::default()->padding(Padding::all(10))->widget(
            ItemList::default()
            ->select(0)
            ->highlightSymbol('')
            ->highlightStyle(Style::default()->fg(AnsiColor::White))
            ->state($this->state)
            ->items(...array_map(
                fn (string $item) => PhpTuiListItem::fromString($item)->style(
                    $this->state->selected < count($this->items) ?
                        Style::default()->fg(RgbColor::fromRgb(100, 100, 100)) :
                        Style::default()->fg(AnsiColor::White)
                ),
                $this->items
            ))
        );
    }
}
