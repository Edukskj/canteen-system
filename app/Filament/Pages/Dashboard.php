<?php
 
namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

use Filament\Forms\Form;

class Dashboard extends \Filament\Pages\Dashboard
{

    protected static ?string $title = 'Dashboard';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chart-bar';
    }

    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Section::make('')->schema([

                DatePicker::make('startDate')
                    -> label('Data de Inicio'),

                DatePicker::make('endDate')
                    -> label('Data de Fim'),

            ])->columns(2)
        ]);
    }

}