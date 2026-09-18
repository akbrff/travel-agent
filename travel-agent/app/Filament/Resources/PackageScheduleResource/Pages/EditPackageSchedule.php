<?php

namespace App\Filament\Resources\PackageScheduleResource\Pages;

use App\Filament\Resources\PackageScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageSchedule extends EditRecord
{
    protected static string $resource = PackageScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
