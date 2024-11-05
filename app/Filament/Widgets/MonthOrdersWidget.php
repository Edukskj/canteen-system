<?php

namespace App\Filament\Widgets;


use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\TrendValue;
use Flowframe\Trend\Trend;

class MonthOrdersWidget extends ChartWidget
{

    protected static ?int $sort = 2;

    use InteractsWithPageFilters;

    protected static ?string $heading = 'Vendas Diárias - Últimos 10 Dias';

    protected function getData(): array
    {

        $start = now()->subDays(10);
        $end = now();

        $dailyCounts = [];
        $dates = [];

        foreach (new \DatePeriod($start, \DateInterval::createFromDateString('1 day'), $end->addDay()) as $day) {
            $count = Order::whereDate('created_at', $day->format('Y-m-d'))->count();
            $dailyCounts[] = $count;
            $dates[] = $day->format('d-m');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total de Vendas',
                    'data' => $dailyCounts,
                    'borderColor' => 'rgb(191, 181, 170)',
                    'backgroundColor' => 'rgba(191, 181, 170, 0.2)',
                    'fill' => true,
                    'hoverBorderColor' => 'rgb(191, 181, 170)',
                    'hoverBackgroundColor' => 'rgba(191, 181, 170, 0.5)',
                    'pointBackgroundColor' => 'rgba(191, 181, 170, 0.5)',
                ],
            ],
            'labels' => $dates, // Datas para os rótulos do eixo x
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Tipo de gráfico
    }
}
