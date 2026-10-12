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
}
