@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">KASIR</p>
            <h1 class="h3 fw-bold mb-1">Transaksi Penjualan</h1>
            <p class="text-secondary mb-0">Pilih menu, hitung total, dan catat uang tunai yang diterima.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Data transaksi belum lengkap.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <section class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 fw-bold mb-0">Menu tersedia</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse ($products as $product)
                            <div class="col-12 col-sm-6 col-lg-4">
                                <article class="card h-100 border-0 shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <h3 class="h6 fw-bold mb-0">{{ $product->nama_menu }}</h3>
                                            <span class="badge bg-danger-subtle text-danger">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</span>
                                        </div>
                                        <p class="small text-secondary mb-2">
                                            @if ($product->ingredient_id && $product->jumlah_potong > 0)
                                                Dipotong {{ $product->jumlah_potong }} {{ $product->ingredient?->satuan ?? 'unit' }} dari {{ $product->ingredient?->nama_bahan ?? 'bahan utama' }}
                                            @else
                                                Tidak memotong stok bahan pokok
                                            @endif
                                        </p>
                                        <button class="btn btn-danger mt-auto" type="button" data-add-product data-product-id="{{ $product->id }}" data-product-name="{{ $product->nama_menu }}" data-product-price="{{ $product->harga_jual }}">
                                            + Tambah menu
                                        </button>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-secondary mb-0">Belum ada menu yang dapat dijual.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-4">
            <section class="card shadow-sm sticky-xl-top" style="top: 90px;">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 fw-bold mb-0">Keranjang</h2>
                </div>
                <div class="card-body">
                    <form id="transaction-form" method="POST" action="{{ route('transactions.store') }}">
                        @csrf
                        <div id="cart-items" class="d-grid gap-2 mb-3"></div>
                        <div id="cart-inputs"></div>

                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between small text-secondary mb-2">
                                <span>Total</span>
                                <strong id="cart-total">Rp 0</strong>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nominal_diterima">Uang diterima</label>
                                <input class="form-control" id="nominal_diterima" name="nominal_diterima" type="number" min="0" step="100" placeholder="50000" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small text-secondary">Kembalian</span>
                                <strong id="change-display">Rp 0</strong>
                            </div>
                            <button class="btn btn-danger w-100" type="submit">Selesaikan transaksi</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const cart = new Map();

        const formatMoney = (value) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }).format(value);
        };

        const renderCart = () => {
            const cartItems = document.querySelector('#cart-items');
            const cartInputs = document.querySelector('#cart-inputs');
            const totalDisplay = document.querySelector('#cart-total');
            const changeDisplay = document.querySelector('#change-display');
            const nominalInput = document.querySelector('#nominal_diterima');

            cartItems.innerHTML = '';
            cartInputs.innerHTML = '';

            let total = 0;
            let index = 0;

            for (const [productId, item] of cart.entries()) {
                total += item.price * item.qty;

                const row = document.createElement('div');
                row.className = 'border rounded p-2';
                row.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <strong class="d-block">${item.name}</strong>
                            <small class="text-secondary">${formatMoney(item.price)} × ${item.qty}</small>
                        </div>
                        <strong>${formatMoney(item.price * item.qty)}</strong>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-change-qty="decrease" data-product-id="${productId}">−</button>
                        <span class="small fw-semibold">${item.qty}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-change-qty="increase" data-product-id="${productId}">＋</button>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-auto" data-remove-product="${productId}">Hapus</button>
                    </div>
                `;
                cartItems.appendChild(row);

                const productInput = document.createElement('input');
                productInput.type = 'hidden';
                productInput.name = `items[${index}][product_id]`;
                productInput.value = productId;
                cartInputs.appendChild(productInput);

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `items[${index}][qty]`;
                qtyInput.value = item.qty;
                cartInputs.appendChild(qtyInput);

                index += 1;
            }

            totalDisplay.textContent = formatMoney(total);

            const nominalValue = Number(nominalInput.value || 0);
            changeDisplay.textContent = formatMoney(Math.max(0, nominalValue - total));

            if (cart.size === 0) {
                cartItems.innerHTML = '<div class="border rounded p-3 text-secondary small">Keranjang masih kosong. Pilih menu terlebih dahulu.</div>';
            }
        };

        const addProduct = (productId, productName, productPrice) => {
            if (!cart.has(productId)) {
                cart.set(productId, {
                    name: productName,
                    price: Number(productPrice),
                    qty: 1,
                });
            } else {
                cart.get(productId).qty += 1;
            }

            renderCart();
        };

        document.querySelectorAll('[data-add-product]').forEach((button) => {
            button.addEventListener('click', () => {
                addProduct(button.dataset.productId, button.dataset.productName, button.dataset.productPrice);
            });
        });

        document.addEventListener('click', (event) => {
            const target = event.target;

            if (target.matches('[data-change-qty]')) {
                const productId = target.dataset.productId;
                const action = target.dataset.changeQty;
                const item = cart.get(productId);

                if (!item) return;

                if (action === 'increase') {
                    item.qty += 1;
                }

                if (action === 'decrease') {
                    item.qty -= 1;
                    if (item.qty <= 0) {
                        cart.delete(productId);
                    }
                }

                renderCart();
            }

            if (target.matches('[data-remove-product]')) {
                const productId = target.dataset.removeProduct;
                cart.delete(productId);
                renderCart();
            }
        });

        document.querySelector('#nominal_diterima').addEventListener('input', renderCart);

        document.querySelector('#transaction-form').addEventListener('submit', (event) => {
            if (cart.size === 0) {
                event.preventDefault();
                alert('Pilih minimal satu menu sebelum menyelesaikan transaksi.');
                return;
            }

            const total = [...cart.values()].reduce((sum, item) => sum + (item.price * item.qty), 0);
            const nominal = Number(document.querySelector('#nominal_diterima').value || 0);

            if (nominal < total) {
                event.preventDefault();
                alert('Uang yang diterima masih kurang dari total pembayaran.');
            }
        });

        renderCart();
    </script>
@endpush
