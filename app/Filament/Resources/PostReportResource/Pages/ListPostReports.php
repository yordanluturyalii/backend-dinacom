<?php

namespace App\Filament\Resources\PostReportResource\Pages;

use App\Filament\Resources\PostReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPostReports extends ListRecords
{
    protected static string $resource = PostReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
