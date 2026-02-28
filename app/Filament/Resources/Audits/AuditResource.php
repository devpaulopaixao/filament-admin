<?php

namespace App\Filament\Resources\Audits;

use App\Filament\Resources\Audits\Pages\ListAudits;
use App\Helpers\AuditableModels;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Tapp\FilamentAuditing\Filament\Infolists\Components\AuditValuesEntry;
use Tapp\FilamentAuditing\Filament\Resources\Audits\AuditResource as BaseAuditResource;

class AuditResource extends BaseAuditResource
{
    public static function table(Table $table): Table
    {
        $table = parent::table($table);

        $table->pushFilters([
            SelectFilter::make('auditable_type')
                ->label('Model')
                ->options(AuditableModels::getList()),
        ]);

        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAudits::route('/'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Tabs')
                ->columnSpanFull()
                ->tabs([
                    Tab::make((string) trans('filament-auditing::filament-auditing.infolist.tab.info'))
                        ->schema([
                            TextEntry::make('user.name')
                                ->label(trans('filament-auditing::filament-auditing.infolist.user')),
                            TextEntry::make('created_at')
                                ->dateTime('M j, Y H:i:s')
                                ->label(trans('filament-auditing::filament-auditing.infolist.created-at')),
                            TextEntry::make('auditable_type')
                                ->label(trans('filament-auditing::filament-auditing.infolist.audited'))
                                ->formatStateUsing(function (string $state) {
                                    return Str::afterLast($state, '\\');
                                }),
                            TextEntry::make('event')
                                ->label(trans('filament-auditing::filament-auditing.infolist.event')),
                            TextEntry::make('url')
                                ->label(trans('filament-auditing::filament-auditing.infolist.url')),
                            TextEntry::make('ip_address')
                                ->label(trans('filament-auditing::filament-auditing.infolist.ip-address')),
                            TextEntry::make('user_agent')
                                ->label(trans('filament-auditing::filament-auditing.infolist.user-agent'))
                                ->columnSpanFull(),
                            TextEntry::make('tags')
                                ->label(trans('filament-auditing::filament-auditing.infolist.tags'))
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Tab::make((string) trans('filament-auditing::filament-auditing.infolist.tab.old-values'))
                        ->schema([
                            AuditValuesEntry::make('old_values')
                                ->hiddenLabel(),
                        ]),
                    Tab::make((string) trans('filament-auditing::filament-auditing.infolist.tab.new-values'))
                        ->schema([
                            AuditValuesEntry::make('new_values')
                                ->hiddenLabel(),
                        ]),
                ]),
        ]);
    }
}
