<?php

namespace App\Filament\Widgets;

use App\Models\BahanBaku;
use Filament\Widgets\Widget;

class StokRendahWidget extends Widget
{
    protected static string $view = 'filament.widgets.stok-rendah-widget';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->isPemilik();
    }

    public function getStokRendah()
    {
        return BahanBaku::whereRaw('stok <= stok_minimum')->orderBy('stok')->get();
    }
}
