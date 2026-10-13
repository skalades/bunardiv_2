<?php

namespace Tests\Feature;

use App\Filament\Widgets\MainDashboardWidget;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UpcomingScheduleDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_dash@bunardi.com',
            'role' => 'admin',
        ]);
    }

    public function test_dashboard_widget_displays_upcoming_7_days_schedule(): void
    {
        $customer = Customer::create(['nama' => 'Budi Santoso', 'no_telp' => '081234567']);

        // 1. Order in 3 days (within 7 days)
        $order3Days = Order::create([
            'no_order' => 'ORD-NEXT-3DAYS',
            'customer_id' => $customer->id,
            'nama_acara' => 'Pernikahan Budi & Ani',
            'jenis_acara' => 'Pernikahan',
            'tanggal' => Carbon::today()->addDays(3)->toDateString(),
            'jam' => '10:00:00',
            'venue' => 'Gedung Pertemuan Graha',
            'subtotal' => 15000000,
            'total' => 15000000,
            'status' => 'proses',
        ]);

        // 2. Order in 10 days (beyond 7 days)
        $order10Days = Order::create([
            'no_order' => 'ORD-NEXT-10DAYS',
            'customer_id' => $customer->id,
            'nama_acara' => 'Konser Musik Akbar',
            'jenis_acara' => 'Konser',
            'tanggal' => Carbon::today()->addDays(10)->toDateString(),
            'jam' => '19:00:00',
            'venue' => 'Stadion',
            'subtotal' => 25000000,
            'total' => 25000000,
            'status' => 'pesanan masuk',
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(MainDashboardWidget::class)
            ->assertSet('scheduleFilter', '7_days')
            ->assertSee('Jadwal 7 Hari Mendatang')
            ->assertSee('Pernikahan Budi & Ani')
            ->assertSee('ORD-NEXT-3DAYS');

        $this->assertCount(1, $test->instance()->upcoming_orders);
        $this->assertEquals('ORD-NEXT-3DAYS', $test->instance()->upcoming_orders->first()->no_order);

        // Switch to today filter: should not see the 3-day event
        $test->call('setScheduleFilter', 'today')
            ->assertSet('scheduleFilter', 'today')
            ->assertSee('Jadwal Hari Ini')
            ->assertSee('Tidak ada jadwal acara');

        $this->assertCount(0, $test->instance()->upcoming_orders);

        // Switch back to 7 days
        $test->call('setScheduleFilter', '7_days')
            ->assertSee('Pernikahan Budi & Ani');

        $this->assertCount(1, $test->instance()->upcoming_orders);
    }

    public function test_dashboard_highlights_today_and_tomorrow_badges(): void
    {
        $customer = Customer::create(['nama' => 'Siti Rahma', 'no_telp' => '081234568']);

        $orderToday = Order::create([
            'no_order' => 'ORD-TODAY-01',
            'customer_id' => $customer->id,
            'nama_acara' => 'Syukuran Hari Ini',
            'jenis_acara' => 'Syukuran',
            'tanggal' => Carbon::today()->toDateString(),
            'jam' => '12:00:00',
            'venue' => 'Rumah Makan Padang',
            'subtotal' => 5000000,
            'total' => 5000000,
            'status' => 'dp dibayar',
        ]);

        $orderTomorrow = Order::create([
            'no_order' => 'ORD-TOMORROW-01',
            'customer_id' => $customer->id,
            'nama_acara' => 'Seminar Bisnis Besok',
            'jenis_acara' => 'Corporate',
            'tanggal' => Carbon::today()->addDay()->toDateString(),
            'jam' => '08:00:00',
            'venue' => 'Hotel Ballroom',
            'subtotal' => 8000000,
            'total' => 8000000,
            'status' => 'pesanan masuk',
        ]);

        Livewire::actingAs($this->admin)
            ->test(MainDashboardWidget::class)
            ->assertSee('HARI INI')
            ->assertSee('Syukuran Hari Ini')
            ->assertSee('BESOK')
            ->assertSee('Seminar Bisnis Besok');
    }
}
