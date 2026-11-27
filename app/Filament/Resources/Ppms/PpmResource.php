<?php

namespace App\Filament\Resources\Ppms;

use App\Filament\Resources\Ppms\Pages\CreatePpm;
use App\Filament\Resources\Ppms\Pages\EditPpm;
use App\Filament\Resources\Ppms\Pages\ListPpms;
use App\Filament\Resources\Ppms\Schemas\PpmForm;
use App\Filament\Resources\Ppms\Tables\PpmsTable;
use App\Models\Ppm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PpmResource extends Resource
{
    protected static ?string $model = Ppm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Kartu Kontrol';

    protected static ?string $modelLabel = 'PPM';

    protected static ?string $pluralModelLabel = 'PPM';

    protected static ?string $slug = 'ppm';

    protected static ?string $recordTitleAttribute = 'nomor_surat';

    public static function form(Schema $schema): Schema
    {
        return PpmForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PpmsTable::configure($table);
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
            'index' => ListPpms::route('/'),
            'create' => CreatePpm::route('/create'),
            'edit' => EditPpm::route('/{record}/edit'),
        ];
    }
}
