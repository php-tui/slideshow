<?php

namespace PhpTui\Slideshow;

use PhpTui\Term\Actions;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\Event\MouseEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Term\MouseButton;
use PhpTui\Term\MouseEventKind;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Display\Display;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\Paragraph;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\HorizontalAlignment;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Throwable;

class App
{
    private int $selected = 0;

    /**
     * @param Slide[] $slides
     */
    public function __construct(
        private Terminal $terminal,
        private Display $display,
        private array $slides
    ) {
    }
    public function run(): void
    {
        try {
            // enable "raw" mode to remove default terminal behavior (e.g.
            // echoing key presses)
            $this->terminal->enableRawMode();
            $this->doRun();
        } catch (Throwable $err) {
            $this->terminal->disableRawMode();
            $this->terminal->execute(Actions::alternateScreenDisable());
            throw $err;
        }
    }
    private function doRun(): void
    {
        $this->terminal->enableRawMode();
        $this->terminal->execute(Actions::cursorHide());
        $this->terminal->execute(Actions::alternateScreenEnable());
        $this->terminal->execute(Actions::enableMouseCapture());

        while (true) {
            $this->currentSlide()->handle(new Tick());
            while (null !== $event = $this->terminal->events()->next()) {
                if ($event instanceof MouseEvent) {
                    if ($event->kind === MouseEventKind::Down) {
                        if ($event->button === MouseButton::Left) {
                            $this->nextSlide();
                        }
                        if ($event->button === MouseButton::Right) {
                            $this->previousSlide();
                        }
                    }
                }
                if ($event instanceof CodedKeyEvent) {
                    if ($event->code === KeyCode::Left) {
                        $this->previousSlide();
                    }
                    if ($event->code === KeyCode::Right) {
                        $this->nextSlide();
                    }
                    if ($event->code === KeyCode::Esc) {
                        break 2;
                    }
                }
                $this->currentSlide()->handle($event);
            }
            $this->display->draw(
                GridWidget::default()
                    ->constraints(
                        Constraint::min(10),
                        Constraint::max(1),
                    )
                    ->widgets(
                        $this->currentSlide()->build(),
                        $this->footer(),
                    )
            );

            usleep(10_000);
        }

        $this->terminal->disableRawMode();
        $this->terminal->execute(Actions::cursorShow());
        $this->terminal->execute(Actions::alternateScreenDisable());
    }

    private function currentSlide(): Slide
    {
        return $this->slides[$this->selected];
    }

    private function footer(): Widget
    {
        return GridWidget::default()
            ->direction(Direction::Horizontal)
            ->constraints(
                Constraint::percentage(50),
                Constraint::percentage(50),
            )->widgets(
                ParagraphWidget::fromString(sprintf(
                    '%s/%s',
                    $this->selected + 1,
                    count($this->slides),
                )),
                ParagraphWidget::fromString(sprintf(
                    '%s',
                    $this->currentSlide()->title(),
                ))->alignment(HorizontalAlignment::Right),
            );
    }

    private function previousSlide(): mixed
    {
        return $this->selected = max(0, $this->selected - 1);
    }

    private function nextSlide(): mixed
    {
        return $this->selected = min(count($this->slides) - 1, $this->selected + 1);
    }
}
