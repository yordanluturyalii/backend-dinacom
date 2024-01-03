<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostReportResource\Pages;
use App\Filament\Resources\PostReportResource\RelationManagers;
use App\Models\PostReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostReportResource extends Resource
{
    protected static ?string $model = PostReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Reports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Tables\Columns\ImageColumn::make('user.avatar')
                        ->disk('images')
                        ->tooltip('Avatar')
                        ->circular()
                        ->grow(false),
                    Stack::make([
                        Tables\Columns\TextColumn::make('user.nama_lengkap')
                            ->label('Full Name')
                            ->tooltip('Full Name')
                            ->weight(FontWeight::Bold)
                            ->grow(false),
                        Tables\Columns\TextColumn::make('user.username')
                            ->tooltip('Username')
                            ->searchable()
                            ->grow(false)
                    ]),
                    Tables\Columns\ToggleColumn::make('user.status')
                        ->tooltip('Account Status')
                        ->onIcon('heroicon-s-no-symbol')
                        ->offIcon('heroicon-s-no-symbol')
                        ->onColor('success')
                        ->offColor('danger')
                        ->grow(false)
                        ->alignEnd()
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->model('post')
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
            'index' => Pages\ListPostReports::route('/'),
            // 'create' => Pages\CreatePostReport::route('/create'),
            // 'edit' => Pages\EditPostReport::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
