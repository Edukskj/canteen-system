<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\TextInput::make('id')
        //             ->required()
        //             ->maxLength(255),
        //     ]);
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                -> label('ID do Pedido')
                -> searchable(),
                
                Tables\Columns\TextColumn::make('grand_total')
                -> label('Valor Total')
                -> money('BRL')
                -> searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                -> label('Data de criação')
                
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('Visualizar Pedido')
                -> url(fn (Order $record):string => OrderResource::getUrl('view', ['record' => $record]))
                -> color('info')
                -> icon('heroicon-o-eye'),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {   
        // Verifica se o relacionamento possui registros
        return  $ownerRecord->orders()->exists(); 
    }

};
