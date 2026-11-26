<?php

namespace App\Filament\Resources\Lcs;

use App\Filament\Resources\Concerns\ScopesToOwnedPegawai;
use App\Filament\Resources\Lcs\Pages\CreateLc;
use App\Filament\Resources\Lcs\Pages\EditLc;
use App\Filament\Resources\Lcs\Pages\ListLcs;
use App\Filament\Resources\Lcs\Schemas\LcForm;
use App\Filament\Resources\Lcs\Tables\LcsTable;
use App\Models\Lc;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LcResource extends Resource
{
    use ScopesToOwnedPegawai;

    protected static ?string $model = Lc::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|UnitEnum|null $navigationGroup = 'Pengembangan';

    protected static ?string $modelLabel = 'LC';

    protected static ?string $pluralModelLabel = 'LC';

    protected static ?string $recordTitleAttribute = 'materi_pengembangan';

    public static function form(Schema $schema): Schema
    {
        return LcForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LcsTable::configure($table);
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
            'index' => ListLcs::route('/'),
            'create' => CreateLc::route('/create'),
            'edit' => EditLc::route('/{record}/edit'),
        ];
    }
}
