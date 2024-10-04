<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Pedidos', Order::query()->count()),
            Stat::make('Preços Médios', Number::currency(Order::query()->avg('grand_total') ?? 0, 'BRL'))
        ];
    }
}
