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
                            -> validationAttribute('RM'),

                        Select::make('guardian_id')
                            -> label('Responsável')
                            -> relationship('guardian','name')
                            -> searchable()
                            -> options(Guardian::where('active', True)->pluck('name', 'id')->toArray()) 
                            -> getSearchResultsUsing(fn (string $search): array => Guardian::where('active', True)->where('name','like',"%{$search}%")->limit(5)->pluck('name', 'id')->toArray())
                            -> getOptionLabelUsing(fn ($value): ?string => Guardian::find($value)?->name)
                            -> required(),

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

                Tables\Columns\TextColumn::make('guardian.name') 
                    -> label('Responsável')
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
