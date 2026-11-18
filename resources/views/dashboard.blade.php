@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
	@php($maxSales = max(1, (float) $salesChart->max('total')))

	<section class="dashboard-page">
		<div class="dashboard-heading">
			<div>
				<p class="dashboard-eyebrow">RINGKASAN OPERASIONAL</p>
				<!-- <h1 class="h3 mb-1">Selamat datang kembali</h1> -->
				<p class="text-secondary mb-0">Pantau aktivitas Ayam Geprek Istiqomah hari ini.</p>
			</div>
			<a class="btn btn-pos-primary dashboard-primary-action" href="{{ route('transactions.index') }}">
				<span aria-hidden="true">＋</span> Buka kasir
			</a>
		</div>

		<div class="dashboard-quick-links" aria-label="Aksi cepat">
			<a href="{{ route('stocks.index') }}"><span class="quick-link-icon quick-link-stock" aria-hidden="true">⇄</span><span><strong>Catat stok</strong><small>Tambah bahan baku</small></span><span class="quick-link-arrow" aria-hidden="true">→</span></a>
			<a href="{{ route('products.index') }}"><span class="quick-link-icon quick-link-menu" aria-hidden="true">▤</span><span><strong>Kelola menu</strong><small>Atur katalog produk</small></span><span class="quick-link-arrow" aria-hidden="true">→</span></a>
			<a href="{{ route('reports.transactions') }}"><span class="quick-link-icon quick-link-report" aria-hidden="true">▥</span><span><strong>Lihat laporan</strong><small>Periksa transaksi</small></span><span class="quick-link-arrow" aria-hidden="true">→</span></a>
		</div>

		<div class="dashboard-stats">
			<article class="dashboard-stat stat-sales">
				<div class="stat-topline"><span>Penjualan hari ini</span><span class="stat-icon" aria-hidden="true">Rp</span></div>
				<strong>Rp {{ number_format((float) $todaySales, 0, ',', '.') }}</strong>
				<small>Total pendapatan masuk</small>
			</article>
			<article class="dashboard-stat stat-transactions">
				<div class="stat-topline"><span>Transaksi hari ini</span><span class="stat-icon" aria-hidden="true">↗</span></div>
				<strong>{{ number_format($todayTransactions, 0, ',', '.') }}</strong>
				<small>nota tercatat</small>
			</article>
			<article class="dashboard-stat stat-products">
				<div class="stat-topline"><span>Menu tersedia</span><span class="stat-icon" aria-hidden="true">▤</span></div>
				<strong>{{ number_format($productCount, 0, ',', '.') }}</strong>
				<small>produk dalam katalog</small>
			</article>
			<article class="dashboard-stat {{ $lowStockCount > 0 ? 'stat-alert' : 'stat-stock' }}">
				<div class="stat-topline"><span>Perlu restok</span><span class="stat-icon" aria-hidden="true">!</span></div>
				<strong>{{ number_format($lowStockCount, 0, ',', '.') }}</strong>
				<small>bahan di bawah stok minimum</small>
			</article>
		</div>

		<div class="dashboard-main-grid">
			<section class="dashboard-panel sales-panel" aria-labelledby="sales-title">
				<div class="dashboard-panel-heading">
					<div>
						<h2 id="sales-title">Tren penjualan</h2>
						<p>Performa selama 7 hari terakhir</p>
					</div>
					<span class="sales-period">7 HARI</span>
				</div>
				<div class="sales-chart" role="img" aria-label="Grafik penjualan tujuh hari terakhir">
					@foreach ($salesChart as $day)
						<div class="sales-chart-column" title="{{ $day['date'] }}: Rp {{ number_format($day['total'], 0, ',', '.') }}">
							<span class="sales-chart-value">{{ $day['total'] > 0 ? number_format($day['total'] / 1000, 0, ',', '.').'k' : '' }}</span>
							<div class="sales-chart-track">
								<span class="sales-chart-bar {{ $day['date'] === now()->toDateString() ? 'is-today' : '' }}" style="height: {{ $day['total'] > 0 ? max(8, ($day['total'] / $maxSales) * 100) : 3 }}%"></span>
							</div>
							<span class="sales-chart-label">{{ $day['label'] }}</span>
						</div>
					@endforeach
				</div>
				<div class="sales-chart-footer"><span><i aria-hidden="true"></i> Penjualan per hari</span><a href="{{ route('reports.transactions') }}">Buka laporan <span aria-hidden="true">→</span></a></div>
			</section>

			<section class="dashboard-panel stock-panel" aria-labelledby="stock-title">
				<div class="dashboard-panel-heading">
					<div>
						<h2 id="stock-title">Pantau stok</h2>
						<p>Bahan yang perlu diperhatikan</p>
					</div>
					<a class="panel-text-link" href="{{ route('ingredients.index') }}">Semua bahan <span aria-hidden="true">→</span></a>
				</div>
				@forelse ($lowStockIngredients as $ingredient)
					<div class="stock-alert-row">
						<span class="stock-alert-mark" aria-hidden="true">!</span>
						<span class="stock-alert-name"><strong>{{ $ingredient->nama_bahan }}</strong><small>Minimum {{ $ingredient->stok_minimum }} {{ $ingredient->satuan }}</small></span>
						<span class="stock-alert-amount">{{ $ingredient->stok_saat_ini }} <small>{{ $ingredient->satuan }}</small></span>
					</div>
				@empty
					<div class="dashboard-empty stock-empty"><span aria-hidden="true">✓</span><strong>Stok aman</strong><small>Belum ada bahan yang perlu direstok.</small></div>
				@endforelse
				@if ($lowStockCount > 0)
					<a class="stock-action" href="{{ route('stocks.index') }}">Catat stok masuk <span aria-hidden="true">→</span></a>
				@endif
			</section>
		</div>

		<section class="dashboard-panel recent-panel" aria-labelledby="recent-title">
			<div class="dashboard-panel-heading">
				<div>
					<h2 id="recent-title">Transaksi terbaru</h2>
					<p>Aktivitas penjualan yang baru tercatat</p>
				</div>
				<a class="panel-text-link" href="{{ route('transaction-history.index') }}">Riwayat transaksi <span aria-hidden="true">→</span></a>
			</div>
			@if ($recentTransactions->isEmpty())
				<div class="dashboard-empty recent-empty">
					<!-- <span aria-hidden="true">＋</span> -->
					<strong>Belum ada transaksi</strong><small>Transaksi baru akan muncul di sini.</small></div>
			@else
				<div class="table-responsive">
					<table class="table dashboard-table mb-0">
						<thead><tr><th>Nomor nota</th><th>Waktu</th><th>Item</th><th class="text-end">Total</th><th class="text-end">Status</th></tr></thead>
						<tbody>
							@foreach ($recentTransactions as $transaction)
								<tr>
									<td><strong>{{ $transaction->nomor_struk }}</strong></td>
									<td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
									<td>{{ $transaction->items_count }} menu</td>
									<td class="text-end fw-bold">Rp {{ number_format((float) $transaction->total_bayar, 0, ',', '.') }}</td>
									<td class="text-end"><span class="transaction-status">Selesai</span></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
		</section>
	</section>
@endsection
