<?php

namespace Tests\Feature;

use App\Filament\Pages\KalenderAcara;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KalenderAcaraTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_cal@bunardi.com',
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_access_kalender_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/kalender')
            ->assertSuccessful()
            ->assertSee('Kalender Jadwal Acara');
    }

    public function test_livewire_kalender_component_renders_and_navigates_months(): void
    {
        Livewire::actingAs($this->admin)
            ->test(KalenderAcara::class)
            ->assertSet('viewMode', 'month')
            ->call('nextMonth')
            ->assertSet('month', (Carbon::now()->month % 12) + 1)
            ->call('previousMonth')
            ->call('today')
            ->assertSet('month', Carbon::now()->month)
            ->assertSet('year', Carbon::now()->year);
    }

    public function test_kalender_filters_orders_correctly(): void
    {
        $customer = Customer::create(['nama' => 'Test Cal Cust', 'no_telp' => '089912345']);

        $order1 = Order::create([
            'no_order' => 'ORD-CAL-001',
            'customer_id' => $customer->id,
            'nama_acara' => 'Gala Dinner Test',
            'jenis_acara' => 'Dinner',
            'tanggal' => Carbon::now()->toDateString(),
            'jam' => '18:00:00',
            'venue' => 'Grand Ballroom',
            'subtotal' => 10000000,
            'total' => 10000000,
            'status' => 'proses',
        ]);

        $order2 = Order::create([
            'no_order' => 'ORD-CAL-002',
            'customer_id' => $customer->id,
            'nama_acara' => 'Khitanan Massal',
            'jenis_acara' => 'Khitanan',
            'tanggal' => Carbon::now()->toDateString(),
            'jam' => '09:00:00',
            'venue' => 'Masjid Raya',
            'subtotal' => 5000000,
            'total' => 5000000,
            'status' => 'selesai',
        ]);

        Livewire::actingAs($this->admin)
            ->test(KalenderAcara::class)
            ->set('search', 'Gala Dinner')
            ->assertSee('Gala Dinner Test')
            ->assertDontSee('Khitanan Massal')
            ->set('search', '')
            ->set('filterStatus', 'selesai')
            ->assertSee('Khitanan Massal')
            ->assertDontSee('Gala Dinner Test');
    }

    public function test_modal_selection_and_closing(): void
    {
        $customer = Customer::create(['nama' => 'Modal Cust', 'no_telp' => '0812345678']);

        $order = Order::create([
            'no_order' => 'ORD-MODAL-001',
            'customer_id' => $customer->id,
            'nama_acara' => 'Wedding of Romeo & Juliet',
            'jenis_acara' => 'Pernikahan',
            'tanggal' => Carbon::now()->toDateString(),
            'jam' => '10:00:00',
            'venue' => 'Verona Palace',
            'subtotal' => 25000000,
            'total' => 25000000,
            'status' => 'pesanan masuk',
        ]);

        Livewire::actingAs($this->admin)
            ->test(KalenderAcara::class)
            ->call('selectOrder', $order->id)
            ->assertSet('selectedOrderId', $order->id)
            ->assertSee('Wedding of Romeo & Juliet')
            ->assertSee('ORD-MODAL-001')
            ->assertSee('Verona Palace')
            ->call('closeModal')
            ->assertSet('selectedOrderId', null);
    }
}
