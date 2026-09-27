<div class="pos-cart-container" 
     x-data="{
        items: [],
        diskon: 0,
        biayaTambahan: 0,
        
        get totalItems() {
            return this.items.reduce((sum, item) => sum + (parseInt(item.qty) || 0), 0);
        },
        get subtotal() {
            return this.items.reduce((sum, item) => sum + (item.price * (parseInt(item.qty) || 0)), 0);
        },
        get total() {
            return this.subtotal - this.diskon + this.biayaTambahan;
        },

        addItem(product) {
            const existing = this.items.find(i => i.id === product.id);
            if (existing) {
                existing.qty = (parseInt(existing.qty) || 0) + 1;
            } else {
                this.items.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    qty: 1
                });
            }
            this.syncFilamentFields();
        },

        updateQty(index, delta) {
            let current = parseInt(this.items[index].qty) || 0;
            this.items[index].qty = current + delta;
            if (this.items[index].qty <= 0) {
                this.items.splice(index, 1);
            }
            this.syncFilamentFields();
        },
        
        validateQty(index) {
            let parsed = parseInt(this.items[index].qty);
            if (isNaN(parsed) || parsed <= 0) {
                this.items[index].qty = 1;
                this.syncFilamentFields();
            }
        },
        
        updateDiskon(val) {
            this.diskon = parseFloat(val) || 0;
            this.syncFilamentFields();
        },
        
        updateBiaya(val) {
            this.biayaTambahan = parseFloat(val) || 0;
            this.syncFilamentFields();
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        },

        syncFilamentFields() {
            const cartInput = document.querySelector('input[wire\\:model$=\'.cart_data\']');
            if (cartInput) {
                cartInput.value = JSON.stringify(this.items);
                cartInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            
            const subtotalInput = document.querySelector('input[wire\\:model$=\'.subtotal\']');
            if (subtotalInput) {
                subtotalInput.value = this.subtotal;
                subtotalInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            const totalInput = document.querySelector('input[wire\\:model$=\'.total\']');
            if (totalInput) {
                totalInput.value = this.total;
                totalInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        },
        
        init() {
            setTimeout(() => {
                const dInput = document.querySelector('input[wire\\:model$=\'.diskon\']');
                const bInput = document.querySelector('input[wire\\:model$=\'.biaya_tambahan\']');
                if (dInput) this.diskon = parseFloat(dInput.value) || 0;
                if (bInput) this.biayaTambahan = parseFloat(bInput.value) || 0;
            }, 500);
        }
     }" 
     @add-to-cart.window="addItem($event.detail)" 
     @update-diskon.window="updateDiskon($event.detail)" 
     @update-biaya.window="updateBiaya($event.detail)">

    <style>
        .pos-cart-container {
            border-top: 1px solid var(--border-color);
            padding-top: 16px;
            margin-top: 16px;
        }
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            color: var(--accent-gold);
            font-size: 13px;
            font-weight: 600;
        }
        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
            max-height: 200px;
            overflow-y: auto;
        }
        .cart-items::-webkit-scrollbar { width: 4px; }
        .cart-items::-webkit-scrollbar-track { background: transparent; }
        .cart-items::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        .cart-item-info {
            display: flex;
            flex-direction: column;
        }
        .cart-item-name {
            font-size: 12px;
            color: var(--text-primary);
            font-weight: 500;
        }
        .cart-item-price {
            font-size: 11px;
            color: var(--text-secondary);
            font-family: monospace;
        }
        .cart-item-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cart-qty-btn {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            background: rgba(255,255,255,0.1);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .cart-qty-input::-webkit-outer-spin-button,
        .cart-qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .cart-qty-input {
            -moz-appearance: textfield;
            width: 36px;
            text-align: center;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            padding: 2px 0;
            outline: none;
        }
        .cart-qty-input:focus {
            border-color: var(--accent-gold);
        }
        .cart-summary {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px;
            background: rgba(0,0,0,0.2);
            border-radius: 8px;
        }
        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-secondary);
        }
        .cart-summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 600;
            color: var(--accent-gold);
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed rgba(255,255,255,0.1);
        }
        .cart-empty {
            text-align: center;
            padding: 24px 0;
            color: var(--text-secondary);
            font-size: 12px;
        }
    </style>

    <div class="cart-header">
        <div><i class="ph ph-shopping-cart" style="margin-right:6px"></i> Item Pesanan</div>
        <div x-text="totalItems + ' item'"></div>
    </div>

    <div class="cart-items" x-show="items.length > 0">
        <template x-for="(item, index) in items" :key="item.id">
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name" x-text="item.name"></div>
                    <div class="cart-item-price" x-text="'Rp ' + formatMoney(item.price)"></div>
                </div>
                <div class="cart-item-actions">
                    <button type="button" class="cart-qty-btn" @click="updateQty(index, -1)">-</button>
                    <input type="number" class="cart-qty-input" x-model.number="item.qty" @input="syncFilamentFields()" @blur="validateQty(index)">
                    <button type="button" class="cart-qty-btn" @click="updateQty(index, 1)">+</button>
                </div>
            </div>
        </template>
    </div>

    <div class="cart-empty" x-show="items.length === 0">
        Klik menu di samping untuk menambahkan.
    </div>

    <div class="cart-summary">
        <div class="cart-summary-row">
            <span>Subtotal</span>
            <span x-text="'Rp ' + formatMoney(subtotal)"></span>
        </div>
        <div class="cart-summary-row">
            <span>Diskon</span>
            <span x-text="'- Rp ' + formatMoney(diskon)"></span>
        </div>
        <div class="cart-summary-row">
            <span>Biaya lain</span>
            <span x-text="'+ Rp ' + formatMoney(biayaTambahan)"></span>
        </div>
        <div class="cart-summary-total">
            <span>TOTAL</span>
            <span x-text="'Rp ' + formatMoney(total)"></span>
        </div>
    </div>
</div>
