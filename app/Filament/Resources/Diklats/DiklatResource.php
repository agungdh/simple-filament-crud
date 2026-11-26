<?php

namespace App\Filament\Resources\Diklats;

use App\Filament\Resources\Diklats\Pages\CreateDiklat;
use App\Filament\Resources\Diklats\Pages\EditDiklat;
use App\Filament\Resources\Diklats\Pages\ListDiklats;
use App\Filament\Resources\Diklats\Schemas\DiklatForm;
use App\Filament\Resources\Diklats\Tables\DiklatsTable;
use App\Models\Diklat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DiklatResource extends Resource
{
    protected static ?string $model = Diklat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Kartu Kontrol';

    protected static ?string $modelLabel = 'Diklat';

    protected static ?string $pluralModelLabel = 'Diklat';

    protected static ?string $recordTitleAttribute = 'nomor_surat';

    public static function form(Schema $schema): Schema
    {
        return DiklatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiklatsTable::configure($table);
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
            'index' => ListDiklats::route('/'),
            'create' => CreateDiklat::route('/create'),
            'edit' => EditDiklat::route('/{record}/edit'),
        ];
    }
}
