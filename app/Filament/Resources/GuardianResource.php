<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuardianResource\Pages;
use App\Filament\Resources\GuardianResource\RelationManagers;
use App\Filament\Resources\GuardianResource\RelationManagers\TransactionRelationManager;
use App\Models\Guardian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user';

    protected static ?string $navigationGroup = 'Clientes';

    protected static ?string $modelLabel = 'Responsáveis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Grid::make() ->schema([

                        Forms\Components\TextInput::make('name')
                            -> label('Nome')
                            -> required()
                            -> placeholder('Nome') 
                            -> validationAttribute('Nome')
                            -> rule('min:3'),
                            
                            Forms\Components\TextInput::make('email')
                            -> label('E-mail') 
                            -> required() 
                            -> placeholder('exemplo@hotmail.com')
                            -> email() 
                            -> validationAttribute('E-mail'),
                            
                        Forms\Components\TextInput::make('cpf')
                            -> label('CPF')
                            -> mask('999.999.999-99')
                            -> placeholder('000.000.000-00')
                            -> validationAttribute('CPF')
                            ->afterStateUpdated(function ($state, callable $set) {
                                $cleanedCpf = preg_replace('/\D/', '', $state);
                                $set('cpf', $cleanedCpf);
                            }),

                        Forms\Components\TextInput::make('phone')
                            -> label('Celular')
                            -> placeholder('(00) 0000-0000')
                            -> tel()
                            -> telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            -> mask('(99) 99999-9999')
                            -> validationAttribute('Celular')
                            ->afterStateUpdated(function ($state, callable $set) {
                                $cleanedPhone = preg_replace('/\D/', '', $state);
                                $set('phone', $cleanedPhone);
                            }),

                        Forms\Components\Select::make('students')
                            -> label('Alunos')
                            -> relationship('students','name')
                            -> multiple()
                            -> preload()
                            -> disabled()
                            -> hidden(fn (string $operation): bool => $operation === 'create'),

                            Forms\Components\TextInput::make('wallet')
                            -> label('Saldo')
                            -> validationAttribute('Saldo')
                            -> prefix('R$')
                            -> numeric()
                            -> hidden(fn (string $operation): bool => $operation === 'create'),

                        Forms\Components\Toggle::make('active')
                            -> label('Ativo')
                            -> default(true),

                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    -> label('Nome') 
                    -> searchable() 
                    -> sortable(),

                Tables\Columns\TextColumn::make('email') 
                    -> label('E-mail')
                    -> searchable() 
                    -> sortable(),

                Tables\Columns\TextColumn::make('phone') 
                    ->formatStateUsing(fn (string $state): string => 
                    '(' . substr($state, 0, 2) . ') ' . substr($state, 2, 5) . '-' . substr($state, 7))
                    -> label('Celular')
                    -> searchable() 
                    -> sortable(),

                Tables\Columns\TextColumn::make('wallet')
                    -> badge()
                    -> color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                    -> label('Saldo')
                    -> money('BRL')
                    -> searchable() 
                    -> sortable(),

                Tables\Columns\IconColumn::make('active')
                    -> label('Ativo')
                    -> boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('active') 
                    -> label('Status')
                    -> options([
                        true => 'Ativo',
                        false => 'Inativo'
                    ])
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TransactionRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuardians::route('/'),
            'create' => Pages\CreateGuardian::route('/create'),
            'view' => Pages\ViewGuardian::route('/{record}'),
            'edit' => Pages\EditGuardian::route('/{record}/edit'),
        ];
    }
}
