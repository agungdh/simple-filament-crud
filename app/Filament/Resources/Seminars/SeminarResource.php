<?php

namespace App\Filament\Resources\Seminars;

use App\Filament\Resources\Concerns\ScopesToOwnedPegawai;
use App\Filament\Resources\Seminars\Pages\CreateSeminar;
use App\Filament\Resources\Seminars\Pages\EditSeminar;
use App\Filament\Resources\Seminars\Pages\ListSeminars;
use App\Filament\Resources\Seminars\Schemas\SeminarForm;
use App\Filament\Resources\Seminars\Tables\SeminarsTable;
use App\Models\Seminar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SeminarResource extends Resource
{
    use ScopesToOwnedPegawai;

    protected static ?string $model = Seminar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Pengembangan';

    protected static ?string $modelLabel = 'Seminar';

    protected static ?string $pluralModelLabel = 'Seminar';

    protected static ?string $recordTitleAttribute = 'materi_pengembangan';

    public static function form(Schema $schema): Schema
    {
        return SeminarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeminarsTable::configure($table);
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
            'index' => ListSeminars::route('/'),
            'create' => CreateSeminar::route('/create'),
            'edit' => EditSeminar::route('/{record}/edit'),
        ];
    }
}
