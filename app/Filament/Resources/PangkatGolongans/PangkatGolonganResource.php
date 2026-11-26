<?php

namespace App\Filament\Resources\PangkatGolongans;

use App\Filament\Resources\PangkatGolongans\Pages\CreatePangkatGolongan;
use App\Filament\Resources\PangkatGolongans\Pages\EditPangkatGolongan;
use App\Filament\Resources\PangkatGolongans\Pages\ListPangkatGolongans;
use App\Filament\Resources\PangkatGolongans\Schemas\PangkatGolonganForm;
use App\Filament\Resources\PangkatGolongans\Tables\PangkatGolongansTable;
use App\Models\PangkatGolongan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PangkatGolonganResource extends Resource
{
    protected static ?string $model = PangkatGolongan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?string $modelLabel = 'Pangkat Golongan';

    protected static ?string $pluralModelLabel = 'Pangkat Golongan';

    protected static ?string $recordTitleAttribute = 'pangkat';

    public static function form(Schema $schema): Schema
    {
        return PangkatGolonganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PangkatGolongansTable::configure($table);
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
            'index' => ListPangkatGolongans::route('/'),
            'create' => CreatePangkatGolongan::route('/create'),
            'edit' => EditPangkatGolongan::route('/{record}/edit'),
        ];
    }
}
