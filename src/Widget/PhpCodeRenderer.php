<?php

namespace PhpTui\Slideshow\Widget;

use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Color\RgbColor;
use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Position\Position;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Span;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

class PhpCodeRenderer implements WidgetRenderer
{
    /**
     * @var array<string,AnsiColor>
     */
    private array $colors;

    public function __construct()
    {
        $this->colors = [
            'T_CONSTANT_ENCAPSED_STRING' => RgbColor::fromRgb(200, 200, 255),
            'T_STRING' => RgbColor::fromRgb(200, 180, 200),
            'T_DOUBLE_COLON' => AnsiColor::LightBlue,
            'T_OBJECT_OPERATOR' => AnsiColor::Red,
            'T_LNUMBER' => RgbColor::fromHsv(42, 81, 100),
            'T_DEFAULT' => RgbColor::fromRgb(200, 180, 200),
            'T_WHITESPACE' => AnsiColor::Reset,
        ];
    }
    public function render(WidgetRenderer $renderer, Widget $widget, Buffer $buffer, Area $area): void
    {
        if (!$widget instanceof PhpCode) {
            return;
        }
        $spans = [];
        $x = 0;
        $y=  0;
        $newLine = false;
        $extraSpace = '';
        $position = Position::at($x, $y);
        $lines = explode("\n", $widget->code);
        foreach ($lines as $line) {
            foreach (token_get_all('<?php ' . $line) as $token) {
                if (is_int($token[0])) {
                    if (token_name($token[0]) === 'T_OPEN_TAG') {
                        continue;
                    }
                    $span = Span::fromString(
                        $token[1]
                    )->style(
                        Style::default()->fg($this->colors[token_name($token[0])]??AnsiColor::Reset)
                    );
                    $width = mb_strlen($token[1]);
                } else {
                    $span = Span::fromString($token)->style(Style::default()->fg(AnsiColor::LightBlue));
                    $width = mb_strlen($token);
                }
                $position = $position->withX($area->left() + $x);
                $position = $position->withY($area->top() + $y);
                $buffer->putSpan($position, $span, PHP_INT_MAX);
                $x += $width;
            }
            $y++;
            $x = 0;
        }
    }
}
