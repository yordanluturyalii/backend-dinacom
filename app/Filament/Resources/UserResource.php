<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('nama_lengkap')
                //     ->label('Full Name')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\DatePicker::make('tanggal lahir')
                //     ->label('Date of Birth')
                //     ->required()
                //     ->maxDate(now()),
                // Forms\Components\TextInput::make('tempat_tinggal')
                //     ->label('Address')
                //     ->required(),
                // Forms\Components\TextInput::make('username')
                //     ->required(),
                // Forms\Components\TextInput::make('email')
                //     ->required()
                //     ->email(),
                // Forms\Components\TextInput::make('password')
                //     ->required()
                //     ->password(),
                // Forms\Components\Toggle::make('status')
                //     ->label('Account Status')
                //     ->required()
                //     ->onIcon('heroicon-s-no-symbol')
                //     ->offIcon('heroicon-s-no-symbol')
                //     ->onColor('success')
                //     ->offColor('danger')
                //     ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Tables\Columns\ImageColumn::make('avatar')
                        ->disk('images')
                        ->tooltip('Avatar')
                        ->circular()
                        ->grow(false),
                    Stack::make([
                        Tables\Columns\TextColumn::make('nama_lengkap')
                            ->label('Full Name')
                            ->tooltip('Full Name')
                            ->weight(FontWeight::Bold)
                            ->grow(false),
                        Tables\Columns\TextColumn::make('username')
                            ->tooltip('Username')
                            ->searchable()
                            ->grow(false)
                    ]),
                    Tables\Columns\ToggleColumn::make('status')
                        ->tooltip('Account Status')
                        ->onIcon('heroicon-s-no-symbol')
                        ->offIcon('heroicon-s-no-symbol')
                        ->onColor('success')
                        ->offColor('danger')
                        ->grow(false)
                        ->alignEnd()
                ]),
                Panel::make([
                    Split::make([
                        Tables\Columns\TextColumn::make('email')
                            ->tooltip('Email')
                            ->icon('heroicon-m-envelope')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('tanggal_lahir')
                            ->label('Date of Birth')
                            ->tooltip('Date of Birth')
                            ->icon('heroicon-m-cake')
                            ->sortable(),
                        Tables\Columns\TextColumn::make('tempat_tinggal')
                            ->label('Address')
                            ->tooltip('Home Address')
                            ->icon('heroicon-s-home'),
                        Tables\Columns\TextColumn::make('password_konfirmasi')
                            ->label('Password')
                            ->tooltip('User Pass')
                            ->icon('heroicon-s-key'),
                    ])
                ])->collapsed(false)
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
