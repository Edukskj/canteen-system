<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
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
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Filament\Forms\Get;
use App\Models\Student;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Support\Enums\IconPosition;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $activeNavigationIcon = 'heroicon-s-banknotes';

    protected static ?string $navigationGroup = 'Pagamentos';

    protected static ?string $modelLabel = 'Movimentações';

    public static function form(Form $form): Form
    {
        
        return $form
        ->schema([
            Section::make('Informações do Pagamento')->schema([
                Forms\Components\Grid::make(12)->schema([
                    Select::make('guardian_id')
                    ->columnSpan(['md' => 6])
                    ->label('Responsável')
                    ->searchable()
                    ->relationship('guardian', 'name', function ($query) {
                        $query->where('active', true);
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {

                        if ($state) {
                            // Obtém todos os alunos associados ao guardian selecionado
                            $students = Student::where('guardian_id', $state)
                                ->where('active', true)
                                ->pluck('name', 'id'); // Pluck os IDs e nomes dos alunos
                            
                            // Define os valores do select de alunos
                            $set('student_id', null); // Limpa o campo antes de preencher
                            $set('student_id_options', $students); // Define as opções para o select
                            
                            // Se houver um único aluno, selecione-o automaticamente
                            if ($students->count() === 1) {
                                $set('student_id', $students->keys()->first()); // Seleciona o único aluno disponível
                            }
                        } else {
                            // Se nenhum guardian foi selecionado, limpa o select de alunos
                            $set('student_id', null);
                            $set('student_id_options', []); // Limpa as opções
                        }

                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->placeholder('Nome')
                            ->validationAttribute('Nome')
                            ->rule('min:3'),
                
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->required()
                            ->placeholder('exemplo@hotmail.com')
                            ->email()
                            ->validationAttribute('E-mail'),
                
                        Forms\Components\TextInput::make('cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            ->placeholder('000.000.000-00')
                            ->validationAttribute('CPF')
                            ->afterStateUpdated(function ($state, callable $set) {
                                $cleanedCpf = preg_replace('/\D/', '', $state);
                                $set('cpf', $cleanedCpf);
                            }),
                
                        Forms\Components\TextInput::make('phone')
                            ->label('Celular')
                            ->placeholder('(00) 0000-0000')
                            ->tel()
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->mask('(99) 99999-9999')
                            ->validationAttribute('Celular')
                            ->afterStateUpdated(function ($state, callable $set) {
                                $cleanedPhone = preg_replace('/\D/', '', $state);
                                $set('phone', $cleanedPhone);
                            }),
                
                        Forms\Components\Toggle::make('active')
                            ->label('Ativo')
                            ->default(true),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action
                            ->modalHeading('Criar Representante')
                            ->modalSubmitActionLabel('Criar')
                            ->modalWidth('lg');
                    }),
                
                Select::make('student_id')
                    ->columnSpan(['md' => 6])
                    ->label('Aluno')
                    ->searchable()
                    ->required()
                    ->options(fn (callable $get) => $get('student_id_options')) // Usa as opções definidas no guardian_id
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $student = Student::find($state);
                        if ($student) {
                            $set('guardian_id', $student->guardian_id);
                        } else {
                            $set('guardian_id', null);
                        }
                    })
                    ->afterStateHydrated(function ($state, callable $set) {
                        $student = Student::find($state);
                        if ($student) {
                            $set('guardian_id', $student->guardian_id);
                        } else {
                            $set('guardian_id', null);
                        }
                    })
                    -> relationship('student','name',function ($query) {
                        $query->where('active',true);
                    })
                    -> createOptionForm([
                        Forms\Components\TextInput::make('name')
                            -> label('Nome')
                            -> required()
                            -> placeholder('Nome') 
                            -> validationAttribute('Nome')
                            -> rule('min:3'),

                        Forms\Components\TextInput::make('rm')
                            -> label('RM')
                            -> validationAttribute('RM'),

                        Select::make('guardian_id')
                            -> label('Responsável')
                            -> relationship('guardian','name', function ($query) {
                                $query->where('active',true);
                            })
                            -> searchable()
                            -> required(),

                        Forms\Components\Toggle::make('active')
                            -> label('Ativo')
                            -> default(true),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action
                            -> modalHeading('Criar Aluno')
                            -> modalSubmitActionLabel('Criar')
                            -> modalWidth('lg');
                    }),

                Textarea::make('notes')
                    -> label('Notas')
                    -> columnSpanFull()
                    -> hidden(fn (Get $get, string $operation) => $get('notes') === null && $operation !== 'create'),

                TextInput::make('value')
                    ->columnSpan(['md' => 6])
                    -> label('Valor')
                    -> prefix('R$')
                    -> numeric()
                    -> required()
                    -> default(1)
                    -> minvalue(1)
                    -> columnSpanFull(),

                Select::make('type')
                    ->columnSpan(['md' => 6])
                    -> label('Tipo')
                    -> options([
                        'E' => 'Entrada',
                        'S' => 'Saída',
                    ])
                    -> hidden(fn (Get $get) => $get('type') === 'R')
                ])
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guardian.name')
                    -> label('Reponsável')
                    -> sortable()
                    -> searchable(),

                TextColumn::make('student.name')
                    -> label('Aluno')
                    -> sortable()
                    -> searchable()
                    -> visibleFrom('md'),

                TextColumn::make('value')
                    -> badge()
                    -> color(function ($record) {
                        if ($record->value == 0) {
                            return 'gray';
                        }
                        return $record->type === 'E' ? 'success' : ($record->type === 'S' ? 'danger' : 'gray');
                    })
                    -> icon(function ($record) {
                        if ($record->value == 0) {
                            return 'heroicon-m-minus';
                        }
                        return $record->type === 'E' ? 'heroicon-m-arrow-trending-up' : ($record->type === 'S' ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus');
                    })
                    -> label('Valor')
                    -> sortable()
                    -> money('BRL'),

                TextColumn::make('created_at')
                    -> label('Criado em')
                    -> sortable()
                    -> date('d/m/Y H:i')
                    -> visibleFrom('md'),

                TextColumn::make('updated_at')
                    -> label('Atualizado em')
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
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('export')
                    -> label('Exportar para Excel')
                    -> icon('heroicon-o-document-arrow-down')
                    -> action(function(Collection $records) {
                        return Excel::download(new TransactionsExport($records), 'movimentacoes.xlsx');
                    })
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
