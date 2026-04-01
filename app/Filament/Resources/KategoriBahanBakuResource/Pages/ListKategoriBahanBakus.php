<?php

namespace App\Filament\Resources\KategoriBahanBakuResource\Pages;

use App\Filament\Resources\KategoriBahanBakuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKategoriBahanBakus extends ListRecords
{
    protected static string $resource = KategoriBahanBakuResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
