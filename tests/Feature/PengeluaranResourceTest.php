<?php

namespace Tests\Feature;

use App\Filament\Resources\PengeluaranResource;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PengeluaranResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_create_pengeluaran(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $customer = Customer::create(['nama' => 'Budi Santoso', 'no_telepon' => '08123', 'alamat' => 'Yogyakarta']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'no_order'    => 'ORD-202610-001',
            'nama_acara'  => 'Pernikahan Budi & Ani',
            'tanggal'     => '2026-10-15',
            'total'       => 5000000,
        ]);

        // Test create page Livewire
        Livewire::test(PengeluaranResource\Pages\CreatePengeluaran::class)
            ->fillForm([
                'tanggal'           => '2026-10-12',
                'kategori'          => 'Properti & Dekorasi',
                'judul'             => 'Beli Bunga Mawar & Melati Segar',
                'nominal'           => 750000,
                'metode_pembayaran' => 'Tunai / Cash',
                'order_id'          => $order->id,
                'pic_id'            => $admin->id,
                'catatan'           => 'Beli di Pasar Bunga Kotabaru',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('expenses', [
            'kategori'          => 'Properti & Dekorasi',
            'judul'             => 'Beli Bunga Mawar & Melati Segar',
            'nominal'           => 750000,
            'order_id'          => $order->id,
            'metode_pembayaran' => 'Tunai / Cash',
        ]);

        $created = Expense::where('judul', 'Beli Bunga Mawar & Melati Segar')->first();
        $this->assertNotNull($created);
        $this->assertStringStartsWith('EXP-20261012-', $created->nomor_transaksi);

        // Test list page render
        Livewire::test(PengeluaranResource\Pages\ListPengeluarans::class)
            ->assertCanSeeTableRecords([$created]);
    }

    public function test_crew_cannot_access_pengeluaran(): void
    {
        $crew = User::factory()->create(['role' => 'crew']);
        $this->actingAs($crew);

        $this->assertFalse(PengeluaranResource::canViewAny());
    }
}
