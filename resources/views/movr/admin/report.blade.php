@extends('movr.layouts.admin')

@section('content')
<section class="space-y-8">
    <div class="rounded-[2rem] bg-black p-8 text-white shadow-2xl">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-gray-400">MOVR Finance</p>
                <h2 class="mt-3 text-3xl font-heading font-bold uppercase tracking-tight sm:text-4xl">Laporan Pendapatan</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-300">Ringkasan performa transaksi dan pendapatan yang mengikuti visual brand MOVR.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-right backdrop-blur">
                <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Total Akumulasi</p>
                <p class="mt-1 text-3xl font-heading font-bold text-accent-green">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-[2rem] border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-xl font-heading font-bold uppercase tracking-tight text-black">Grafik Pertumbuhan Pendapatan</h3>
        <div class="h-[320px]">
            @if($revenueData->count() > 0)
                <canvas id="revenueChart"></canvas>
            @else
                <div class="flex h-full items-center justify-center rounded-2xl bg-gray-50">
                    <p class="text-gray-500">Belum ada data pendapatan untuk ditampilkan</p>
                </div>
            @endif
        </div>
    </div>
            <section class="space-y-8">
                <div class="rounded-[2rem] bg-black px-6 py-8 text-white shadow-2xl shadow-black/10 sm:px-8">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-gray-400">Financial Report</p>
                            <h2 class="mt-3 text-3xl font-heading font-bold tracking-tight sm:text-4xl">Laporan Pendapatan MOVR</h2>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-300">Ringkasan performa transaksi yang mengikuti warna dan gaya visual homepage MOVR.</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 px-5 py-4 text-right backdrop-blur-sm">
                            <p class="text-xs uppercase tracking-[0.3em] text-gray-300">Total Akumulasi</p>
                            <p class="mt-2 text-3xl font-bold text-accent-green">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <section class="space-y-8">
                    <div class="rounded-[2rem] border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-black">Grafik Pertumbuhan Pendapatan</h3>
                                <p class="text-sm text-gray-500">Tren pendapatan bulanan</p>
                            </div>
                        </div>
                        <div style="height: 320px;">
                            @if($revenueData->count() > 0)
                                <canvas id="revenueChart"></canvas>
                            @else
                                <div class="flex h-full items-center justify-center rounded-2xl bg-gray-50">
                                    <p class="text-gray-500">Belum ada data pendapatan untuk ditampilkan</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-black">Catatan Transaksi Selesai</h3>
                            <p class="text-sm text-gray-500">Daftar transaksi yang sudah dibayar</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-sm font-bold text-gray-700">ID Pesanan</th>
                                        <th class="px-6 py-4 text-sm font-bold text-gray-700">Pelanggan</th>
                                        <th class="px-6 py-4 text-sm font-bold text-gray-700">Tanggal</th>
                                        <th class="px-6 py-4 text-sm font-bold text-gray-700">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($incomeRecords as $record)
                                        <tr class="transition hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-medium text-black">#{{ $record->midtrans_order_id ?? $record->id }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ $record->user->name ?? 'Unknown' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $record->created_at->format('d M Y, H:i') }}</td>
                                            <td class="px-6 py-4 text-sm font-bold text-accent-green">Rp {{ number_format($record->total_amount ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                                Belum ada transaksi selesai
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($incomeRecords->count() > 0)
                            <div class="border-t border-gray-200 p-4">
                                {{ $incomeRecords->links() }}
                            </div>
                        @endif
                    </div>
                </section>
            </section>
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
    @endif
</script>
@endsection