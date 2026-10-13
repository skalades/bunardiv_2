@php
    $inventories = \App\Models\Inventory::orderBy('kategori')->orderBy('nama')->get();
    $categories = $inventories->pluck('kategori')->filter()->unique()->values();
@endphp

<div class="pos-equipment-wrapper" 
     x-data="{
        filter: 'All',
        search: '',
        items: [],

        get totalItemsCount() {
            return this.items.length;
        },

        get totalQtyCount() {
            return this.items.reduce((sum, item) => sum + (parseInt(item.qty) || 0), 0);
        },

        getItemQty(id) {
            const item = this.items.find(i => i.id === id);
            return item ? item.qty : 0;
        },

        addItem(inv) {
            const existing = this.items.find(i => i.id === inv.id);
            if (existing) {
                existing.qty = (parseInt(existing.qty) || 0) + 1;
            } else {
                this.items.push({
                    id: inv.id,
                    name: inv.name,
                    category: inv.category,
                    unit: inv.unit,
                    stock: inv.stock,
                    qty: 1
                });
            }
            this.syncFields();
        },

        updateQty(index, delta) {
            let current = parseInt(this.items[index].qty) || 0;
            let next = current + delta;
            if (next <= 0) {
                this.items.splice(index, 1);
            } else {
                this.items[index].qty = next;
            }
            this.syncFields();
        },

        setQtyDirect(index, val) {
            let parsed = parseInt(val);
            if (isNaN(parsed) || parsed <= 0) {
                this.items[index].qty = 1;
            } else {
                this.items[index].qty = parsed;
            }
            this.syncFields();
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.syncFields();
        },

        clearAll() {
            this.items = [];
            this.syncFields();
        },

        syncFields() {
            const hiddenInput = document.querySelector('input[name$=\'selected_equipment_data\']') || 
                                document.querySelector('input[wire\\:model$=\'.selected_equipment_data\']');
            if (hiddenInput) {
                hiddenInput.value = JSON.stringify(this.items);
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
     }">

    <style>
        /* Lebarkan modal window saat picker aktif agar tidak terhimpit */
        .fi-modal-window {
            max-width: min(94vw, 1260px) !important;
            width: 100% !important;
        }

        .pos-equipment-wrapper {
            --bg-dark: #0f1015;
            --bg-card: #15161d;
            --bg-hover: #1c1d27;
            --border-color: #262938;
            --accent-gold: #cfa24b;
            --accent-gold-hover: #deb25b;
            --text-primary: #f1f5f9;
            --text-secondary: #8b92a4;
            --success-green: #2ecc71;
            --danger-red: #e74c3c;
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            box-sizing: border-box;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }

        /* Menggunakan Flexbox agar kedua panel presisi di dalam modal tanpa overflow */
        .pe-layout {
            display: flex;
            flex-direction: row;
            gap: 16px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            min-height: 480px;
        }

        @media (max-width: 960px) {
            .pe-layout {
                flex-direction: column;
            }
            .pe-cart-panel {
                flex: 1 1 auto !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* Sisi Kiri: Panel Katalog Barang */
        .pe-catalog-panel {
            flex: 1 1 0%;
            min-width: 0; /* KUNCI: Mencegah flex blowout */
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .pe-search-bar {
            display: flex;
            align-items: center;
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }

        .pe-search-bar i {
            position: absolute;
            left: 12px;
            font-size: 16px;
            color: var(--text-secondary);
        }

        .pe-search-input {
            width: 100%;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 9px 12px 9px 36px;
            color: var(--text-primary);
            font-size: 13px;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .pe-search-input:focus {
            border-color: var(--accent-gold);
        }

        .pe-categories-scroll {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            padding-bottom: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        .pe-categories-scroll::-webkit-scrollbar {
            height: 4px;
        }
        .pe-categories-scroll::-webkit-scrollbar-thumb {
            background: #2a2d3d;
            border-radius: 4px;
        }

        .pe-cat-chip {
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .pe-cat-chip:hover {
            color: var(--text-primary);
            border-color: #3b4056;
        }

        .pe-cat-chip.active {
            background: var(--accent-gold);
            color: #0c0d10;
            border-color: var(--accent-gold);
            font-weight: 700;
        }

        /* Grid barang otomatis menyesuaikan ruang */
        .pe-grid-items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 10px;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 4px;
            box-sizing: border-box;
        }

        .pe-grid-items::-webkit-scrollbar {
            width: 5px;
        }
        .pe-grid-items::-webkit-scrollbar-thumb {
            background: #2a2d3d;
            border-radius: 4px;
        }

        .pe-item-card {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 12px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 6px;
            position: relative;
            box-sizing: border-box;
            transition: all 0.15s ease;
            user-select: none;
        }

        .pe-item-card:hover {
            border-color: var(--accent-gold);
            transform: translateY(-2px);
            background: var(--bg-hover);
        }

        .pe-item-card.is-selected {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 1px var(--accent-gold);
        }

        .pe-item-cat {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--accent-gold);
            font-weight: 700;
        }

        .pe-item-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
            flex: 1;
        }

        .pe-item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
            font-size: 11px;
            color: var(--text-secondary);
        }

        .pe-badge-stock {
            background: rgba(255, 255, 255, 0.06);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 10.5px;
        }

        .pe-selected-indicator {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--accent-gold);
            color: #0c0d10;
            border-radius: 100px;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 7px;
        }

        /* Sisi Kanan: Panel Keranjang Peralatan Dipilih */
        .pe-cart-panel {
            flex: 0 0 340px;
            width: 340px;
            max-width: 340px;
            min-width: 0; /* KUNCI: Mencegah overflow */
            display: flex;
            flex-direction: column;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .pe-cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .pe-cart-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pe-cart-title i {
            color: var(--accent-gold);
            font-size: 18px;
        }

        .pe-cart-count-badge {
            background: rgba(207, 162, 75, 0.15);
            color: var(--accent-gold);
            border: 1px solid rgba(207, 162, 75, 0.3);
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 100px;
        }

        .pe-cart-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 330px;
            overflow-y: auto;
            flex: 1;
            padding-right: 4px;
            box-sizing: border-box;
        }

        .pe-cart-list::-webkit-scrollbar {
            width: 4px;
        }
        .pe-cart-list::-webkit-scrollbar-thumb {
            background: #2a2d3d;
            border-radius: 4px;
        }

        .pe-cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 10px;
            gap: 8px;
            box-sizing: border-box;
            width: 100%;
            overflow: hidden;
            transition: border-color 0.15s;
        }

        .pe-cart-item:hover {
            border-color: #3b4056;
        }

        .pe-cart-item-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            min-width: 0; /* Agar teks panjang terpotong rapi dengan ellipsis */
            overflow: hidden;
        }

        .pe-cart-item-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pe-cart-item-sub {
            font-size: 10px;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pe-qty-controls {
            display: flex;
            align-items: center;
            gap: 3px;
            flex-shrink: 0;
        }

        .pe-btn-qty {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: #1f222e;
            border: 1px solid #33374a;
            color: var(--accent-gold);
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }

        .pe-btn-qty:hover {
            background: var(--accent-gold);
            color: #0c0d10;
        }

        .pe-qty-input {
            width: 38px;
            height: 26px;
            text-align: center;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 12px;
            font-weight: 700;
            font-family: monospace;
            outline: none;
            padding: 0 2px;
            box-sizing: border-box;
        }

        .pe-qty-input:focus {
            border-color: var(--accent-gold);
        }

        .pe-btn-remove {
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 15px;
            cursor: pointer;
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: color 0.15s;
        }

        .pe-btn-remove:hover {
            color: var(--danger-red);
        }

        .pe-cart-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--text-secondary);
            font-size: 12px;
            height: 260px;
            text-align: center;
            padding: 16px;
            box-sizing: border-box;
        }

        .pe-cart-empty i {
            font-size: 36px;
            color: #3b4056;
        }

        .pe-cart-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 12px;
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11.5px;
            color: var(--text-secondary);
            box-sizing: border-box;
        }

        .pe-cart-footer-val {
            font-size: 13px;
            font-weight: 700;
            color: var(--accent-gold);
            font-family: monospace;
        }
    </style>

    <div class="pe-layout">
        <!-- Panel Kiri: Katalog Pilihan Barang -->
        <div class="pe-catalog-panel">
            <div class="pe-search-bar">
                <i class="ph ph-magnifying-glass"></i>
                <input type="text" 
                       class="pe-search-input" 
                       x-model="search" 
                       placeholder="Cari nama peralatan, kursi, piring, lampu...">
            </div>

            <div class="pe-categories-scroll">
                <button type="button" 
                        class="pe-cat-chip" 
                        :class="{ 'active': filter === 'All' }" 
                        @click="filter = 'All'">
                    Semua Kategori ({{ $inventories->count() }})
                </button>
                @foreach($categories as $cat)
                    <button type="button" 
                            class="pe-cat-chip" 
                            :class="{ 'active': filter === '{{ $cat }}' }" 
                            @click="filter = '{{ $cat }}'">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

            <div class="pe-grid-items">
                @forelse($inventories as $inv)
                    @php
                        $invData = [
                            'id'       => $inv->id,
                            'name'     => $inv->nama,
                            'category' => $inv->kategori ?? 'Umum',
                            'unit'     => $inv->satuan ?? 'pcs',
                            'stock'    => $inv->stok_aktual ?? 0,
                        ];
                    @endphp
                    <div class="pe-item-card"
                         :class="{ 'is-selected': getItemQty({{ $inv->id }}) > 0 }"
                         x-show="(filter === 'All' || filter === '{{ $inv->kategori }}') && {{ \Illuminate\Support\Js::from(strtolower($inv->nama)) }}.includes(search.toLowerCase())"
                         @click="addItem({{ \Illuminate\Support\Js::from($invData) }})">
                        
                        <template x-if="getItemQty({{ $inv->id }}) > 0">
                            <div class="pe-selected-indicator" x-text="'x' + getItemQty({{ $inv->id }})"></div>
                        </template>

                        <div class="pe-item-cat">{{ $inv->kategori ?? 'Peralatan' }}</div>
                        <div class="pe-item-name">{{ $inv->nama }}</div>
                        <div class="pe-item-footer">
                            <span class="pe-badge-stock">Stok: {{ $inv->stok_aktual ?? 0 }} {{ $inv->satuan ?? 'pcs' }}</span>
                            <span style="color: var(--accent-gold); font-size: 13px;"><i class="ph ph-plus-circle"></i></span>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1/-1; text-align: center; color: var(--text-secondary); padding: 40px 0;">
                        Belum ada data barang di inventaris.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Panel Kanan: Keranjang Peralatan Dipilih -->
        <div class="pe-cart-panel">
            <div class="pe-cart-header">
                <div class="pe-cart-title">
                    <i class="ph ph-toolbox"></i>
                    <span>Peralatan Dipilih</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="pe-cart-count-badge" x-text="totalItemsCount + ' Jenis'"></span>
                    <button type="button" 
                            x-show="items.length > 0" 
                            @click="clearAll()" 
                            style="background: transparent; border: none; color: #e74c3c; font-size: 11px; cursor: pointer;">
                        Reset
                    </button>
                </div>
            </div>

            <!-- List Item Dipilih -->
            <div class="pe-cart-list" x-show="items.length > 0">
                <template x-for="(item, index) in items" :key="item.id">
                    <div class="pe-cart-item">
                        <div class="pe-cart-item-info">
                            <div class="pe-cart-item-name" x-text="item.name"></div>
                            <div class="pe-cart-item-sub">
                                <span x-text="item.category"></span> &bull; 
                                <span style="font-family: monospace;" x-text="'Stok: ' + item.stock + ' ' + item.unit"></span>
                            </div>
                        </div>

                        <div class="pe-qty-controls">
                            <button type="button" class="pe-btn-qty" @click="updateQty(index, -1)">-</button>
                            <input type="number" 
                                   class="pe-qty-input" 
                                   x-model.number="item.qty" 
                                   @input="setQtyDirect(index, $el.value)">
                            <button type="button" class="pe-btn-qty" @click="updateQty(index, 1)">+</button>
                            <button type="button" class="pe-btn-remove" @click="removeItem(index)" title="Hapus">
                                <i class="ph ph-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div class="pe-cart-empty" x-show="items.length === 0">
                <i class="ph ph-hand-pointing"></i>
                <div style="font-weight: 600; color: var(--text-primary);">Belum Ada Peralatan Dipilih</div>
                <div style="font-size: 11px; line-height: 1.4;">Klik kartu peralatan di katalog sebelah kiri untuk memasukkan barang.</div>
            </div>

            <!-- Summary Footer -->
            <div class="pe-cart-footer" x-show="items.length > 0">
                <span>Total Barang Disiapkan:</span>
                <span class="pe-cart-footer-val" x-text="totalQtyCount + ' Item (' + totalItemsCount + ' macam)'"></span>
            </div>
        </div>
    </div>
</div>
