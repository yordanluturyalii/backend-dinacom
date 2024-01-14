<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostCommentResource\Pages;
use App\Filament\Resources\PostCommentResource\RelationManagers;
use App\Models\Post;
use App\Models\PostComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
use Illuminate\Support\Collection;

class PostCommentResource extends Resource
{
    protected static ?string $model = PostComment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Comments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('admin_id')
                    ->label('Admin')
                    ->required()
                    ->options([
                        1 => 'Admin'
                    ])
                    ->default(1),
                Forms\Components\Select::make('post_id')
                    ->required()
                    ->label('Reply to Post')
                    ->options(Post::all()->pluck('title', 'id'))
                    ->live(),
                Forms\Components\Select::make('parent_id')
                    ->label('Reply to Comment')
                    // ->options(PostComment::all()->pluck('content', 'id')),
                    ->options(fn (Get $get): Collection => PostComment::query()
                        ->where('post_id', $get('post_id'))
                        ->pluck('content', 'id')),
                Forms\Components\Textarea::make('content')
                    ->label('Content')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        Tables\Columns\ImageColumn::make('user.avatar')
                            ->disk('images')
                            ->tooltip('Avatar')
                            ->circular()
                            ->grow(false),
                        Tables\Columns\ImageColumn::make('admin.avatar')
                            ->disk('images')
                            ->tooltip('Avatar')
                            ->circular()
                            ->grow(false),
                    ])->grow(false),
                    Stack::make([
                        Tables\Columns\TextColumn::make('user.nama_lengkap')
                            // ->getStateUsing()
                            ->label('Name')
                            ->tooltip('Name')
                            ->weight(FontWeight::Bold)
                            ->grow(false),
                        Tables\Columns\TextColumn::make('admin.name')
                            // ->getStateUsing(function ($record){
                            //     if (condition) {
                            //         # code...
                            //     } else {
                            //         # code...
                            //     }
                            // })
                            ->tooltip('Username')
                            ->searchable()
                            ->weight(FontWeight::Bold)
                            ->grow(false)
                    ]),
                    Tables\Columns\TextColumn::make('name_visibility')
                    ->label('Name Visibility')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        0 => 'Anonim',
                        1 => 'Public'
                    })
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        0 => 'gray',
                        1 => 'success'
                    }),
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
                        // Section::make('Comment Information')
                        //     ->schema([
                        //         TextEntry::make('user.nama_lengkap')
                        //             ->label('Author Name'),
                        //         TextEntry::make('user.username')
                        //             ->label('Author Username'),
                        //         TextEntry::make('title'),
                        //         TextEntry::make('name_visibility')
                        //             ->label('Name Visibility')
                        //             ->formatStateUsing(fn ($state): string => match ($state) {
                        //                 0 => 'Hidden',
                        //                 1 => 'Public'
                        //             })
                        //             ->badge()
                        //             ->color(fn ($state): string => match ($state) {
                        //                 0 => 'gray',
                        //                 1 => 'success'
                        //             }),
                        //         TextEntry::make('post_visibility')
                        //             ->label('Post Visibility')
                        //             ->formatStateUsing(fn ($state): string => match ($state) {
                        //                 0 => 'Private',
                        //                 1 => 'Public'
                        //             })
                        //             ->badge()
                        //             ->color(fn ($state): string => match ($state) {
                        //                 0 => 'gray',
                        //                 1 => 'success'
                        //             }),
                        //         TextEntry::make('status')
                        //             ->formatStateUsing(fn ($state): string => match ($state) {
                        //                 0 => 'Belum Diproses',
                        //                 1 => 'Sedang Diproses',
                        //                 2 => 'Sudah Ditangani',
                        //                 3 => 'Ditolak'
                        //             })
                        //             ->badge()
                        //             ->color(fn ($state): string => match ($state) {
                        //                 0 => 'gray',
                        //                 1 => 'warning',
                        //                 2 => 'success',
                        //                 3 => 'danger',
                        //             }),
                        //         // TextEntry::make('status_message'),
                        //         TextEntry::make('postComments')
                        //             ->label('Post Comments')
                        //             ->getStateUsing(fn ($record) => $record->postComments->count()),
                        //         TextEntry::make('postLikes')
                        //             ->label('Post Likes')
                        //             ->getStateUsing(fn ($record) => $record->postLikes->count()),
                        //         TextEntry::make('postShares')
                        //             ->label('Post Shares')
                        //             ->getStateUsing(fn ($record) => $record->postShares->count()),
                        //         TextEntry::make('postViews')
                        //             ->label('Post Views')
                        //             ->getStateUsing(fn ($record) => $record->postViews->count())
                        //     ])
                        //     ->columns(),
                        Section::make('Post')
                            ->schema([
                                ImageEntry::make('post.postImages.path')
                                    ->disk('images')
                                    ->label('Images'),
                                TextEntry::make('post.content')
                                    ->markdown()
                            ]),
                        Section::make('Comment Information')
                            ->schema([
                                TextEntry::make('user.nama_lengkap')
                                    ->label('Author Name')
                                    ->visible(fn ($record): bool => $record->user_id !== null),
                                TextEntry::make('user.username')
                                    ->label('Author Username')
                                    ->visible(fn ($record): bool => $record->user_id !== null),
                                TextEntry::make('admin.name')
                                    ->label('Author Username')
                                    ->visible(fn ($record): bool => $record->user_id === null),
                                TextEntry::make('name_visibility')
                                    ->label('Name Visibility')
                                    ->formatStateUsing(fn ($state): string => match ($state) {
                                        0 => 'Anonim',
                                        1 => 'Public'
                                    })
                                    ->badge()
                                    ->color(fn ($state): string => match ($state) {
                                        0 => 'gray',
                                        1 => 'success'
                                    }),
                                TextEntry::make('content')
                                    ->markdown()
                            ])
                            ->columns(),
                        Section::make('Reply to')
                            ->schema([
                                TextEntry::make('user.nama_lengkap')
                                    ->label('Author Name')
                                    ->visible(fn ($record): bool => $record->user_id !== null),
                                TextEntry::make('user.username')
                                    ->label('Author Username')
                                    ->visible(fn ($record): bool => $record->user_id !== null),
                                TextEntry::make('admin.name')
                                    ->label('Author Username')
                                    ->visible(fn ($record): bool => $record->user_id === null),
                                TextEntry::make('parentComment.name_visibility')
                                    ->label('Name Visibility')
                                    ->formatStateUsing(fn ($state): string => match ($state) {
                                        0 => 'Anonim',
                                        1 => 'Public'
                                    })
                                    ->badge()
                                    ->color(fn ($state): string => match ($state) {
                                        0 => 'gray',
                                        1 => 'success'
                                    })
                                    ->visible(fn ($record): bool => $record->parent_id !== null),
                                TextEntry::make('parentComment.content')
                                    ->label('Content')
                                    ->markdown()
                                    ->visible(fn ($record): bool => $record->parent_id !== null),
                            ])
                            ->columns()
                            ->visible(fn ($record): bool => $record->parent_id !== null)
                    ]),
                Tables\Actions\DeleteAction::make()
                    ->label('Takedown Comment'),
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
            'index' => Pages\ListPostComments::route('/'),
            'create' => Pages\CreatePostComment::route('/create'),
            'edit' => Pages\EditPostComment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
