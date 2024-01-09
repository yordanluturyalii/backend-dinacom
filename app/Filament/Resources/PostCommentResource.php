<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostCommentResource\Pages;
use App\Filament\Resources\PostCommentResource\RelationManagers;
use App\Models\Post;
use App\Models\PostComment;
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
                    ->options(Post::all()->pluck('title', 'id')),
                Forms\Components\Select::make('parent_id')
                    ->label('Reply to Comment')
                    ->options(PostComment::all()->pluck('content', 'id')),
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
                        0 => 'Hidden',
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
            'index' => Pages\ListPostComments::route('/'),
            'create' => Pages\CreatePostComment::route('/create'),
            'edit' => Pages\EditPostComment::route('/{record}/edit'),
        ];
    }
}
