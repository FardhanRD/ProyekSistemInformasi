@php
    $produk = $product ?? $produk ?? null;
    $ratingStats = $ratingStats ?? [
        'average' => round((float) ($produk->rata_rating ?? $produk?->average_rating ?? 0), 1),
        'total' => (int) ($produk->jumlah_ulasan ?? $produk?->review_count ?? 0),
        'distribution' => [],
    ];
    $ratingDistribution = $ratingDistribution ?? ($ratingStats['distribution'] ?? []);
    $ratings = $ratings ?? ($produk ? $produk->ratings()->with('buyer.pengguna')->latest()->paginate(5) : collect());
    $averageRating = (float) ($ratingStats['average'] ?? 0);
    $totalReviews = (int) ($ratingStats['total'] ?? 0);
@endphp

<div>
    {{-- Statistik Rating --}}
    <div class="mb-5 rounded-3xl bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="flex flex-col items-center justify-center border-r border-gray-100 text-center md:px-2">
                <span class="text-6xl font-black text-gray-800">{{ number_format($averageRating, 1) }}</span>
                <div class="mt-2 flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="h-5 w-5 {{ $i <= round($averageRating) ? 'fill-amber-400 text-amber-400' : 'fill-gray-200 text-gray-200' }}" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="mt-1 text-sm text-gray-400">dari {{ $totalReviews }} ulasan</p>
            </div>

            <div class="space-y-2 md:col-span-2">
                @foreach([5,4,3,2,1] as $star)
                    @php
                        $count = $ratingDistribution[$star] ?? 0;
                        $pct = $totalReviews > 0 ? round($count / $totalReviews * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="flex w-16 items-center justify-end gap-0.5">
                            <span class="text-sm font-medium text-gray-600">{{ $star }}</span>
                            <svg class="h-3.5 w-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="h-2.5 flex-1 rounded-full bg-gray-100">
                            <div class="h-2.5 rounded-full bg-amber-400 transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="w-8 text-right text-xs text-gray-400">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- List Ulasan --}}
    <div class="space-y-4">
        @forelse($ratings as $ulasan)
            @php
                $buyerName = $ulasan->buyer?->pengguna?->nama_pengguna ?? 'Anonymous';
                $avatar = strtoupper(mb_substr($buyerName, 0, 1));
            @endphp
            <div class="rounded-3xl bg-white p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-[#63A2BB] text-sm font-bold text-white">
                        {{ $avatar }}
                    </div>

                    <div class="flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $buyerName }}</p>
                                <div class="mt-0.5 flex items-center gap-1.5">
                                    <div class="flex gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-3 w-3 {{ $i <= $ulasan->bintang ? 'fill-amber-400 text-amber-400' : 'fill-gray-200 text-gray-200' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    @if($ulasan->is_verified)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-700">✓ Verified Purchase</span>
                                    @endif
                                </div>
                            </div>
                            <span class="flex-shrink-0 text-xs text-gray-400">{{ $ulasan->created_at?->isoFormat('D MMM YYYY') ?? '-' }}</span>
                        </div>

                        @if($ulasan->judul_ulasan)
                            <p class="mt-2 text-sm font-semibold text-gray-700">{{ $ulasan->judul_ulasan }}</p>
                        @endif

                        @if($ulasan->isi_ulasan)
                            <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ $ulasan->isi_ulasan }}</p>
                        @endif

                        @if($ulasan->foto_ulasan)
                            @php
                                $fotos = is_array($ulasan->foto_ulasan) ? $ulasan->foto_ulasan : json_decode($ulasan->foto_ulasan, true);
                            @endphp
                            @if($fotos)
                                <div class="mt-3 flex gap-2">
                                    @foreach((array) $fotos as $foto)
                                        <img src="{{ $foto }}" class="h-16 w-16 cursor-pointer rounded-xl object-cover transition hover:opacity-90" alt="Foto ulasan">
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white p-12 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-50">
                    <span class="text-3xl">⭐</span>
                </div>
                <p class="font-semibold text-gray-500">Belum ada ulasan</p>
                <p class="mt-1 text-sm text-gray-400">Jadilah yang pertama mengulas produk ini!</p>
            </div>
        @endforelse

        @if(isset($ratings) && method_exists($ratings, 'links'))
            <div class="mt-4">
                {{ $ratings->links() }}
            </div>
        @endif
    </div>

    @if($hasPurchased && !$hasReviewed)
        <div class="mt-8 border-t pt-8" x-data="reviewFormComponent()">
            <template x-if="showReviewForm">
                <form @submit.prevent="submitReview()" class="rounded-2xl bg-gray-50 p-6" enctype="multipart/form-data">
                    <h3 class="mb-6 text-lg font-semibold text-gray-900">Tulis Ulasan Anda</h3>

                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-semibold text-gray-700">Rating</label>
                        <div class="flex gap-2">
                            <template x-for="star in [1,2,3,4,5]" :key="star">
                                <button type="button" @click="selectedRating = star" :class="{'text-4xl text-amber-400': star <= selectedRating, 'text-4xl text-gray-300': star > selectedRating}" class="cursor-pointer transition hover:scale-110" x-text="'★'"></button>
                            </template>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-semibold text-gray-700">Judul Ulasan</label>
                        <input type="text" name="judul_ulasan" x-model="formData.judul_ulasan" placeholder="Contoh: Kualitas bagus, pengiriman cepat" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" required>
                    </div>

                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-semibold text-gray-700">Isi Ulasan</label>
                        <textarea name="isi_ulasan" x-model="formData.isi_ulasan" placeholder="Bagikan pengalaman Anda dengan produk ini..." rows="5" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" required></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-semibold text-gray-700">Foto (Maks 3 file, JPG/PNG/WEBP)</label>
                        <input type="file" name="foto_ulasan[]" @change="handlePhotoUpload($event)" multiple accept="image/jpeg,image/png,image/webp,.webp" class="w-full rounded-lg border border-gray-300 px-4 py-2">
                        <div class="mt-3 flex flex-wrap gap-2" x-show="uploadedPhotos.length > 0">
                            <template x-for="(photo, idx) in uploadedPhotos" :key="idx">
                                <div class="relative">
                                    <img :src="photo.preview" :alt="'Photo ' + (idx + 1)" class="h-20 w-20 rounded border object-cover">
                                    <button type="button" @click="removePhoto(idx)" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white">×</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <template x-if="errorMessage">
                        <div class="mb-4 rounded bg-red-100 p-4 text-red-700">
                            <p x-text="errorMessage"></p>
                        </div>
                    </template>

                    <div class="flex gap-3">
                        <button type="submit" :disabled="isSubmitting" :class="{'cursor-not-allowed opacity-50': isSubmitting}" class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700">
                            <span x-show="!isSubmitting">Kirim Ulasan</span>
                            <span x-show="isSubmitting">Mengirim...</span>
                        </button>
                        <button type="button" @click="showReviewForm = false" class="rounded-lg border border-gray-300 px-6 py-2 font-semibold transition hover:bg-gray-50">Batal</button>
                    </div>
                </form>
            </template>

            <template x-if="!showReviewForm">
                <button @click="showReviewForm = true" class="rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700">Tulis Ulasan</button>
            </template>
        </div>
    @endif
