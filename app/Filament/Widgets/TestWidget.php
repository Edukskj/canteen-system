<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TestWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Alunos no Ano';

    protected function getData(): array
    {

        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];
        
        if (($start == null) && ($end == null)) {
            $data = Trend::model(Student::class)
            ->between(
                start: now()->subMonth(2),
                end: now(),
            )
            ->perMonth()
            ->count();
        } else {
            $data = Trend::model(Student::class)
            ->between(
                start: $start ? Carbon::parse($start) : now(),
                end: $end ? Carbon::parse($end) : now(),
            )
            ->perMonth()
            ->count();
        }

        //dd($data);

        return [
            'datasets' => [
                [
                    'label' => 'Alunos',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),  
                    'borderColor' => 'rgb(255, 99, 132)',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
