<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsWidget extends BaseWidget
{

    use InteractsWithPageFilters;

    protected function getStats(): array
    {

        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];

        return [
            Stat::make('Novos usuários', 
                User::
                when($start, fn($query)=> $query->whereDate('created_at','>',$start))
                ->when($end, fn($query)=> $query->whereDate('created_at','<',$end))
                ->count()
                )
                -> description('Novos usuários cadastrados')
                -> descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                -> chart([15,25,15,8,17,5,25,14,26])
                -> color('success')
        ];
    }
}
