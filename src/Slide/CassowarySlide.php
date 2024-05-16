<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Slideshow\Widget\PhpCode;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\CanvasWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\Core\Widget\Paragraph;
use PhpTui\Tui\Extension\Bdf\Shape\TextShape;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\Block;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\Core\Widget\ItemList\ItemListState;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Position\FloatPosition;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;

final class CassowarySlide implements Slide
{
    private int $mainPercentage = 50;

    private int $headerLength = 2;

    /**
     * @var ItemList\ItemListState
     */
    private ListState $state;
    /**
     * @param array<int,mixed> $items
     */
    public function __construct(
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
                $this->cassowary(),
            );
    }

    public function handle(Tick|Event $event): void
    {
        if ($event instanceof CodedKeyEvent) {
            if ($event->code === KeyCode::Up) {
                $this->headerLength++;
            }
            if ($event->code === KeyCode::Down) {
                $this->headerLength--;
            }
        }
        if ($event instanceof CharKeyEvent) {
            if ($event->char === '+') {
                $this->mainPercentage += 10;
            }
            if ($event->char === '-') {
                $this->mainPercentage -= 10;
            }
        }
    }

    private function text(): Widget
    {
        return BlockWidget::default()->padding(Padding::fromScalars(5, 5, 5, 5))->widget(
            new PhpCode(sprintf(
                <<<'EOT'
                    Grid::default()
                        ->constraints(
                            Constraint::length(%d),
                            Constraint::min(2),
                            Constraint::length(%d),
                        )
                        ->widgets(
                            Grid::default()
                                ->direction(Direction::Horizontal)
                                ->constraints(
                                    Constraint::percentage(%d),
                                    Constraint::min(1)
                                )
                                ->widgets(
                                    Block::default()
                                        ->borders(Borders::ALL)->titles(
                                            Title::fromString('Main Content')
                                        )->widget(
                                            Paragraph::fromString(
                                                'use + and - to  adjust'
                                            ))
                                        )
                                    ,
                                    Block::default()
                                        ->borders(Borders::ALL)
                                        ->titles(
                                            Title::fromString('Sidebar')
                                        )
                                )
                        )
                    EOT
                ,
                $this->headerLength, $this->headerLength, $this->mainPercentage
            ))
        );
    }

    private function cassowary(): Widget
    {
        return GridWidget::default()
            ->constraints(
                Constraint::length($this->headerLength),
                Constraint::min(2),
                Constraint::length($this->headerLength),
            )
            ->widgets(
                BlockWidget::default()->borders(Borders::ALL)->titles(Title::fromString('Header')),
                GridWidget::default()
                    ->direction(Direction::Horizontal)
                    ->constraints(
                        Constraint::percentage($this->mainPercentage),
                        Constraint::min(1)
                    )
                    ->widgets(
                        BlockWidget::default()->borders(Borders::ALL)->titles(
                            Title::fromString('Main Content')
                        )->widget(
                            ParagraphWidget::fromString(
                                sprintf(
                                    '%s%% use + and - to  adjust',
                                    $this->mainPercentage
                                )
                            )
                        ),
                        BlockWidget::default()->borders(Borders::ALL)->titles(Title::fromString('Sidebar'))
                    ),
                BlockWidget::default()->borders(Borders::ALL)->titles(Title::fromString('Footer')),
            );
    }
}
