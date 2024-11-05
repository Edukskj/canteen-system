<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class
        ];
    }

    public function getTabs(): array {
        return [
            null => Tab::make('Todos'),
            'À Entregar' => Tab::make()->query(fn ($query) => $query->where('delivery', 'E')->where('status', '!=', 'E')),
            'Pendentes' => Tab::make()->query(fn ($query) => $query->where('status', 'P')),
            'Pagos' => Tab::make()->query(fn ($query) => $query->where('status', 'E'))
        ];
    }
}
