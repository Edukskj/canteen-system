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

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $activeNavigationIcon = 'heroicon-s-banknotes';

    protected static ?string $navigationGroup = 'Movimentações';

    protected static ?string $modelLabel = 'Movimentações';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Informações do Pagamento')->schema([

                Select::make('guardian_id')
                    -> label('Responsável')
                    -> searchable()
                    -> relationship('guardian','name', function ($query) {
                        $query->where('active',true);
                    })
                    -> required()
                    -> createOptionForm([
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
                            -> placeholder('000.000.000-00')
                            -> validationAttribute('CPF'),

                        Forms\Components\TextInput::make('phone')
                            -> label('Celular')
                            -> placeholder('(00) 0000-0000')
                            -> tel()
                            -> telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            -> validationAttribute('Celular'),

                        Forms\Components\Toggle::make('active')
                            -> label('Ativo')
                            -> default(true),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action
                            -> modalHeading('Criar Representante')
                            -> modalSubmitActionLabel('Criar')
                            -> modalWidth('lg');
                    }),
        
                    Select::make('student_id')
                    -> label('Aluno')
                    -> searchable()
                    -> required()
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
                    -> columnSpanFull(),

                TextInput::make('value')
                    -> label('Valor')
                    -> prefix('R$')
                    -> numeric()
                    -> required()
                    -> default(1)
                    -> minvalue(1)
                    -> columnSpanFull(),

                Select::make('type')
                    -> label('Tipo')
                    -> options([
                        'E' => 'Entrada',
                        'S' => 'Saída',
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
                    -> searchable(),

                TextColumn::make('value')
                    -> badge()
                    -> color(function ($record) {
                        if ($record->value == 0) {
                            return 'gray';
                        }
                        return $record->type === 'E' ? 'success' : ($record->type === 'S' ? 'danger' : 'gray');
                    })
                    -> label('Valor')
                    -> sortable()
                    -> money('BRL'),

                TextColumn::make('created_at')
                    -> label('Criado em')
                    -> sortable()
                    -> date('d/m/Y H:i'),

                TextColumn::make('updated_at')
                    -> label('Atualizado em')
                    -> dateTime()
                    -> sortable()
                    -> since()
                    -> toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
