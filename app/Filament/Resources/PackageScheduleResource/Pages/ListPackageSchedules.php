<?php

namespace App\Filament\Resources\PackageScheduleResource\Pages;

use App\Filament\Resources\PackageScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageSchedules extends ListRecords
{
    protected static string $resource = PackageScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
