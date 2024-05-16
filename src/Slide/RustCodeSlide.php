<?php

namespace PhpTui\Slideshow\Slide;

use PhpTui\Slideshow\Widget\PhpCode;
use PhpTui\Term\Event;
use PhpTui\Slideshow\Slide;
use PhpTui\Slideshow\Tick;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Extension\Bdf\Shape\TextShape;
use PhpTui\Tui\Extension\Core\Widget\Block;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Extension\Core\Widget\CanvasWidget;
use PhpTui\Tui\Extension\Core\Widget\Grid;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Position\FloatPosition;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;

class RustCodeSlide implements Slide
{
    public function title(): string
    {
        return 'Porting Rust';
    }

    public function build(): Widget
    {
        return GridWidget::default()
            ->constraints(Constraint::length(6), Constraint::min(2))
            ->widgets(
                CanvasWidget::fromIntBounds(0, 120, 0, 10)
                    ->draw(new TextShape(
                        'default',
                        'Porting Rust Code', AnsiColor::Cyan, FloatPosition::at(2,2)

                    )),
                GridWidget::default()
                    ->direction(Direction::Horizontal)
                    ->constraints(Constraint::percentage(50), Constraint::percentage(50))
                    ->widgets(
                        $this->code('Rust',
                            <<<'EOT'
                            let mut solver = Solver::new();
                            let inner = area.inner(&layout.margin);

                            let (area_start, area_end) = match layout.direction {
                                Direction::Horizontal => (f64::from(inner.x), f64::from(inner.right())),
                                Direction::Vertical => (f64::from(inner.y), f64::from(inner.bottom())),
                            };
                            let area_size = area_end - area_start;
                            EOT
                        ),
                        $this->code('PHP',
                            <<<'EOT'
                            $solver = Solver::new();
                            $inner = $area->inner($layout->margin);

                            [$areaStart, $areaEnd] = match ($layout->direction) {
                                Direction::Horizontal => [$inner->position->x, $inner->right()],
                                Direction::Vertical => [$inner->position->y, $inner->bottom()],
                            };

                            $areaSize = $areaEnd - $areaStart;
                            EOT
                        )
                    )
            );
    }

    public function handle(Tick|Event $event): void
    {
    }

    private function code(string $title, string $code): Widget
    {
        return BlockWidget::default()
            ->titles(Title::fromString($title))
            ->borders(Borders::ALL)
            ->widget(new PhpCode($code));
    }
}
