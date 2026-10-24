<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Enums\TenantStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tenant Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('address')
                    ->label('Address')
                    ->maxLength(255),

                TextInput::make('phone_number')
                    ->label('Phone number')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('email')
                    ->label('Email ')
                    ->email()
                    ->maxLength(255),

                Select::make('timezone')
                    ->label('Time Zone')
                    ->required()
                    ->options([
                        'Asia/Jakarta'  => 'WIB (Asia/Jakarta)',
                        'Asia/Makassar' => 'WITA (Asia/Makassar)',
                        'Asia/Jayapura' => 'WIT (Asia/Jayapura)',
                    ]),

                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options(TenantStatus::class)
                    ->default(TenantStatus::Active),
            ]);
    }
}
