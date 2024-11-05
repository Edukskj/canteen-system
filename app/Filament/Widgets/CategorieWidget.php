<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CategorieWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Pedidos por Categorias - Últimos 30 dias';

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [2, 10, 5],
                    'backgroundColor' => [
                        'rgb(255,99,132)',
                        'rgb(54,162,235)',
                        'rgb(255,205,86)'
                    ]
                ],
            ],
            'labels' => ['Bedidas', 'Salgados', 'Balas'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
