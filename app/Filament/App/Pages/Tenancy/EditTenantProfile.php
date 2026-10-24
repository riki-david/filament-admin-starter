<?php

namespace App\Filament\App\Pages\Tenancy;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EditTenantProfile extends BaseEditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Edit Tenant Profile';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deleteTenant')
                ->label('Delete tenant')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete this tenant?')
                ->modalDescription('All data for this tenant will be permanently deleted and cannot be restored.')
                // Hide when the user lacks permission or this is their only tenant
                ->visible(fn(): bool => Auth::user()->can('delete', $this->tenant)
                    && Auth::user()->getTenants(Filament::getCurrentPanel())->count() > 1)
                ->action(function () {
                    // Re-check on the server: visible() alone is not authorization
                    abort_unless(Auth::user()->can('delete', $this->tenant), 403);

                    $this->tenant->delete();

                    return redirect()->route('filament.app.tenant');
                }),
        ];
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
                    ->label('Email ')
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
}
