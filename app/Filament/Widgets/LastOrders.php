<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class LastOrders extends BaseWidget
{

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Últimas Vendas';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at','desc')
            ->columns([
                TextColumn::make('student.name')
                    -> label('Cliente')
                    -> sortable()
                    -> searchable(),
                    
                TextColumn::make('grand_total')
                    -> label('Valor Total')
                    -> sortable()
                    -> money('BRL'),

                TextColumn::make('period')
                    -> label('Período')
                    -> getStateUsing(function ($record) {
                        return match($record->period) {
                            'M' => 'Manhã',
                            'T' => 'Tarde',
                            'N' => 'Noite',
                            default => 'Não definido',
                        };
                    })
                    -> toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    -> label('Status')
                    -> getStateUsing(function ($record) {
                        return match($record->status) {
                            'P' => 'Pendente',
                            'E' => 'Pago',
                            'I' => 'Impresso',
                            default => 'Não definido',
                        };
                    })
                    -> badge()
                    -> color(function ($state) {
                        return match ($state) {
                            'Pendente' => 'info',
                            'Impresso' => 'warning',
                            'Pago' => 'success',
                            default => 'gray',
                        };
                    })
                    ->icon(function ($state) {
                        return match ($state) {
                            'Pendente' => 'heroicon-m-currency-dollar',
                            'Impresso' => 'heroicon-m-newspaper',
                            'Pago' => 'heroicon-m-check-badge',
                            default => 'heroicon-m-question-mark-circle'
                        };
                    }),

                TextColumn::make('delivery')
                    -> label('Entrega')
                    -> getStateUsing(function ($record) {
                        return match($record->delivery) {
                            'E' => 'Entregar',
                            'F' => 'Enviado',
                            'N' => 'Não Requisitado',
                            default => 'Não Requisitado',
                        };
                    })
                    -> badge()
                    -> color(function ($state) {
                        return match ($state) {
                            'Entregar' => 'info',
                            'Enviado' => 'success',
                            'Não Requisitado' => 'gray',
                            default => 'gray',
                        };
                    })
                    -> icon(function ($state) {
                        return match ($state) {
                            'Entregar' => 'heroicon-m-truck',
                            'Enviado' => 'heroicon-m-check-badge',
                            'Não Requisitado' => 'heroicon-m-x-circle',
                            default => 'heroicon-m-x-circle'
                        };
                    }),

                TextColumn::make('created_at')
                    -> label('Criado em')
                    -> sortable()
                    -> date('d/m/Y H:i')
                    -> visibleFrom('md'),

                TextColumn::make('updated_at')
                    -> label('Atualizado há')
                    -> dateTime()
                    -> sortable()
                    -> since()
                    -> toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
