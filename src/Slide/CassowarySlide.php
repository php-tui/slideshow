<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Slideshow\Widget\PhpCode;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Tui\Adapter\Bdf\FontRegistry;
use PhpTui\Tui\Adapter\Bdf\Shape\TextShape;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Model\AnsiColor;
use PhpTui\Tui\Model\Constraint;
use PhpTui\Tui\Model\Direction;
use PhpTui\Tui\Model\Widget;
use PhpTui\Tui\Model\Widget\Borders;
use PhpTui\Tui\Model\Widget\FloatPosition;
use PhpTui\Tui\Model\Widget\Title;
use PhpTui\Tui\Widget\Block;
use PhpTui\Tui\Widget\Block\Padding;
use PhpTui\Tui\Widget\Canvas;
use PhpTui\Tui\Widget\Grid;
use PhpTui\Tui\Widget\ItemList;
use PhpTui\Tui\Widget\ItemList\ItemListState;
use PhpTui\Tui\Widget\Paragraph;

final class CassowarySlide implements Slide
{
    private int $mainPercentage = 50;

    private int $headerLength = 2;

    /**
     * @var ItemList\ItemListState
     */
    private ItemListState $state;

    public function __construct(
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
        return Block::default()->padding(Padding::fromScalars(5, 5, 5, 5))->widget(
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
        return Grid::default()
            ->constraints(
                Constraint::length($this->headerLength),
                Constraint::min(2),
                Constraint::length($this->headerLength),
            )
            ->widgets(
                Block::default()->borders(Borders::ALL)->titles(Title::fromString('Header')),
                Grid::default()
                    ->direction(Direction::Horizontal)
                    ->constraints(
                        Constraint::percentage($this->mainPercentage),
                        Constraint::min(1)
                    )
                    ->widgets(
                        Block::default()->borders(Borders::ALL)->titles(
                            Title::fromString('Main Content')
                        )->widget(
                            Paragraph::fromString(
                                sprintf(
                                    '%s%% use + and - to  adjust',
                                    $this->mainPercentage
                                )
                            )
                        ),
                        Block::default()->borders(Borders::ALL)->titles(Title::fromString('Sidebar'))
                    ),
                Block::default()->borders(Borders::ALL)->titles(Title::fromString('Footer')),
            );
    }
}
