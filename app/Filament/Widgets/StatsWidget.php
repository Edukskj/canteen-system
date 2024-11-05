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

        $start = $this->filters['startDate'] ?? $defaultStart;
        $end = $this->filters['endDate'] ?? $defaultEnd;


        $formatNumber = function (int $number): string {
            if ($number < 1000) {
                return (string) Number::format($number, 0);
            }

            if ($number < 1000000) {
                return Number::format($number / 1000, 2) . 'k';
            }

            return Number::format($number / 1000000, 2) . 'm';
        };

        $receitaMensalAtual = Order::
        when($start, fn($query) => $query->whereDate('created_at', '>', $start))
        ->when($end, fn($query) => $query->whereDate('created_at', '<', $end))
        ->where('status', 'E')
        ->sum('amount_paid');

        $receitaMensalPassada = 0;
        if (empty($this->filters['startDate']) && empty($this->filters['endDate'])) {
            $startPassado = Carbon::now()->subMonth()->startOfMonth();
            $endPassado = Carbon::now()->subMonth()->endOfMonth();

            $receitaMensalPassada = Order::
                whereDate('created_at', '>=', $startPassado)
                ->whereDate('created_at', '<=', $endPassado)
                ->where('status', 'E')
                ->sum('amount_paid');
        }


        $description = '';
        $icon = 'heroicon-o-arrow-trending-up';
        $color = 'success';

        if ($receitaMensalAtual > $receitaMensalPassada) {
            $description = 'R$ ' . $formatNumber($receitaMensalAtual - $receitaMensalPassada) . ' de aumento';
        } elseif ($receitaMensalAtual < $receitaMensalPassada) {
            $description = 'R$ ' . $formatNumber($receitaMensalPassada - $receitaMensalAtual) . ' de baixa';
            $icon = 'heroicon-o-arrow-trending-down';
            $color = 'danger';
        } else {
            $description = 'Sem variação';
        }

        return [
            Stat::make('Receita Mensal', 'R$ ' . $formatNumber($receitaMensalAtual))
                -> description($description)
                -> descriptionIcon($icon)
                -> chart([15,25,15,8,17,5,25,14,26])
                -> color($color),
            
            Stat::make('Vendas Realizadas', 
                Order::
                when($start, fn($query)=> $query->whereDate('created_at','>',$start))
                ->when($end, fn($query)=> $query->whereDate('created_at','<',$end))
                ->where('status', 'E')
                ->count()
                )
                -> description('Novos usuários cadastrados')
                -> descriptionIcon('heroicon-m-currency-dollar', IconPosition::Before)
                -> chart([15,25,15,8,17,5,25,14,26])
                -> color('success'),
            
            Stat::make('Receita Pendente', 'R$ ' . $formatNumber( 
                Order::
                when($start, fn($query)=> $query->whereDate('created_at','>',$start))
                ->when($end, fn($query)=> $query->whereDate('created_at','<',$end))
                ->where('status', 'P')
                ->select(DB::raw('SUM(grand_total - amount_paid) as total'))
                ->value('total'))
                )
                -> description('Novos usuários cadastrados')
                -> descriptionIcon('heroicon-m-shopping-bag', IconPosition::Before)
                -> chart([15,25,15,8,17,5,25,14,26])
                -> color('success'),
        ];
    }
}
