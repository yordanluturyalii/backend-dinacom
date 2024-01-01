<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        0 => 'Belum Ditangani',
                        1 => 'Sedang Ditangani',
                        2 => 'Selesai',
                        3 => 'Ditolak'
                    ]),
                // Forms\Components\Textarea::make('status_message')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('postImages.path')
                    ->disk('images')
                    ->label('Image')
                    ->circular()
                    ->stacked()
                    ->limit(2)
                    ->limitedRemainingText(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(26),
                Tables\Columns\TextColumn::make('name_visibility')
                    ->label('Name Visibility')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        0 => 'Hidden',
                        1 => 'Public'
                    })
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        0 => 'gray',
                        1 => 'success'
                    }),
                Tables\Columns\TextColumn::make('post_visibility')
                    ->label('Post Visibility')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        0 => 'Private',
                        1 => 'Public'
                    })
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        0 => 'gray',
                        1 => 'success'
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        0 => 'Belum Ditangani',
                        1 => 'Sedang Ditangani',
                        2 => 'Selesai',
                        3 => 'Ditolak'
                    })
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        0 => 'gray',
                        1 => 'warning',
                        2 => 'success',
                        3 => 'danger',
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('View')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->infolist([
                        Section::make('Post Information')
                            ->schema([
                                TextEntry::make('user.nama_lengkap')
                                    ->label('Author Name'),
                                TextEntry::make('user.username')
                                    ->label('Author Username'),
                                TextEntry::make('title'),
                                TextEntry::make('name_visibility')
                                    ->label('Name Visibility')
                                    ->formatStateUsing(fn ($state): string => match ($state) {
                                        0 => 'Hidden',
                                        1 => 'Public'
                                    })
                                    ->badge()
                                    ->color(fn ($state): string => match ($state) {
                                        0 => 'gray',
                                        1 => 'success'
                                    }),
                                TextEntry::make('post_visibility')
                                    ->label('Post Visibility')
                                    ->formatStateUsing(fn ($state): string => match ($state) {
                                        0 => 'Private',
                                        1 => 'Public'
                                    })
                                    ->badge()
                                    ->color(fn ($state): string => match ($state) {
                                        0 => 'gray',
                                        1 => 'success'
                                    }),
                                TextEntry::make('status')
                                    ->formatStateUsing(fn ($state): string => match ($state) {
                                        0 => 'Belum Diproses',
                                        1 => 'Sedang Diproses',
                                        2 => 'Sudah Ditangani',
                                        3 => 'Ditolak'
                                    })
                                    ->badge()
                                    ->color(fn ($state): string => match ($state) {
                                        0 => 'gray',
                                        1 => 'warning',
                                        2 => 'success',
                                        3 => 'danger',
                                    }),
                                // TextEntry::make('status_message'),
                                TextEntry::make('postComments')
                                    ->label('Post Comments')
                                    ->formatStateUsing(fn ($record) => $record->postComments->count()),
                                TextEntry::make('postLikes')
                                    ->label('Post Likes')
                                    ->formatStateUsing(fn ($record) => $record->postLikes->count()),
                                TextEntry::make('postShares')
                                    ->label('Post Shares')
                                    ->formatStateUsing(fn ($record) => $record->postShares->count()),
                                TextEntry::make('postViews')
                                    ->label('Post Views')
                                    ->formatStateUsing(fn ($record) => $record->postViews->count())
                            ])
                            ->columns(),
                        Section::make('Body')
                            ->schema([
                                ImageEntry::make('postImages.path')
                                    ->disk('images')
                                    ->label('Images'),
                                TextEntry::make('content')
                                    ->markdown()
                            ])
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist;
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            // 'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
