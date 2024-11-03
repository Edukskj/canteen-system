<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Filament\Resources\StudentResource\RelationManagers\OrdersRelationManager;
use App\Models\Student;
use App\Models\Guardian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    protected static ?string $navigationGroup = 'Clientes';

    protected static ?string $modelLabel = 'Alunos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Grid::make()->schema([

                        Forms\Components\TextInput::make('name')
                            -> label('Nome')
                            -> required()
                            -> placeholder('Nome') 
                            -> validationAttribute('Nome')
                            -> rule('min:3'),

                        Forms\Components\TextInput::make('rm')
                            -> label('RM')
                            -> validationAttribute('RM')
                            -> required()
                            -> placeholder('RM'),

                        Select::make('guardian_id')
                            -> label('Responsável')
                            -> relationship('guardian','name', function ($query) {
                                $query->where('active',true);
                            })
                            -> searchable()
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
                                    -> mask('999.999.999-99')
                                    -> placeholder('000.000.000-00')
                                    -> validationAttribute('CPF')
                                    -> afterStateUpdated(function ($state, callable $set) {
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
                                    -> afterStateUpdated(function ($state, callable $set) {
                                        $cleanedPhone = preg_replace('/\D/', '', $state);
                                        $set('phone', $cleanedPhone);
                                    }),
        
                                Forms\Components\Toggle::make('active')
                                    -> label('Ativo')
                                    -> onColor('success')
                                    -> offColor('danger')
                                    -> default(true),

                            ])
                            ->createOptionAction(function (Action $action) {
                                return $action
                                    -> modalHeading('Criar Representante')
                                    -> modalSubmitActionLabel('Criar')
                                    -> modalWidth('lg');
                            }),

                        Forms\Components\Select::make('period')
                            -> label('Período')
                            -> options([
                                'M' => 'Manhã',
                                'T' => 'Tarde',
                                'N' => 'Noite'
                            ]),

                        Forms\Components\TextInput::make('teacher')
                            -> label('Professora')
                            -> visible(fn (callable $get) => $get('infantil') === true),
                            
                            
                        Forms\Components\TextInput::make('grade')
                            -> label('Série')
                            -> visible(fn (callable $get) => $get('infantil') === true),
                        

                        Forms\Components\TextArea::make('observation')
                            -> label('Observação')
                            -> columnSpanFull(),

                        Forms\Components\Toggle::make('active')
                            -> columnSpan(1)
                            -> onColor('success')
                            -> offColor('danger')
                            -> label('Ativo')
                            -> default(true),
                            
                        Forms\Components\Toggle::make('infantil')
                            -> label('Infantil')
                            -> default(false)
                            -> reactive(),
                    

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
                    
                Tables\Columns\TextColumn::make('guardian.name') 
                    -> label('Responsável')
                    -> searchable() 
                    -> sortable(),

                Tables\Columns\TextColumn::make('rm')
                    -> label('RM') 
                    -> searchable() 
                    -> sortable(),

                Tables\Columns\TextColumn::make('period')
                    -> label('Período')
                    -> searchable()
                    ->getStateUsing(function ($record) {
                        return match($record->period) {
                            'M' => 'Manhã',
                            'T' => 'Tarde',
                            'N' => 'Noite',
                            default => 'Não definido',
                        };
                    })
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
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
