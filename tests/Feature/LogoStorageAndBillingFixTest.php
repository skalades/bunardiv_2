<?php

namespace Tests\Feature;

use App\Filament\Pages\Settings;
use App\Filament\Resources\TagihanResource;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LogoStorageAndBillingFixTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_logo_is_stored_on_public_disk_and_found_by_get_logo_path(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $file = UploadedFile::fake()->image('logo.jpg', 200, 200);

        Livewire::test(Settings::class)
            ->set('data.company_name', 'N7 DECORATION & CATERING BU NARDI')
            ->set('data.company_logo', [$file->store('logos', 'public')])
            ->call('save')
            ->assertHasNoErrors();

        $setting = Setting::first();
        $this->assertNotNull($setting);
        $this->assertNotEmpty($setting->company_logo);

        // Path candidate must be resolved
        $this->assertNotNull($setting->getLogoPath());
    }

    public function test_tagihan_sisa_handles_overpayment_without_negative_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $customer = Customer::create(['nama' => 'Daffa Test', 'no_telepon' => '081234567']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'no_order' => 'ORD-OVERPAY-01',
            'nama_acara' => 'Ulang Tahun',
            'tanggal' => '2026-10-25',
            'total' => 5000000,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 6000000,
            'payment_method' => 'Transfer BCA',
            'payment_date' => now(),
            'reference_number' => 'PAY-OP-01',
        ]);

        Livewire::test(TagihanResource\Pages\ListTagihans::class)
            ->assertSee('Lebih Bayar');
    }

    public function test_add_payment_caps_amount_to_order_remaining_balance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $customer = Customer::create(['nama' => 'Daffa Test 2', 'no_telepon' => '081234567']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'no_order' => 'ORD-CAP-01',
            'nama_acara' => 'Ulang Tahun Daffa',
            'tanggal' => '2026-10-25',
            'total' => 5400000,
        ]);

        // Main invoice with 5.400.000
        $mainInv = Invoice::create([
            'no_invoice' => 'INV-ORD-CAP-01',
            'order_id' => $order->id,
            'judul_tagihan' => 'Tagihan Pesanan',
            'nominal_tagihan' => 5400000,
            'jatuh_tempo' => now(),
        ]);

        // DP payment 2.700.000 paid
        Payment::create([
            'order_id' => $order->id,
            'amount' => 2700000,
            'payment_method' => 'Transfer BCA',
            'payment_date' => now(),
            'reference_number' => 'PAY-DP-01',
        ]);

        // Mount Bayar action for main invoice: default amount must be 2.700.000 (remaining balance), NOT 5.400.000!
        Livewire::test(TagihanResource\Pages\ListTagihans::class)
            ->mountTableAction('add_payment', $order)
            ->assertTableActionDataSet([
                'invoice_id' => $mainInv->id,
                'amount' => 2700000,
            ]);
    }
}
