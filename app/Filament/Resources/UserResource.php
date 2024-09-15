<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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
                            ->rule('min:3'),

                        Forms\Components\TextInput::make('email')
                            -> label('E-mail') 
                            -> required() 
                            -> placeholder('exemplo@hotmail.com')
                            ->email() 
                            -> validationAttribute('E-mail'),

                        Forms\Components\TextInput::make('password')
                            -> label('Senha') 
                            -> required(fn(string $context):bool=>$context=='create')
                            -> password()
                            -> confirmed(),

                        Forms\Components\TextInput::make('password_confirmation')
                            -> label('Confirmação de Senha') 
                            -> required(fn(string $context):bool=>$context=='create')
                            -> password() 
                            -> validationAttribute('Confirmação de Senha'),

                        Forms\Components\Toggle::make('active')
                            -> label('Ativo'),

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

                Tables\Columns\IconColumn::make('active')
                    -> label('Ativo')
                    ->boolean(),

            ])
            ->filters([

                Tables\Filters\SelectFilter::make('active') 
                    -> options([
                        true => 'Ativo',
                        false => 'Inativo'
                    ])
                    -> default('true')

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
