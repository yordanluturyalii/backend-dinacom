<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostReportResource\Pages;
use App\Filament\Resources\PostReportResource\RelationManagers;
use App\Models\PostReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'create' => Pages\CreatePostReport::route('/create'),
            'edit' => Pages\EditPostReport::route('/{record}/edit'),
        ];
    }
}
