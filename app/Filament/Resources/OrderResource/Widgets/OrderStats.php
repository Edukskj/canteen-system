<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Carbon\Carbon;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {

        $start = Carbon::now()->subDays(30);
        $end = Carbon::now();

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
        
        $avaragePrice = Order::whereBetween('created_at', [$start, $end])->avg('grand_total') ?? 0;

        return [
            Stat::make('Total de Pedidos - 30 dias', Order::whereBetween('created_at', [$start, $end])->count())
            ->chart([15, 25, 15, 8, 17, 5, 25, 14, 26])
            ->color('gray'),
            Stat::make('Preços Médios - 30 dias',  Number::currency($formatNumber($avaragePrice), 'BRL'))
        ];
    }
}
