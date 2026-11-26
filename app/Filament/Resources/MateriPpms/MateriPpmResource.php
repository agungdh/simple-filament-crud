<?php

namespace App\Filament\Resources\MateriPpms;

use App\Filament\Resources\MateriPpms\Pages\CreateMateriPpm;
use App\Filament\Resources\MateriPpms\Pages\EditMateriPpm;
use App\Filament\Resources\MateriPpms\Pages\ListMateriPpms;
use App\Filament\Resources\MateriPpms\Schemas\MateriPpmForm;
use App\Filament\Resources\MateriPpms\Tables\MateriPpmsTable;
use App\Models\MateriPpm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MateriPpmResource extends Resource
{
    protected static ?string $model = MateriPpm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static string|UnitEnum|null $navigationGroup = 'Kartu Kontrol';

    protected static ?string $modelLabel = 'Materi PPM';

    protected static ?string $pluralModelLabel = 'Materi PPM';

    protected static ?string $recordTitleAttribute = 'materi_pengembangan';

    public static function form(Schema $schema): Schema
    {
        return MateriPpmForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MateriPpmsTable::configure($table);
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
            'index' => ListMateriPpms::route('/'),
            'create' => CreateMateriPpm::route('/create'),
            'edit' => EditMateriPpm::route('/{record}/edit'),
        ];
    }
}
