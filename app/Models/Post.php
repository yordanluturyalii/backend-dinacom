<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'name_visibility',
        'post_visibility',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function postImages(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

    public function PostComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function PostLikes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function PostReports(): HasMany
    {
        return $this->hasMany(PostReport::class);
    }

    public function PostShares(): HasMany
    {
        return $this->hasMany(PostShare::class);
    }

    public function PostViews(): HasMany
    {
        return $this->hasMany(PostView::class);
    }
}
