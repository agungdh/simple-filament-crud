<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminRekapitulasiTable;
use App\Filament\Widgets\AdminStatsOverview;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DashboardAdmin extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'dashboard-admin';

    protected static ?string $title = 'Dashboard Admin';

    protected static ?string $navigationLabel = 'Dashboard Admin';

    protected static ?int $navigationSort = -1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    /**
     * Only admins may access the admin dashboard.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    /**
     * Get the dashboard filter form.
     */
    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(Dashboard::tahunOptions())
                            ->default(now()->year)
                            ->selectablePlaceholder(false)
                            ->live(),
                        Select::make('modul')
                            ->label('Modul')
                            ->options(Dashboard::modulOptions())
                            ->default('')
                            ->selectablePlaceholder(false)
                            ->live(),
                    ])
                    ->columns(3),
            ]);
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            AdminStatsOverview::class,
            AdminRekapitulasiTable::class,
        ];
    }

    public function getColumns(): int
    {
        return 1;
    }
}
