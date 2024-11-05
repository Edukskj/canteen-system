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
        $defaultStart = Carbon::now()->startOfMonth();
        $defaultEnd = Carbon::now()->endOfMonth();

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

        $receitaMensalAtual = 0;
        $receitaMensalAtual = Order::
            when($start, fn($query) => $query->whereDate('created_at', '>=', $start))
            ->when($end, fn($query) => $query->whereDate('created_at', '<=', $end))
            ->where('status', 'E')
            ->sum('amount_paid') ?? 0;

        $receitaMensalPassada = 0;
        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            $startPassado = Carbon::now()->subMonth()->startOfMonth();
            $endPassado = Carbon::now()->subMonth()->endOfMonth();

            $receitaMensalPassada = Order::
                whereDate('created_at', '>=', $startPassado)
                ->whereDate('created_at', '<=', $endPassado)
                ->where('status', 'E')
                ->sum('amount_paid') ?? 0;
        }

        $description = '';
        $icon = '';
        $color = 'success';

        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            if ($receitaMensalAtual > $receitaMensalPassada) {
                $description = 'R$ ' . $formatNumber($receitaMensalAtual - $receitaMensalPassada) . ' de aumento';
                $icon = 'heroicon-o-arrow-trending-up';
                $color = 'success';
            } elseif ($receitaMensalAtual < $receitaMensalPassada) {
                $description = 'R$ ' . $formatNumber($receitaMensalPassada - $receitaMensalAtual) . ' de queda';
                $icon = 'heroicon-o-arrow-trending-down';
                $color = 'danger';
            } else {
                $description = 'Sem variação';
                $icon = 'heroicon-o-minus';
                $color = 'gray';
            }
        }

        // Estatísticas de Vendas
        $vendasAtual = Order::
            when($start, fn($query) => $query->whereDate('created_at', '>=', $start))
            ->when($end, fn($query) => $query->whereDate('created_at', '<=', $end))
            ->where('status', 'E')
            ->count();

        $vendasPassada = 0;
        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            $startPassado = Carbon::now()->subMonth()->startOfMonth();
            $endPassado = Carbon::now()->subMonth()->endOfMonth();

            $vendasPassada = Order::
                whereDate('created_at', '>=', $startPassado)
                ->whereDate('created_at', '<=', $endPassado)
                ->where('status', 'E')
                ->count();
        }

        $description2 = '';
        $icon2 = '';
        $color2 = 'success';

        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            if ($vendasAtual > $vendasPassada) {
                $description2 = 'R$ ' . $formatNumber($vendasAtual - $vendasPassada) . ' de aumento';
                $icon2 = 'heroicon-o-arrow-trending-up';
                $color2 = 'success';
            } elseif ($vendasAtual < $vendasPassada) {
                $description2 = 'R$ ' . $formatNumber($vendasPassada - $vendasAtual) . ' de queda';
                $icon2 = 'heroicon-o-arrow-trending-down';
                $color2 = 'danger';
            } else {
                $description2 = 'Sem variação';
                $icon2 = 'heroicon-o-minus';
                $color2 = 'gray';
            }
        }

        // Estatísticas de Pendentes
        $pendenteAtual = Order::
            when($start, fn($query) => $query->whereDate('created_at', '>=', $start))
            ->when($end, fn($query) => $query->whereDate('created_at', '<=', $end))
            ->where('status', 'P')
            ->select(DB::raw('SUM(grand_total - amount_paid) as total'))
            ->value('total') ?? 0;

        $pendentePassada = 0;
        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            $startPassado = Carbon::now()->subMonth()->startOfMonth();
            $endPassado = Carbon::now()->subMonth()->endOfMonth();

            $pendentePassada = Order::
                whereDate('created_at', '>=', $startPassado)
                ->whereDate('created_at', '<=', $endPassado)
                ->where('status', 'P')
                ->select(DB::raw('SUM(grand_total - amount_paid) as total'))
                ->value('total') ?? 0;
        }

        $description3 = '';
        $icon3 = '';
        $color3 = 'success';

        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            if ($pendenteAtual > $pendentePassada) {
                $description3 = 'R$ ' . $formatNumber($pendenteAtual - $pendentePassada) . ' de aumento';
                $icon3 = 'heroicon-o-arrow-trending-up';
                $color3 = 'success';
            } elseif ($pendenteAtual < $pendentePassada) {
                $description3 = 'R$ ' . $formatNumber($pendentePassada - $pendenteAtual) . ' de queda';
                $icon3 = 'heroicon-o-arrow-trending-down';
                $color3 = 'danger';
            } else {
                $description3 = 'Sem variação';
                $icon3 = 'heroicon-o-minus';
                $color3 = 'gray';
            }
        }

        return [
            Stat::make('Receita Mensal', 'R$ ' . $formatNumber($receitaMensalAtual))
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
