<?php

namespace Tests\Feature;

use App\Filament\Resources\TagihanResource;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TagihanInvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_bayar_action_includes_invoice_and_saves_payment_with_invoice_id(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $customer = Customer::create(['nama' => 'Pak Hendra', 'no_telepon' => '081234567', 'alamat' => 'Sleman']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'no_order'    => 'ORD-TEST-INV-01',
            'nama_acara'  => 'Pernikahan Hendra & Lani',
            'tanggal'     => '2026-10-20',
            'total'       => 10000000,
        ]);

        $invoice = Invoice::create([
            'no_invoice'      => 'INV-ORD-TEST-INV-01-DP1',
            'order_id'        => $order->id,
            'judul_tagihan'   => 'Pembayaran DP 50%',
            'nominal_tagihan' => 5000000,
            'jatuh_tempo'     => '2026-10-20',
        ]);

        Livewire::test(TagihanResource\Pages\ListTagihans::class)
            ->callTableAction('add_payment', $order, [
                'invoice_id'       => $invoice->id,
                'amount'           => 5000000,
                'payment_method'   => 'Transfer BCA',
                'payment_date'     => '2026-10-12',
                'reference_number' => 'PAY-TEST-001',
                'notes'            => 'Transfer via BCA Klik',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('payments', [
            'order_id'         => $order->id,
            'invoice_id'       => $invoice->id,
            'amount'           => 5000000,
            'payment_method'   => 'Transfer BCA',
            'reference_number' => 'PAY-TEST-001',
        ]);

        $payment = Payment::where('reference_number', 'PAY-TEST-001')->first();
        $this->assertNotNull($payment);
        $this->assertEquals($invoice->id, $payment->invoice_id);

        // Test receipt PDF generation
        $response = $this->get(route('receipt.download', $payment));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_custom_invoice_action_defaults_to_remaining_balance_when_partially_paid(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $customer = Customer::create(['nama' => 'Dafa', 'no_telepon' => '081234567', 'alamat' => 'Yogya']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'no_order'    => 'ORD-DAFA-01',
            'nama_acara'  => 'Kawinan',
            'tanggal'     => '2026-10-25',
            'total'       => 45000000,
        ]);

        Payment::create([
            'order_id'         => $order->id,
            'amount'           => 13000000,
            'payment_method'   => 'Transfer BCA',
            'payment_date'     => '2026-10-12',
            'reference_number' => 'PAY-DAFA-DP',
        ]);

        Livewire::test(TagihanResource\Pages\ListTagihans::class)
            ->mountTableAction('custom_invoice', $order)
            ->assertTableActionDataSet([
                'judul_tagihan'   => 'Pelunasan Tagihan',
                'nominal_tagihan' => 32000000,
            ]);
    }

    public function test_bayar_action_defaults_to_selected_invoice_nominal(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $customer = Customer::create(['nama' => 'Test Customer', 'no_telepon' => '08111111', 'alamat' => 'Solo']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'no_order'    => 'ORD-202610-0002LKP',
            'nama_acara'  => 'Event Test',
            'tanggal'     => '2026-10-25',
            'total'       => 10000000,
        ]);

        // Invoice 1 (DP1): 2.000.000 - already paid
        $inv1 = Invoice::create([
            'no_invoice'      => 'INV-ORD-202610-0002LKP-DP1',
            'order_id'        => $order->id,
            'judul_tagihan'   => 'Pembayaran DP 1',
            'nominal_tagihan' => 2000000,
            'jatuh_tempo'     => '2026-10-20',
        ]);
        Payment::create([
            'order_id'         => $order->id,
            'invoice_id'       => $inv1->id,
            'amount'           => 2000000,
            'payment_method'   => 'Transfer BCA',
            'payment_date'     => '2026-10-10',
            'reference_number' => 'PAY-001',
        ]);

        // Invoice 2 (DP2): 1.637.907 - UNPAID
        $inv2 = Invoice::create([
            'no_invoice'      => 'INV-ORD-202610-0002LKP-DP2',
            'order_id'        => $order->id,
            'judul_tagihan'   => 'Pembayaran DP 2',
            'nominal_tagihan' => 1637907,
            'jatuh_tempo'     => '2026-10-22',
        ]);

        // When opening the Bayar modal, invoice_id should be inv2 and amount should be 1637907 (not total order remaining 8000000)
        Livewire::test(TagihanResource\Pages\ListTagihans::class)
            ->mountTableAction('add_payment', $order)
            ->assertTableActionDataSet([
                'invoice_id' => $inv2->id,
                'amount'     => 1637907,
            ]);
    }

    public function test_paid_invoice_is_excluded_from_payment_options(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $customer = Customer::create(['nama' => 'Test Customer 2', 'no_telepon' => '0822222', 'alamat' => 'Jogja']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'no_order'    => 'ORD-202610-EXCLUDE',
            'nama_acara'  => 'Event Exclude Test',
            'tanggal'     => '2026-10-25',
            'total'       => 5000000,
        ]);

        // Invoice 1 (DP1): 2.500.000 - already fully paid
        $inv1 = Invoice::create([
            'no_invoice'      => 'INV-ORD-202610-EXCLUDE-DP1',
            'order_id'        => $order->id,
            'judul_tagihan'   => 'DP 1',
            'nominal_tagihan' => 2500000,
            'jatuh_tempo'     => '2026-10-20',
        ]);
        Payment::create([
            'order_id'         => $order->id,
            'invoice_id'       => $inv1->id,
            'amount'           => 2500000,
            'payment_method'   => 'Transfer BCA',
            'payment_date'     => '2026-10-10',
            'reference_number' => 'PAY-EXC-001',
        ]);

        // Invoice 2 (Pelunasan): 2.500.000 - UNPAID
        $inv2 = Invoice::create([
            'no_invoice'      => 'INV-ORD-202610-EXCLUDE-DP2',
            'order_id'        => $order->id,
            'judul_tagihan'   => 'Pelunasan',
            'nominal_tagihan' => 2500000,
            'jatuh_tempo'     => '2026-10-22',
        ]);

        Livewire::test(TagihanResource\Pages\ListTagihans::class)
            ->mountTableAction('add_payment', $order)
            ->assertTableActionDataSet([
                'invoice_id' => $inv2->id,
                'amount'     => 2500000,
            ]);

        // When order is fully paid, remaining balance is 0
        Payment::create([
            'order_id'         => $order->id,
            'invoice_id'       => $inv2->id,
            'amount'           => 2500000,
            'payment_method'   => 'Transfer BCA',
            'payment_date'     => '2026-10-11',
            'reference_number' => 'PAY-EXC-002',
        ]);

        $this->assertEquals(0, max(0, $order->total - $order->fresh()->payments()->sum('amount')));
    }
}
