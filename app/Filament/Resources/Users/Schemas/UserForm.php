<?php

namespace App\Filament\Resources\Users\Schemas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        $user         = Auth::user();
        $isSuperAdmin = $user->hasRole('super_admin');

        return $schema
            ->components([
                FileUpload::make('avatar_url')
                    ->label('Avatar')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('custom-avatars')
                    ->maxSize(2048)
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->maxLength(255)
                    ->regex('/^[a-zA-Z0-9._]+$/') // only letters, numbers, periods, underscores
                    ->unique(ignoreRecord: true),

                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->maxLength(255)
                    ->email()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('Password')
                    ->required(fn(string $context): bool => $context === 'create')
                    ->password()
                    ->minLength(6)
                    ->confirmed()
                    ->revealable()
                    ->autocomplete('new-password')
                    ->dehydrated(fn($state) => !empty($state)),

                TextInput::make('password_confirmation')
                    ->label('Password Confirmation')
                    ->required(fn(string $context): bool => $context === 'create')
                    ->password()
                    ->minLength(6)
                    ->revealable()
                    ->dehydrated(fn($state) => !empty($state)),

                Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query)  use ($isSuperAdmin) {
                            if (!$isSuperAdmin) {
                                $query->where('name', '!=', 'super_admin');
                            }
                        }
                    ),
            ]);
    }
}
