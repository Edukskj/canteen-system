<?php

namespace App\Filament\Resources\GuardianResource\RelationManagers;

use Filament\Forms;
use App\Models\Payment;
use Filament\Forms\Form;
use App\Filament\Resources\PaymentResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form;
            // ->schema([
            //     Forms\Components\TextInput::make('id')
            //         ->required()
            //         ->maxLength(255),
            // ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('guardian.name')
                    -> label('Aluno')
                    -> sortable()
                    -> searchable(),

                TextColumn::make('value')
                    -> label('Valor')
                    -> sortable()
                    -> money('BRL'),

                TextColumn::make('created_at')
                    -> label('Criado em')
                    -> sortable()
                    -> date('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('Visualizar Pedido')
                -> url(fn (Payment $record):string => PaymentResource::getUrl('view', ['record' => $record]))
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
}
