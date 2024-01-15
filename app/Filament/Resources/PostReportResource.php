<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostReportResource\Pages;
use App\Filament\Resources\PostReportResource\RelationManagers;
use App\Models\PostReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
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

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?int $navigationSort = 4;

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
                        ->tooltip('Reporter Avatar')
                        ->circular()
                        ->grow(false),
                    Stack::make([
                        Tables\Columns\TextColumn::make('user.nama_lengkap')
                            ->label('Reporter Full Name')
                            ->tooltip('Reporter Full Name')
                            ->weight(FontWeight::Bold)
                            ->grow(false),
                        Tables\Columns\TextColumn::make('user.username')
                            ->tooltip('Reporter Username')
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
                Tables\Actions\Action::make('View')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->infolist([
                        Section::make('Reporter')
                            ->schema([
                                TextEntry::make('user.nama_lengkap')
                                    ->label('Author Name'),
                                TextEntry::make('user.username')
                                    ->label('Author Username'),
                            ])
                            ->columns(),
                        Section::make('Post Information')
                            ->schema([
                                TextEntry::make('post.user.nama_lengkap')
                                    ->label('Author Name'),
                                TextEntry::make('post.user.username')
                                    ->label('Author Username'),
                                TextEntry::make('post.title'),
                                TextEntry::make('post.name_visibility')
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
                                TextEntry::make('post.post_visibility')
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
                                TextEntry::make('post.status')
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
                                TextEntry::make('post.postComments')
                                    ->label('Post Comments')
                                    ->getStateUsing(fn ($record) => $record->post->postComments->count()),
                                TextEntry::make('post.postLikes')
                                    ->label('Post Likes')
                                    ->getStateUsing(fn ($record) => $record->post->postLikes->count()),
                                TextEntry::make('post.postShares')
                                    ->label('Post Shares')
                                    ->getStateUsing(fn ($record) => $record->post->postShares->count()),
                                TextEntry::make('post.postViews')
                                    ->label('Post Views')
                                    ->getStateUsing(fn ($record) => $record->post->postViews->count())
                            ])
                            ->columns(),
                        Section::make('Post')
                            ->schema([
                                ImageEntry::make('post.postImages.path')
                                    ->disk('images')
                                    ->label('Images'),
                                TextEntry::make('post.content')
                                    ->markdown()
                            ])
                    ]),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\Action::make('View')
                //     ->icon('heroicon-m-eye')
                //     ->color('gray')
                //     ->infolist([
                //         Section::make('Post Information')
                //             ->schema([
                //                 TextEntry::make('user.nama_lengkap')
                //                     ->label('Author Name'),
                //                 TextEntry::make('user.username')
                //                     ->label('Author Username'),
                //                 TextEntry::make('post.title'),
                //                 TextEntry::make('post.name_visibility')
                //                     ->label('Name Visibility')
                //                     ->formatStateUsing(fn ($state): string => match ($state) {
                //                         0 => 'Hidden',
                //                         1 => 'Public'
                //                     })
                //                     ->badge()
                //                     ->color(fn ($state): string => match ($state) {
                //                         0 => 'gray',
                //                         1 => 'success'
                //                     }),
                //                 TextEntry::make('post.post_visibility')
                //                     ->label('Post Visibility')
                //                     ->formatStateUsing(fn ($state): string => match ($state) {
                //                         0 => 'Private',
                //                         1 => 'Public'
                //                     })
                //                     ->badge()
                //                     ->color(fn ($state): string => match ($state) {
                //                         0 => 'gray',
                //                         1 => 'success'
                //                     }),
                //                 TextEntry::make('post.status')
                //                     ->formatStateUsing(fn ($state): string => match ($state) {
                //                         0 => 'Belum Diproses',
                //                         1 => 'Sedang Diproses',
                //                         2 => 'Sudah Ditangani',
                //                         3 => 'Ditolak'
                //                     })
                //                     ->badge()
                //                     ->color(fn ($state): string => match ($state) {
                //                         0 => 'gray',
                //                         1 => 'warning',
                //                         2 => 'success',
                //                         3 => 'danger',
                //                     }),
                //                 // TextEntry::make('status_message'),
                //                 TextEntry::make('postComments')
                //                     ->label('Post Comments')
                //                     ->getStateUsing(fn ($record) => $record->post->postComments->count()),
                //                 TextEntry::make('postLikes')
                //                     ->label('Post Likes')
                //                     ->getStateUsing(fn ($record) => $record->post->postLikes->count()),
                //                 TextEntry::make('postShares')
                //                     ->label('Post Shares')
                //                     ->getStateUsing(fn ($record) => $record->post->postShares->count()),
                //                 TextEntry::make('postViews')
                //                     ->label('Post Views')
                //                     ->getStateUsing(fn ($record) => $record->post->postViews->count())
                //             ])
                //             ->columns(),
                //         Section::make('Body')
                //             ->schema([
                //                 ImageEntry::make('post.postImages.path')
                //                     ->disk('images')
                //                     ->label('Images'),
                //                 TextEntry::make('post.content')
                //                     ->markdown()
                //             ])
                //     ]),
                Tables\Actions\Action::make('Delete')
                    ->label('Takedown Post')
                    ->color('danger')
                    ->icon('heroicon-s-trash')
                    ->requiresConfirmation()
                    ->action(fn (PostReport $record) => $record->post()->delete())
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
