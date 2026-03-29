<?php

namespace App\Filament\Resources\TransaksiPenjualanResource\Pages;

use App\Filament\Resources\TransaksiPenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiPenjualan extends EditRecord
{
    protected static string $resource = TransaksiPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $total = $this->record->detailPenjualan()->sum('subtotal');
        $this->record->updateQuietly(['total' => $total]);
    }
}
