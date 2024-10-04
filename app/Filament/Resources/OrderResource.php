<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Number;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Vendas';
    
    protected static ?string $modelLabel = 'Pedidos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Informações do Pedido')->schema([
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
                            -> columnSpanFull()
                    ]),

                    Section::make('Itens do Pedido')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([

                                Select::make('product_id')
                                    -> relationship('product','name')
                                    -> searchable()
                                    -> preload()
                                    -> required()
                                    -> distinct()
                                    -> disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    -> columnSpan(4)
                                    -> reactive()
                                    -> afterStateUpdated(fn ($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                    -> afterStateUpdated(fn ($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0)),

                                TextInput::make('quantity')
                                    -> label('Quantidade')
                                    -> numeric()
                                    -> required()
                                    -> default(1)
                                    -> minValue(1)
                                    -> columnSpan(2)
                                    -> reactive()
                                    -> afterStateUpdated(fn ($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount'))),

                                TextInput::make('unit_amount')
                                    -> label('Valor Unidade')
                                    -> numeric()
                                    -> required()
                                    -> default(1)
                                    -> minvalue(1)
                                    -> columnSpan(3),

                                TextInput::make('total_amount')
                                    -> label('Valor Total')
                                    -> numeric()
                                    -> required()
                                    -> columnSpan(3)

                            ])->columns(12),

                            Placeholder::make('grand_total_placeholder')
                            ->label('Valor Final')
                            ->content(function (Get $get, Set $set){
                                $total = 0;
                                if (!$repeaters = $get('items')) {
                                    return $total;
                                }

                                foreach ($repeaters as $key => $repeater) {
                                    $total += $get("items.{$key}.total_amount");
                                }

                                $set('grand_total', $total);
                                return Number::currency($total, 'BRL');
                            }),

                            Hidden::make('grand_total')
                            ->default(0)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    -> label('Cliente')
                    -> sortable()
                    -> searchable(),

                TextColumn::make('grand_total')
                    -> label('Valor Total')
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
