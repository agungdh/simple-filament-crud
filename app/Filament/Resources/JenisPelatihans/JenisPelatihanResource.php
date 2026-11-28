<?php

namespace App\Filament\Resources\JenisPelatihans;

use App\Filament\Resources\JenisPelatihans\Pages\CreateJenisPelatihan;
use App\Filament\Resources\JenisPelatihans\Pages\EditJenisPelatihan;
use App\Filament\Resources\JenisPelatihans\Pages\ListJenisPelatihans;
use App\Filament\Resources\JenisPelatihans\Schemas\JenisPelatihanForm;
use App\Filament\Resources\JenisPelatihans\Tables\JenisPelatihansTable;
use App\Models\JenisPelatihan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class JenisPelatihanResource extends Resource
{
    protected static ?string $model = JenisPelatihan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Jenis Pelatihan';

    protected static ?string $pluralModelLabel = 'Jenis Pelatihan';

    protected static ?string $slug = 'jenis-pelatihan';

    protected static ?string $recordTitleAttribute = 'jenis_pelatihan';

    public static function form(Schema $schema): Schema
    {
        return JenisPelatihanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JenisPelatihansTable::configure($table);
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
            'index' => ListJenisPelatihans::route('/'),
            'create' => CreateJenisPelatihan::route('/create'),
            'edit' => EditJenisPelatihan::route('/{record}/edit'),
        ];
    }
}
