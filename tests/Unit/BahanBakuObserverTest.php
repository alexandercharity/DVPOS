<?php

namespace Tests\Unit;

use App\Observers\BahanBakuObserver;
use PHPUnit\Framework\TestCase;

class BahanBakuObserverTest extends TestCase
{
    private BahanBakuObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new BahanBakuObserver();
    }

    public function test_convert_gram_to_base(): void
    {
        $this->assertEquals(100.0, BahanBakuObserver::convertToBase(100, 'gram'));
    }

    public function test_convert_kg_to_base(): void
    {
        $this->assertEquals(1000.0, BahanBakuObserver::convertToBase(1, 'kg'));
    }

    public function test_convert_liter_to_base(): void
    {
        $this->assertEquals(500.0, BahanBakuObserver::convertToBase(0.5, 'liter'));
    }

    public function test_convert_ml_to_base(): void
    {
        $this->assertEquals(250.0, BahanBakuObserver::convertToBase(250, 'ml'));
    }

    public function test_convert_pcs_to_base(): void
    {
        $this->assertEquals(5.0, BahanBakuObserver::convertToBase(5, 'pcs'));
    }

    public function test_convert_butir_to_base(): void
    {
        $this->assertEquals(3.0, BahanBakuObserver::convertToBase(3, 'butir'));
    }

    public function test_convert_ikat_to_base(): void
    {
        $this->assertEquals(2.0, BahanBakuObserver::convertToBase(2, 'ikat'));
    }

    public function test_convert_bungkus_to_base(): void
    {
        $this->assertEquals(4.0, BahanBakuObserver::convertToBase(4, 'bungkus'));
    }

    public function test_convert_unknown_satuan_defaults_to_multiplier_one(): void
    {
        $this->assertEquals(10.0, BahanBakuObserver::convertToBase(10, 'unknown_unit'));
    }

    public function test_convert_case_insensitive(): void
    {
        $this->assertEquals(2000.0, BahanBakuObserver::convertToBase(2, 'KG'));
    }

    public function test_convert_zero_value(): void
    {
        $this->assertEquals(0.0, BahanBakuObserver::convertToBase(0, 'kg'));
    }

    public function test_convert_fractional_kg(): void
    {
        $this->assertEquals(500.0, BahanBakuObserver::convertToBase(0.5, 'kg'));
    }

    public function test_convert_large_value(): void
    {
        $this->assertEquals(5000.0, BahanBakuObserver::convertToBase(5, 'kg'));
    }
}
