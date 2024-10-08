<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Categorie;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput; 
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $activeNavigationIcon = 'heroicon-s-squares-2x2';

    protected static ?string $navigationGroup = 'Vendas';
    
    protected static ?string $modelLabel = 'Produtos';

    protected static ?int $navigationSort = 2;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Informações do Produto')->schema([
                        Forms\Components\TextInput::make('name')
                            -> label('Nome')
                            -> columnSpanFull()
                            -> required()
                            -> maxLength(255),

                        Forms\Components\MarkdownEditor::make('description')
                            -> label('Descrição')
                            -> columnSpanFull()
                            -> fileAttachmentsDirectory('products'),
                    ])->columns(2),

                    Forms\Components\Section::make('Imagens')->schema([

                        Forms\Components\FileUpload::make('images')
                            -> label('')
                            -> multiple()
                            -> directory('products')
                            -> maxFiles(5)
                            -> reorderable(),
                    ])
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Preço')->schema([
                        Forms\Components\TextInput::make('price')
                            -> label('Valor Unitário')
                            -> numeric()
                            -> required()
                            -> prefix('R$')
                    ]),
    
                    Forms\Components\Section::make('Grupo')->schema([
                        Forms\Components\Select::make('category_id')
                            -> label('Categoria')
                            -> required()
                            -> searchable()
                            -> preload()
                            -> relationship('category','name')
                            -> createOptionForm([
                                TextInput::make('name')
                                    -> required()
                                    -> maxLength(255),

                                Forms\Components\Toggle::make('active')
                                    -> required()
                                    -> default(true),
                                ])
                    ]),
                    
                    Forms\Components\Section::make('Status')->schema([
                        Forms\Components\Toggle::make('active')
                            -> label('Ativo')
                            -> required()
                            -> default(true)
                    ])
                    
                    ])->columnSpan(1)

            ])->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('name')
                    -> label('Nome')
                    -> searchable(),
                
                Tables\Columns\TextColumn::make('category.name')
                    -> label('Categoria')    
                    -> searchable(),
                
                Tables\Columns\TextColumn::make('price')
                    -> label('Preço')    
                    -> money('BRL')    
                    -> searchable(),

                Tables\Columns\IconColumn::make('active')
                    -> label('Ativo')    
                    -> boolean()
                    -> searchable()

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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
