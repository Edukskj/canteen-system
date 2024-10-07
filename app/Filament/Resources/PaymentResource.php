<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $activeNavigationIcon = 'heroicon-s-banknotes';

    protected static ?string $navigationGroup = 'Pagamentos';

    protected static ?string $modelLabel = 'Entradas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Pagamento')->schema([

                    Select::make('user_id')
                        -> label('Usuário')
                        -> preload()
                        //-> relationship('user','name')
                        -> searchable()
                        -> options(User::where('active', True)->pluck('name', 'id')->toArray()) 
                        -> getSearchResultsUsing(fn (string $search): array => User::where('active', True)->where('name','like',"%{$search}%")->limit(5)->pluck('name', 'id')->toArray())
                        -> getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                        -> required(),

                    Textarea::make('notes')
                        -> label('Notas')
                        -> columnSpanFull(),

                    TextInput::make('value')
                        -> label('Valor')
                        -> numeric()
                        -> required()
                        -> default(1)
                        -> minvalue(1)
                        -> columnSpanFull(),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    -> label('Aluno')
                    -> sortable()
                    -> searchable(),

                TextColumn::make('value')
                    -> label('Valor')
                    -> sortable()
                    -> money('BRL'),

                TextColumn::make('created_at')
                    -> label('Criado em')
                    -> dateTime()
                    -> sortable()
                    -> toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    -> label('Atualizado em')
                    -> dateTime()
                    -> sortable()
                    -> toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}