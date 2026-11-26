<?php

namespace App\Filament\Resources\Mentorings;

use App\Filament\Resources\Concerns\ScopesToOwnedPegawai;
use App\Filament\Resources\Mentorings\Pages\CreateMentoring;
use App\Filament\Resources\Mentorings\Pages\EditMentoring;
use App\Filament\Resources\Mentorings\Pages\ListMentorings;
use App\Filament\Resources\Mentorings\Schemas\MentoringForm;
use App\Filament\Resources\Mentorings\Tables\MentoringsTable;
use App\Models\Mentoring;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MentoringResource extends Resource
{
    use ScopesToOwnedPegawai;

    protected static ?string $model = Mentoring::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Lainnya';

    protected static ?string $modelLabel = 'Mentoring';

    protected static ?string $pluralModelLabel = 'Mentoring';

    protected static ?string $recordTitleAttribute = 'materi_pengembangan';

    public static function form(Schema $schema): Schema
    {
        return MentoringForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MentoringsTable::configure($table);
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
            'index' => ListMentorings::route('/'),
            'create' => CreateMentoring::route('/create'),
            'edit' => EditMentoring::route('/{record}/edit'),
        ];
    }
}
