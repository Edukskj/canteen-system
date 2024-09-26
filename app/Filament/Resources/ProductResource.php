<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Informações do Produto')->schema([
                        Forms\Components\TextInput::make('name')
                            -> label('Nome')
                            -> required()
                            -> maxLength(255),

                        Forms\Components\MarkdownEditor::make('description')
                            -> label('Descrição')
                            -> columnSpanFull()
                            -> fileAttachmentsDirectory('products'),
                    ])->columns(2),

                    Forms\Components\Section::make('Imagens')->schema([

                        Forms\Components\FileUpload::make('images')
                            -> label('Imagens')
                            -> multiple()
                            -> directory('products')
                            -> maxFiles(5)
                            -> reorderable(),
                    ])
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Preço')->schema([
                        Forms\Components\TextInput::make('price')
                            -> label('Preço')
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
                    ])])->columnSpan(1)

            ])->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
