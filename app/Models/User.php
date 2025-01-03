<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label('User Name')
                ->string()
                ->required()
                ->columnSpanFull()
                ->minLength(3)
                ->maxLength(255),

            Grid::make('email')
                ->columns(2)
                ->schema([
                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    DateTimePicker::make('email_verified_at')
                        ->label('Email Verified At')
                        ->default(now()),
                ]),

            Grid::make('password')
                ->columns(2)
                ->visible(fn(string $context) => $context !== 'view')
                ->schema([
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord)
                        ->dehydrated(fn($state) => filled($state)),

                    TextInput::make('passwordConfirmation')
                        ->label('Password Confirmation')
                        ->password()
                        ->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord)
                        ->dehydrated(false)

                ])
        ];
    }
}