</div>

<script>
    function reviewFormComponent() {
        return {
            showReviewForm: false,
            selectedRating: 0,
            formData: {
                judul_ulasan: '',
                isi_ulasan: ''
            },
            uploadedPhotos: [],
            errorMessage: '',
            isSubmitting: false,

            handlePhotoUpload(event) {
                const files = Array.from(event.target.files);
                if (this.uploadedPhotos.length + files.length > 3) {
                    this.errorMessage = 'Maksimal 3 foto';
                    return;
                }

                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.uploadedPhotos.push({
                            file: file,
                            preview: e.target.result
                        });
                    };
                    reader.readAsDataURL(file);
                });
            },

            removePhoto(idx) {
                this.uploadedPhotos.splice(idx, 1);
            },

            async submitReview() {
                this.errorMessage = '';

                if (this.selectedRating === 0) {
                    this.errorMessage = 'Pilih rating terlebih dahulu';
                    return;
                }

                if (!this.formData.judul_ulasan.trim()) {
                    this.errorMessage = 'Judul ulasan wajib diisi';
                    return;
                }

                if (!this.formData.isi_ulasan.trim()) {
                    this.errorMessage = 'Isi ulasan wajib diisi';
                    return;
                }

                this.isSubmitting = true;

                const formData = new FormData();
                formData.append('produk_id', {{ $produk?->produk_id ?? 0 }});
                formData.append('bintang', this.selectedRating);
                formData.append('judul_ulasan', this.formData.judul_ulasan);
                formData.append('isi_ulasan', this.formData.isi_ulasan);

                this.uploadedPhotos.forEach(photo => {
                    formData.append('foto_ulasan[]', photo.file);
                });

                try {
                    const response = await fetch('/api/review/store', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('✓ Ulasan berhasil ditambahkan');
                        window.location.reload();
                    } else {
                        this.errorMessage = data.message;
                    }
                } catch (error) {
                    this.errorMessage = 'Terjadi kesalahan: ' + error.message;
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }
</script>