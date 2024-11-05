<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Student;
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
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Actions\Action;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\BulkAction;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $activeNavigationIcon = 'heroicon-s-shopping-bag';

    protected static ?string $navigationGroup = 'Vendas';
    
    protected static ?string $modelLabel = 'Pedidos';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Informações do Cliente')
                    ->schema([
                        Forms\Components\Grid::make(12)
                            ->schema([
                                TextInput::make('rm')
                                    ->label('RM')
                                    ->columnSpan(['md' => 2])
                                    ->reactive()
                                    ->debounce(500)
                                    ->dehydrateStateUsing(fn ($state) => null)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $student = Student::where('rm', $state)->first();
                                        if ($student) {
                                            $set('student_id', $student->id);
                                            $set('guardian', optional($student->guardian)->name);
                                            $set('period', $student->period);
                                            $set('teacher', $student->teacher);
                                            $set('grade', $student->grade);
                                            $set('observation', $student->observation);
                                            // Define showAdditionalFields based on infantil value from student record
                                            $set('showAdditionalFields', $student->infantil);
                                        } else {
                                            $set('student_id', null);
                                            $set('guardian', null);
                                            $set('period', null);
                                            $set('teacher', null);
                                            $set('grade', null);
                                            $set('observation', null);
                                            $set('showAdditionalFields', false);
                                        }
                                    })
                                    ->afterStateHydrated(function ($state, callable $set) {
                                        $student = Student::where('rm', $state)->first();
                                        if ($student) {
                                            $set('student_id', $student->id);
                                            $set('guardian', optional($student->guardian)->name);
                                            $set('period', $student->period);
                                            $set('teacher', $student->teacher);
                                            $set('grade', $student->grade);
                                            $set('observation', $student->observation);
                                            $set('showAdditionalFields', $student->infantil);
                                        } else {
                                            $set('student_id', null);
                                            $set('guardian', null);
                                            $set('period', null);
                                            $set('teacher', null);
                                            $set('grade', null);
                                            $set('observation', null);
                                            $set('showAdditionalFields', false);
                                        }
                                    }),

                                Select::make('student_id')
                                    ->label('Aluno')
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(['md' => 4])
                                    ->relationship('student', 'name', function ($query) {
                                        $query->where('active', true)
                                            ->select('id', 'name');
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $student = Student::find($state);
                                        if ($student) {
                                            $set('rm', $student->rm);
                                            $set('guardian', optional($student->guardian)->name);
                                            $set('period', $student->period);
                                            $set('teacher', $student->teacher);
                                            $set('grade', $student->grade);
                                            $set('observation', $student->observation);
                                            $set('showAdditionalFields', $student->infantil);
                                        } else {
                                            $set('rm', null);
                                            $set('guardian', null);
                                            $set('period', null);
                                            $set('teacher', null);
                                            $set('grade', null);
                                            $set('observation', null);
                                            $set('showAdditionalFields', false);
                                        }
                                    })
                                    ->afterStateHydrated(function ($state, callable $set) {
                                        $student = Student::find($state);
                                        if ($student) {
                                            $set('rm', $student->rm);
                                            $set('guardian', optional($student->guardian)->name);
                                            $set('period', $student->period);
                                            $set('teacher', $student->teacher);
                                            $set('grade', $student->grade);
                                            $set('observation', $student->observation);
                                            $set('showAdditionalFields', $student->infantil);
                                        } else {
                                            $set('rm', null);
                                            $set('guardian', null);
                                            $set('period', null);
                                            $set('teacher', null);
                                            $set('grade', null);
                                            $set('observation', null);
                                            $set('showAdditionalFields', false);
                                        }
                                    }),

                                TextInput::make('guardian')
                                    ->label('Responsável')
                                    ->columnSpan(['md' => 3])
                                    ->disabled()
                                    ->dehydrateStateUsing(fn ($state) => null),

                                Select::make('period')
                                    ->label('Período')
                                    ->columnSpan(['md' => 3])
                                    ->options([
                                        'M' => 'Manhã',
                                        'T' => 'Tarde',
                                        'N' => 'Noite'
                                    ]),

                                TextInput::make('teacher')
                                    ->label('Professora')
                                    ->columnSpan(['md' => 6])
                                    ->disabled()
                                    ->visible(fn (callable $get) => $get('showAdditionalFields') === true),

                                TextInput::make('grade')
                                    ->label('Série')
                                    ->columnSpan(['md' => 6])
                                    ->disabled()
                                    ->visible(fn (callable $get) => $get('showAdditionalFields') === true),

                                Textarea::make('observation')
                                    ->label('Observação')
                                    ->columnSpan(['md' => 12])
                                    ->disabled()
                            ])
                ]),


                Section::make('Informações do Pedido')->schema([
                    Forms\Components\Grid::make(12) ->schema([
                        Textarea::make('notes')
                            ->columnSpan(['md' => 5])
                            ->label('Notas'),

                        Hidden::make('grand_total')
                            ->default(0)
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // Quando o grand_total é atualizado, verifica o status
                                if ($get('status') === 'E') {
                                    $set('amount_paid', $state); // Atualiza amount_paid com o valor do grand_total
                                }
                            }),

                        ToggleButtons::make('status')
                            ->label('Status')
                            ->inline()
                            ->columnSpan(['md' => 3])
                            ->default('P')
                            ->options([
                                'P' => 'Pendente',
                                'E' => 'Pago',
                            ])
                            ->colors([
                                'P' => 'info',
                                'E' => 'success',
                            ])
                            -> icons([
                                'P' => 'heroicon-m-currency-dollar',
                                'E' => 'heroicon-m-check-badge',
                            ])
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // Se o status é alterado para 'E', atualiza amount_paid com o valor de grand_total
                                if ($state === 'E') {
                                    $set('amount_paid', $get('grand_total'));
                                }
                            }),

                        ToggleButtons::make('delivery')
                            ->label('Entrega')
                            ->columnSpan(['md' => 4])
                            ->default('N')
                            ->inline()
                            ->options([
                                'E' => 'Entregar',
                                'F' => 'Enviado',
                                'N' => 'Não Requisitado',
                            ])
                            ->colors([
                                'E' => 'info',
                                'F' => 'success',
                                'N' => 'gray',
                            ])
                            ->icons([
                                'E' => 'heroicon-m-truck',
                                'F' => 'heroicon-m-check-badge',
                                'N' => 'heroicon-m-x-circle',
                            ])
                            ->visible(fn (callable $get) => $get('showAdditionalFields') === true),

                    ]),
                ]),

                Hidden::make('amount_paid')
                ->default(0),

                Section::make('Itens do Pedido')->schema([
                    Repeater::make('items')
                        ->relationship()
                        ->label("Itens")
                        ->schema([

                            Select::make('product_id')
                                -> label('Produto')
                                -> relationship('product','name', function ($query) {
                                    $query->where('active',true);
                                })
                                -> searchable()
                                -> preload()
                                -> required()
                                -> distinct()
                                -> disableOptionsWhenSelectedInSiblingRepeaterItems()
                                -> columnSpan(['md' => 4])
                                -> reactive()
                                -> afterStateUpdated(fn ($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                -> afterStateHydrated(fn ($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                -> afterStateUpdated(fn ($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0)),

                            TextInput::make('quantity')
                                -> label('Quantidade')
                                -> numeric()
                                -> required()
                                -> default(1)
                                -> minValue(1)
                                -> columnSpan(['md' => 2])
                                -> reactive()
                                -> afterStateUpdated(fn ($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount'))),

                            TextInput::make('unit_amount')
                                -> label('Valor Unitário')
                                -> prefix('R$')
                                -> numeric()
                                -> required()
                                -> disabled()
                                -> minvalue(1)
                                -> columnSpan(['md' => 3]),

                            TextInput::make('total_amount')
                                -> label('Valor Total')
                                -> prefix('R$')
                                -> numeric()
                                -> required()
                                -> columnSpan(['md' => 3])

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

                ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    -> label('ID')
                    -> sortable()
                    -> searchable()
                    -> visibleFrom('md'),

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
                    })
                    -> toggleable(isToggledHiddenByDefault: true),

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
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('student.name')
                            ->label('Nome'),

                        NumberConstraint::make('grand_total')
                            ->icon('heroicon-m-currency-dollar')
                            ->label('Valor Total'),
                            
                        DateConstraint::make('created_at')
                            ->label('Criado em'),
                    ])
                    ->constraintPickerColumns(2),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            
            ->deferFilters()
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
                    BulkAction::make('export')
                    -> label('Exportar PDF')
                    -> icon('heroicon-o-document-arrow-down')
                    -> action(function (Collection $records) {
                        // Coleta os IDs dos registros selecionados
                        $orderIds = $records->pluck('id')->toArray();
                        return redirect()->route('order.pdf.download', ['ids' => $orderIds]);
                    }),
                ]),
            ])
            ->poll('25s')
            ->defaultSort('created_at', 'desc');
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
