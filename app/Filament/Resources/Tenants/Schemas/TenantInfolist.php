<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Tenant Information')
                    ->columns(2)
                    ->components([
                        TextEntry::make('name')
                            ->label('Tenant Name'),

                        TextEntry::make('slug')
                            ->label('Subdomain')
                            ->copyable(),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),

                        TextEntry::make('timezone')
                            ->label('Time Zone'),
                    ]),

                Section::make('Contact')
                    ->columns(2)
                    ->components([
                        TextEntry::make('phone_number')
                            ->label('Phone Number')
                            ->placeholder('-'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-'),

                        TextEntry::make('address')
                            ->label('Address')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Ownership')
                    ->columns(2)
                    ->components([
                        TextEntry::make('owners.name')
                            ->label('Owners')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('users_count')
                            ->label('Total Users')
                            ->state(fn($record): int => $record->users()->count()),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ]),
            ]);
    }
}
