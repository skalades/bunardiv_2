<?php

namespace Tests\Feature;

use App\Filament\Pages\KalenderAcara;
use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
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

    public function test_pesanan_baru_button_points_to_pos_cepat_create_order_url(): void
    {
        $response = $this->actingAs($this->admin)->get('/kalender');
        $response->assertSuccessful();
        $response->assertSee('/orders/create');

        // Test POS Cepat page loads successfully
        $createResponse = $this->actingAs($this->admin)->get('/orders/create');
        $createResponse->assertSuccessful();
        $createResponse->assertSee('POS Cepat');
    }

    public function test_admin_can_create_order_via_pos_cepat_page(): void
    {
        $customer = Customer::create(['nama' => 'Pak Budi', 'no_telp' => '081234567']);
        $product = Product::create([
            'nama' => 'Paket Prasmanan Test',
            'kategori' => 'CATERING',
            'harga' => 50000,
        ]);

        $cartData = json_encode([
            [
                'id' => $product->id,
                'name' => $product->nama,
                'price' => 50000,
                'qty' => 10,
            ],
        ]);

        Livewire::actingAs($this->admin)
            ->test(CreateOrder::class)
            ->fillForm([
                'customer_id' => $customer->id,
                'nama_acara' => 'Syukuran Kantor',
                'jenis_acara' => 'Corporate',
                'tanggal' => '2026-10-28',
                'jam' => '12:00:00',
                'venue' => 'Gedung Menara',
                'cart_data' => $cartData,
                'subtotal' => 500000,
                'total' => 500000,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'nama_acara' => 'Syukuran Kantor',
            'total' => 500000,
        ]);

        $order = Order::where('nama_acara', 'Syukuran Kantor')->first();
        $this->assertNotNull($order);
        $this->assertStringStartsWith('ORD-', $order->no_order);
        $this->assertCount(1, $order->items);
        $this->assertEquals(50000, $order->items->first()->harga);
        $this->assertEquals(10, $order->items->first()->qty);
    }
}
