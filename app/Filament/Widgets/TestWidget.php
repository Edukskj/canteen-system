<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TestWidget extends ChartWidget
{
    protected static ?int $sort = 1;

    use InteractsWithPageFilters;

    protected static ?string $heading = 'Vendas - Status Pagamento';

    protected function getData(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];

        // Define a data de início e fim
        $dateRangeStart = $start ? Carbon::parse($start) : now()->subMonth(5);
        $dateRangeEnd = $end ? Carbon::parse($end) : now();

        // Consulta para todos os pedidos no intervalo de datas, agrupando por mês
        $allOrders = Trend::model(Order::class)
            ->between(start: $dateRangeStart, end: $dateRangeEnd)
            ->perMonth()
            ->count();

        // Inicializa arrays para armazenar contagens de pedidos por status
        $paidCounts = [];
        $pendingCounts = [];

        // Loop pelos dados do Trend
        foreach ($allOrders as $value) {
            // Converte a data para um objeto Carbon
            $date = Carbon::parse($value->date);
            $month = $date->month;
            $year = $date->year;

            // Contagem de pedidos pagos e pendentes para o mês atual
            $paidCount = Order::where('status', 'E')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();

            $pendingCount = Order::where('status', 'P')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();

            // Adiciona os resultados aos arrays
            $paidCounts[] = $paidCount;
            $pendingCounts[] = $pendingCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pagas',
                    'data' => $paidCounts,
                    'borderColor' => 'rgb(224, 168, 94)',
                    'backgroundColor' => 'rgba(224, 168, 94, 0.6)', // Cor de fundo da barra
                ],
                [
                    'label' => 'Pendentes',
                    'data' => $pendingCounts,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)', // Cor de fundo da barra
                ],
            ],
            'labels' => $allOrders->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M Y')), // Formatando labels para mês e ano
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Muda o tipo de gráfico para 'bar'
    }
}
