<?php

namespace Tests\Feature;

use App\Filament\Pages\KalenderAcara;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Filament\Resources\TagihanResource\Pages\ListTagihans;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_del@bunardi.com',
            'role' => 'admin',
        ]);
    }

    public function test_deleting_order_cascades_and_removes_all_related_records(): void
    {
        $customer = Customer::create(['nama' => 'Cust Deletion', 'no_telp' => '0812345678']);
        $product = Product::create(['nama' => 'Pkt Nasi Kotak', 'kategori' => 'CATERING', 'harga' => 35000]);

        $order = Order::create([
            'no_order' => 'ORD-DEL-001',
            'customer_id' => $customer->id,
            'nama_acara' => 'Pesta Ulang Tahun',
            'jenis_acara' => 'Ulang Tahun',
            'tanggal' => Carbon::now()->toDateString(),
            'jam' => '10:00:00',
            'venue' => 'Resto A',
            'subtotal' => 350000,
            'total' => 350000,
            'status' => 'pesanan masuk',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 10,
            'harga' => 35000,
            'subtotal' => 350000,
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'no_invoice' => 'INV-ORD-DEL-001',
            'judul_tagihan' => 'Tagihan DP',
            'nominal_tagihan' => 350000,
            'jatuh_tempo' => Carbon::now()->addDays(3),
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'amount' => 100000,
            'payment_method' => 'Transfer BCA',
            'payment_date' => Carbon::now(),
        ]);

        $workOrder = WorkOrder::create([
            'order_id' => $order->id,
            'pic_id' => $this->admin->id,
            'tanggal_persiapan' => Carbon::now()->toDateString(),
            'status' => 'draft',
        ]);

        $orderId = $order->id;
        $order->delete();

        $this->assertDatabaseMissing('orders', ['id' => $orderId]);
        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
        $this->assertDatabaseMissing('work_orders', ['id' => $workOrder->id]);
    }

    public function test_calendar_modal_can_delete_order(): void
    {
        $customer = Customer::create(['nama' => 'Cust Cal Del', 'no_telp' => '0812345679']);

        $order = Order::create([
            'no_order' => 'ORD-CAL-DEL',
            'customer_id' => $customer->id,
            'nama_acara' => 'Acara Kalender Hapus',
            'jenis_acara' => 'Konser',
            'tanggal' => Carbon::now()->toDateString(),
            'jam' => '19:00:00',
            'venue' => 'Stadion',
            'subtotal' => 1000000,
            'total' => 1000000,
            'status' => 'pesanan masuk',
        ]);

        Livewire::actingAs($this->admin)
            ->test(KalenderAcara::class)
            ->call('selectOrder', $order->id)
            ->assertSee('ORD-CAL-DEL')
            ->assertSee('Hapus Pesanan')
            ->call('deleteOrder', $order->id)
            ->assertSet('selectedOrderId', null)
            ->assertDontSee('ORD-CAL-DEL');

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_order_table_has_delete_action(): void
    {
        $customer = Customer::create(['nama' => 'Cust Table Del', 'no_telp' => '0812345670']);

        $order = Order::create([
            'no_order' => 'ORD-TBL-001',
            'customer_id' => $customer->id,
            'nama_acara' => 'Table Action Order',
            'jenis_acara' => 'Gathering',
            'tanggal' => Carbon::now()->toDateString(),
            'jam' => '08:00:00',
            'venue' => 'Hotel C',
            'subtotal' => 500000,
            'total' => 500000,
            'status' => 'pesanan masuk',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListOrders::class)
            ->assertTableActionExists('delete')
            ->callTableAction('delete', $order);

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_tagihan_table_has_delete_action(): void
    {
        $customer = Customer::create(['nama' => 'Cust Tagihan Del', 'no_telp' => '0812345671']);

        $order = Order::create([
            'no_order' => 'ORD-TGH-001',
            'customer_id' => $customer->id,
            'nama_acara' => 'Tagihan Action Order',
            'jenis_acara' => 'Gathering',
            'tanggal' => Carbon::now()->toDateString(),
            'jam' => '08:00:00',
            'venue' => 'Hotel D',
            'subtotal' => 500000,
            'total' => 500000,
            'status' => 'pesanan masuk',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListTagihans::class)
            ->assertTableActionExists('delete')
            ->callTableAction('delete', $order);

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_view_order_page_has_delete_action(): void
    {
        $customer = Customer::create(['nama' => 'Cust View Del', 'no_telp' => '0812345672']);

        $order = Order::create([
            'no_order' => 'ORD-VIEW-001',
            'customer_id' => $customer->id,
            'nama_acara' => 'View Action Order',
            'jenis_acara' => 'Gathering',
            'tanggal' => Carbon::now()->toDateString(),
            'jam' => '08:00:00',
            'venue' => 'Hotel E',
            'subtotal' => 500000,
            'total' => 500000,
            'status' => 'pesanan masuk',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewOrder::class, ['record' => $order->id])
            ->assertActionExists('delete')
            ->callAction('delete');

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }
}
