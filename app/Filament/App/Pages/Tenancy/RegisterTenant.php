<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Models\Tenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class RegisterTenant extends BaseRegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Tenant';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Tenant name')
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
                    ->label('Email address')
                    ->email()
                    ->maxLength(255),

                Select::make('timezone')
                    ->label('Time zone')
                    ->required()
                    ->options([
                        'Asia/Jakarta'  => 'WIB (Asia/Jakarta)',
                        'Asia/Makassar' => 'WITA (Asia/Makassar)',
                        'Asia/Jayapura' => 'WIT (Asia/Jayapura)',
                    ]),
            ]);
    }

    protected function handleRegistration(array $data): Tenant
    {
        $tenant = Tenant::create($data);

        $tenant->users()->attach(Auth::user());

        return $tenant;
    }
}
