<div class="pos-menu-container" x-data="{ filter: 'All', search: '' }">
    <style>
        .pos-menu-container {
            --bg-card: #151618;
            --text-primary: #f0f0f0;
            --text-secondary: #888888;
            --accent-gold: #cfa24b;
            --border-color: #262626;
        }
        .pos-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; }
        .pos-filters { display: flex; gap: 8px; }
        .pos-filter-btn { padding: 8px 16px; border-radius: 100px; font-size: 12px; font-weight: 500; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: var(--text-secondary); cursor: pointer; transition: all 0.2s; }
        .pos-filter-btn.active { background: var(--accent-gold); color: #000; border-color: var(--accent-gold); }
        .pos-search { flex-grow: 1; max-width: 300px; position: relative; }
        .pos-search input { width: 100%; background: transparent; border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 16px 8px 36px; color: var(--text-primary); font-size: 13px; outline: none; }
        .pos-search input:focus { border-color: var(--accent-gold); }
        .pos-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }
        
        .pos-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; max-height: 600px; overflow-y: auto; padding-right: 8px; }
        @media (max-width: 1024px) { .pos-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { 
            .pos-grid { grid-template-columns: 1fr; } 
            .pos-header { flex-direction: column; align-items: stretch; }
            .pos-search { max-width: 100%; } 
            .pos-filters { flex-wrap: wrap; }
        }
        
        .pos-grid::-webkit-scrollbar { width: 4px; }
        .pos-grid::-webkit-scrollbar-track { background: transparent; }
        .pos-grid::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        
        .pos-item { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; cursor: pointer; transition: all 0.2s; display: flex; flex-direction: column; gap: 8px; }
        .pos-item:hover { border-color: var(--accent-gold); transform: translateY(-2px); }
        .pos-item-category { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: var(--accent-gold); font-weight: 600; }
        .pos-item-name { font-size: 14px; font-weight: 500; color: var(--text-primary); line-height: 1.3; }
        .pos-item-price { font-size: 15px; font-family: monospace; color: var(--accent-gold); font-weight: 600; margin-top: auto; }
    </style>

    <div class="pos-header">
        <div class="pos-filters">
            <button type="button" class="pos-filter-btn" :class="{ 'active': filter === 'All' }" @click="filter = 'All'">All</button>
            <button type="button" class="pos-filter-btn" :class="{ 'active': filter === 'CATERING' }" @click="filter = 'CATERING'">Catering</button>
            <button type="button" class="pos-filter-btn" :class="{ 'active': filter === 'DECORATION' }" @click="filter = 'DECORATION'">Decoration</button>
        </div>
        <div class="pos-search">
            <i class="ph ph-magnifying-glass"></i>
            <input type="text" x-model="search" placeholder="Cari menu...">
        </div>
    </div>

    @php
        $products = \App\Models\Product::all();
    @endphp

    <div class="pos-grid">
        @foreach($products as $p)
        <div class="pos-item" 
             x-show="(filter === 'All' || filter === '{{ strtoupper($p->kategori) }}') && {{ \Illuminate\Support\Js::from(strtolower($p->nama)) }}.includes(search.toLowerCase())"
             @click="$dispatch('add-to-cart', { id: {{ $p->id }}, name: {{ \Illuminate\Support\Js::from($p->nama) }}, price: {{ $p->harga }} })">
            <div class="pos-item-category">{{ $p->kategori }}</div>
            <div class="pos-item-name">{{ $p->nama }}</div>
            <div class="pos-item-price">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
        </div>
        @endforeach
    </div>
</div>
