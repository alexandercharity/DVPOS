<?php

namespace App\Filament\Resources\ResepResource\Pages;

use App\Filament\Resources\ResepResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResep extends EditRecord
{
    protected static string $resource = ResepResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
