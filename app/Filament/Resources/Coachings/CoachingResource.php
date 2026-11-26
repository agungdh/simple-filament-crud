<?php

namespace App\Filament\Resources\Coachings;

use App\Filament\Resources\Coachings\Pages\CreateCoaching;
use App\Filament\Resources\Coachings\Pages\EditCoaching;
use App\Filament\Resources\Coachings\Pages\ListCoachings;
use App\Filament\Resources\Coachings\Schemas\CoachingForm;
use App\Filament\Resources\Coachings\Tables\CoachingsTable;
use App\Filament\Resources\Concerns\ScopesToOwnedPegawai;
use App\Models\Coaching;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CoachingResource extends Resource
{
    use ScopesToOwnedPegawai;

    protected static ?string $model = Coaching::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Lainnya';

    protected static ?string $modelLabel = 'Coaching';

    protected static ?string $pluralModelLabel = 'Coaching';

    protected static ?string $recordTitleAttribute = 'materi_pengembangan';

    public static function form(Schema $schema): Schema
    {
        return CoachingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoachingsTable::configure($table);
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
            'index' => ListCoachings::route('/'),
            'create' => CreateCoaching::route('/create'),
            'edit' => EditCoaching::route('/{record}/edit'),
        ];
    }
}
