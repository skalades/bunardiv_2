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
            border-top: 1px solid #222532;
            padding-top: 16px;
            margin-top: 16px;
        }
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            color: #f1f2f6;
            font-size: 14px;
            font-weight: 600;
        }
        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
            max-height: 240px;
            overflow-y: auto;
        }
        .cart-items::-webkit-scrollbar { width: 4px; }
        .cart-items::-webkit-scrollbar-track { background: transparent; }
        .cart-items::-webkit-scrollbar-thumb { background: #333644; border-radius: 4px; }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            background: #181922;
            border: 1px solid #262938;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2);
            transition: border-color 0.15s ease;
        }
        .cart-item:hover {
            border-color: #cfa24b;
        }
        .cart-item-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .cart-item-name {
            font-size: 13px;
            color: #f1f2f6;
            font-weight: 600;
        }
        .cart-item-price {
            font-size: 12px;
            color: #cfa24b;
            font-family: monospace;
            font-weight: 500;
        }
        .cart-item-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .cart-qty-btn {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: #232634;
            color: #cfa24b;
            border: 1px solid #33374a;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.15s;
        }
        .cart-qty-btn:hover {
            background: #cfa24b;
            color: #0c0d10;
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
            background: #111217;
            border: 1px solid #2d3040;
            color: #f1f2f6;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 0;
            outline: none;
        }
        .cart-qty-input:focus {
            border-color: #cfa24b;
        }
        .cart-summary {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px;
            background: #181922;
            border-radius: 8px;
            border: 1px solid #262938;
        }
        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #9299ab;
        }
        .cart-summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            font-weight: 700;
            color: #cfa24b;
            margin-top: 6px;
            padding-top: 10px;
            border-top: 1px dashed #2d3040;
        }
        .cart-empty {
            text-align: center;
            padding: 24px 0;
            color: #717789;
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
