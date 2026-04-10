<?php

namespace App\Filament\Resources\KategoriBahanBakuResource\Pages;

use App\Filament\Resources\KategoriBahanBakuResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKategoriBahanBaku extends CreateRecord
{
    protected static string $resource = KategoriBahanBakuResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
