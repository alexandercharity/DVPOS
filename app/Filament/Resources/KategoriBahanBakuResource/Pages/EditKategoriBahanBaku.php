<?php

namespace App\Filament\Resources\KategoriBahanBakuResource\Pages;

use App\Filament\Resources\KategoriBahanBakuResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKategoriBahanBaku extends EditRecord
{
    protected static string $resource = KategoriBahanBakuResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
