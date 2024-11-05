<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $defaultStart = Carbon::now()->subDays(30); // Últimos 30 dias
        $defaultEnd = Carbon::now();

        $start = !empty($this->filters['startDate']) ? Carbon::parse($this->filters['startDate']) : $defaultStart;
        $end = !empty($this->filters['endDate']) ? Carbon::parse($this->filters['endDate']) : $defaultEnd;

        $formatNumber = function (?int $number): string {
            $number = $number ?? 0;

            if ($number < 1000) {
                return (string) Number::format($number, 0);
            }

            if ($number < 1000000) {
                return Number::format($number / 1000, 2) . 'k';
            }

            return Number::format($number / 1000000, 2) . 'm';
        };

        // Receita dos últimos 30 dias
        $receitaAtual = Order::whereBetween('created_at', [$start, $end])
            ->where('status', 'E')
            ->sum('amount_paid') ?? 0;

        $receitaAnterior = 0;
        $description = '';
        $icon = '';
        $name = 'Receita no Período';
        $color = 'success';

        // Apenas calcula a variação se não houver filtros de data personalizados
        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            $receitaAnterior = Order::whereBetween('created_at', [$start->copy()->subDays(30), $end->copy()->subDays(30)])
                ->where('status', 'E')
                ->sum('amount_paid') ?? 0;

            if ($receitaAtual > $receitaAnterior) {
                $description = 'R$ ' . $formatNumber($receitaAtual - $receitaAnterior) . ' de aumento';
                $name = 'Receita - Últimos 30 Dias';
                $icon = 'heroicon-o-arrow-trending-up';
                $color = 'success';
            } elseif ($receitaAtual < $receitaAnterior) {
                $description = 'R$ ' . $formatNumber($receitaAnterior - $receitaAtual) . ' de queda';
                $name = 'Receita - Últimos 30 Dias';
                $icon = 'heroicon-o-arrow-trending-down';
                $color = 'danger';
            } else {
                $name = 'Receita - Últimos 30 Dias';
                $description = 'Sem variação';
                $icon = 'heroicon-o-minus';
                $color = 'gray';
            }
        }

        // Contagem de vendas dos últimos 30 dias
        $vendasAtual = Order::whereBetween('created_at', [$start, $end])
            ->where('status', 'E')
            ->count();

        $vendasAnterior = 0;
        $description2 = '';
        $icon2 = '';
        $color2 = 'success';

        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            $vendasAnterior = Order::whereBetween('created_at', [$start->copy()->subDays(30), $end->copy()->subDays(30)])
                ->where('status', 'E')
                ->count();

            if ($vendasAtual > $vendasAnterior) {
                $description2 = 'R$ ' . $formatNumber($vendasAtual - $vendasAnterior) . ' de aumento';
                $icon2 = 'heroicon-o-arrow-trending-up';
                $color2 = 'success';
            } elseif ($vendasAtual < $vendasAnterior) {
                $description2 = 'R$ ' . $formatNumber($vendasAnterior - $vendasAtual) . ' de queda';
                $icon2 = 'heroicon-o-arrow-trending-down';
                $color2 = 'danger';
            } else {
                $description2 = 'Sem variação';
                $icon2 = 'heroicon-o-minus';
                $color2 = 'gray';
            }
        }

        // Receita pendente dos últimos 30 dias
        $pendenteAtual = Order::whereBetween('created_at', [$start, $end])
            ->where('status', 'P')
            ->select(DB::raw('SUM(grand_total - amount_paid) as total'))
            ->value('total') ?? 0;

        $pendenteAnterior = 0;
        $description3 = '';
        $icon3 = '';
        $color3 = 'success';

        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            $pendenteAnterior = Order::whereBetween('created_at', [$start->copy()->subDays(30), $end->copy()->subDays(30)])
                ->where('status', 'P')
                ->select(DB::raw('SUM(grand_total - amount_paid) as total'))
                ->value('total') ?? 0;

            if ($pendenteAtual > $pendenteAnterior) {
                $description3 = 'R$ ' . $formatNumber($pendenteAtual - $pendenteAnterior) . ' de aumento';
                $icon3 = 'heroicon-o-arrow-trending-up';
                $color3 = 'success';
            } elseif ($pendenteAtual < $pendenteAnterior) {
                $description3 = 'R$ ' . $formatNumber($pendenteAnterior - $pendenteAtual) . ' de queda';
                $icon3 = 'heroicon-o-arrow-trending-down';
                $color3 = 'danger';
            } else {
                $description3 = 'Sem variação';
                $icon3 = 'heroicon-o-minus';
                $color3 = 'gray';
            }
        }

        return [
            Stat::make($name, 'R$ ' . $formatNumber($receitaAtual))
                ->description($description)
                ->descriptionIcon($icon)
                ->chart([15, 25, 15, 8, 17, 5, 25, 14, 26])
                ->color($color),

            Stat::make('Vendas Realizadas', $formatNumber($vendasAtual))
                ->description($description2)
                ->descriptionIcon($icon2)
                ->chart([15, 25, 15, 8, 17, 5, 25, 14, 26])
                ->color($color2),

            Stat::make('Receita Pendente', 'R$ ' . $formatNumber($pendenteAtual))
                ->description($description3)
                ->descriptionIcon($icon3)
                ->chart([15, 25, 15, 8, 17, 5, 25, 14, 26])
                ->color($color3),
        ];
    }
}
