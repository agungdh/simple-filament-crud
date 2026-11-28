<?php

namespace App\Filament\Resources\BelajarMandiris;

use App\Filament\Resources\BelajarMandiris\Pages\CreateBelajarMandiri;
use App\Filament\Resources\BelajarMandiris\Pages\EditBelajarMandiri;
use App\Filament\Resources\BelajarMandiris\Pages\ListBelajarMandiris;
use App\Filament\Resources\BelajarMandiris\Schemas\BelajarMandiriForm;
use App\Filament\Resources\BelajarMandiris\Tables\BelajarMandirisTable;
use App\Filament\Resources\Concerns\ScopesToOwnedPegawai;
use App\Models\BelajarMandiri;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BelajarMandiriResource extends Resource
{
    use ScopesToOwnedPegawai;

    protected static ?string $model = BelajarMandiri::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static string|UnitEnum|null $navigationGroup = 'Lainnya';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Belajar Mandiri';

    protected static ?string $pluralModelLabel = 'Belajar Mandiri';

    protected static ?string $slug = 'belajar-mandiri';

    protected static ?string $recordTitleAttribute = 'materi_pengembangan';

    public static function form(Schema $schema): Schema
    {
        return BelajarMandiriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BelajarMandirisTable::configure($table);
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
            'index' => ListBelajarMandiris::route('/'),
            'create' => CreateBelajarMandiri::route('/create'),
            'edit' => EditBelajarMandiri::route('/{record}/edit'),
        ];
    }
}
