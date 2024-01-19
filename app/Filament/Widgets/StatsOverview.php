<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostReport;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->chart([14, 14, 18, 16, 15, 14, 19])
                ->url(env('APP_URL') . 'admin/users'),
            Stat::make('Total Posts', Post::count())
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->chart([12, 14, 14, 13, 16, 13, 15])
                ->url(env('APP_URL') . 'admin/posts'),
            Stat::make('Total Comments', PostComment::count())
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->color('primary')
                ->chart([16, 16, 17, 19, 17, 16, 20])
                ->url(env('APP_URL') . 'admin/post-comments'),
            Stat::make('Total Reports', PostReport::count())
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->chart([16, 14, 17, 15, 17, 16, 25])
                ->url(env('APP_URL') . 'admin/post-reports'),
        ];
    }
}
