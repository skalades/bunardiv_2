<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_order_status_via_table_action(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $customer = Customer::create(['nama' => 'Ibu Siti', 'no_telepon' => '0811223344']);
        $order = Order::create([
            'no_order' => 'ORD-20261013-0001',
            'customer_id' => $customer->id,
            'nama_acara' => 'Ulang Tahun Siti',
            'tanggal' => '2026-10-25',
            'total' => 5000000,
            'status' => 'pesanan masuk',
        ]);

        Livewire::test(OrderResource\Pages\ListOrders::class)
            ->callTableAction('ubah_status', $order, [
                'status' => 'selesai',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'selesai',
        ]);
    }

    public function test_payment_creation_auto_syncs_order_status(): void
    {
        $customer = Customer::create(['nama' => 'Budi Santoso', 'no_telepon' => '0812999888']);
        $order = Order::create([
            'no_order' => 'ORD-20261013-0002',
            'customer_id' => $customer->id,
            'nama_acara' => 'Catering Budi',
            'tanggal' => '2026-10-30',
            'total' => 10000000,
            'status' => 'pesanan masuk',
        ]);

        // Bayar DP 3jt
        Payment::create([
            'order_id' => $order->id,
            'amount' => 3000000,
            'payment_method' => 'Transfer BCA',
            'payment_date' => now(),
            'reference_number' => 'PAY-DP-1',
        ]);

        $order->refresh();
        $this->assertEquals('dp dibayar', $order->status);

        // Pelunasan sisa 7jt
        Payment::create([
            'order_id' => $order->id,
            'amount' => 7000000,
            'payment_method' => 'Transfer BCA',
            'payment_date' => now(),
            'reference_number' => 'PAY-LUNAS-1',
        ]);

        $order->refresh();
        $this->assertEquals('selesai', $order->status);
    }
}
