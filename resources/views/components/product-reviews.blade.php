<!-- Product Reviews Component -->
<div>
    <!-- Rating Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 py-6 border-b">
        <!-- Left: Average Rating -->
        <div class="flex flex-col items-center justify-center">
            <div class="text-5xl font-bold text-gray-900">{{ $ratingStats['average'] }}</div>
            <div class="flex gap-1 my-2">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($ratingStats['average']))
                        <span class="text-2xl text-yellow-400">★</span>
                    @elseif($i - 0.5 <= $ratingStats['average'])
                        <span class="text-2xl text-yellow-400">⭐</span>
                    @else
                        <span class="text-2xl text-gray-300">☆</span>
                    @endif
                @endfor
            </div>
            <div class="text-gray-600 text-sm">Dari {{ $ratingStats['total'] }} ulasan</div>
        </div>

        <!-- Middle: Rating Distribution -->
        <div class="flex flex-col gap-3">
            @for($rating = 5; $rating >= 1; $rating--)
                @php
                    $count = $ratingStats['distribution'][$rating] ?? 0;
                    $percentage = $ratingStats['total'] > 0 ? round(($count / $ratingStats['total']) * 100) : 0;
                @endphp
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-700 w-6">{{ $rating }}★</span>
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <span class="text-xs text-gray-600 w-8 text-right">{{ $percentage }}%</span>
                </div>
            @endfor
        </div>

        <!-- Right: Review Button -->
        @if($hasPurchased && !$hasReviewed)
            <div class="flex items-center justify-center">
                <button @click="showReviewForm = true" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Tulis Ulasan
                </button>
            </div>
        @endif
    </div>

    <!-- Reviews List -->
    <div id="reviews-list" class="space-y-6 mb-8">
        @php
            $reviews = $product->ratings()->with('buyer.pengguna')->latest()->paginate(5);
        @endphp

        @forelse($reviews as $review)
            <div class="border-b pb-6 flex gap-4">
                <!-- Buyer Avatar -->
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center text-blue-700 font-bold">
                        {{ substr($review->buyer->pengguna->nama ?? 'User', 0, 1) }}
                    </div>
                </div>

                <!-- Review Content -->
                <div class="flex-1">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $review->buyer->pengguna->nama ?? 'Pembeli' }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->bintang)
                                            <span class="text-yellow-400">★</span>
                                        @else
                                            <span class="text-gray-300">☆</span>
                                        @endif
                                    @endfor
                                </div>
                                @if($review->is_verified)
                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-semibold">✓ Verified Purchase</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $review->created_at ? \Carbon\Carbon::parse($review->created_at)->format('d M Y') : 'Unknown' }}
                        </div>
                    </div>

                    <!-- Review Title and Content -->
                    @if($review->judul_ulasan)
                        <h4 class="font-semibold text-gray-900 mb-2">{{ $review->judul_ulasan }}</h4>
                    @endif
                    <p class="text-gray-700 mb-3">{{ $review->isi_ulasan }}</p>

                    <!-- Review Photos -->
                    @if($review->foto_ulasan && is_array($review->foto_ulasan) && count($review->foto_ulasan) > 0)
                        <div class="flex gap-2 mb-3">
                            @foreach($review->foto_ulasan as $foto)
                                <img src="{{ asset('storage/' . $foto) }}" alt="Review photo" class="w-20 h-20 object-cover rounded border">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-8">Belum ada ulasan untuk produk ini</p>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
        <div class="flex justify-center gap-2 mb-8">
            {{ $reviews->links() }}
        </div>
    @endif

    <!-- Review Form -->
    @if($hasPurchased && !$hasReviewed)
        <div x-data="reviewFormComponent()" class="mt-8 pt-8 border-t">
            <template x-if="showReviewForm">
                <form @submit.prevent="submitReview()" class="bg-gray-50 p-6 rounded-lg" enctype="multipart/form-data">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Tulis Ulasan Anda</h3>

                    <!-- Rating Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Rating</label>
                        <div class="flex gap-2">
                            <template x-for="star in [1,2,3,4,5]" :key="star">
                                <button type="button" @click="selectedRating = star"
                                        :class="{'text-yellow-400 text-4xl': star <= selectedRating, 'text-gray-300 text-4xl': star > selectedRating}"
                                        class="transition hover:scale-110 cursor-pointer"
                                        x-text="'★'">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Title Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Ulasan</label>
                        <input type="text" name="judul_ulasan" x-model="formData.judul_ulasan" placeholder="Contoh: Kualitas bagus, pengiriman cepat"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <!-- Content Textarea -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Ulasan</label>
                        <textarea name="isi_ulasan" x-model="formData.isi_ulasan" placeholder="Bagikan pengalaman Anda dengan produk ini..."
                                  rows="5"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  required></textarea>
                    </div>

                    <!-- Photo Upload -->
                    <div class="mb-6">
                           <label class="block text-sm font-semibold text-gray-700 mb-2">Foto (Maks 3 file, JPG/PNG/WEBP)</label>
                           <input type="file" name="foto_ulasan[]" @change="handlePhotoUpload($event)" multiple accept="image/jpeg,image/png,image/webp,.webp"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <div class="mt-3 flex gap-2 flex-wrap" x-show="uploadedPhotos.length > 0">
                            <template x-for="(photo, idx) in uploadedPhotos" :key="idx">
                                <div class="relative">
                                    <img :src="photo.preview" :alt="'Photo ' + (idx + 1)" class="w-20 h-20 object-cover rounded border">
                                    <button type="button" @click="removePhoto(idx)"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                                        ×
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <template x-if="errorMessage">
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <p x-text="errorMessage"></p>
                        </div>
                    </template>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" :disabled="isSubmitting" 
                                :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                            <span x-show="!isSubmitting">Kirim Ulasan</span>
                            <span x-show="isSubmitting">Mengirim...</span>
                        </button>
                        <button type="button" @click="showReviewForm = false"
                                class="px-6 py-2 border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </template>

            <template x-if="!showReviewForm">
                <button @click="showReviewForm = true"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Tulis Ulasan
                </button>
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
                formData.append('produk_id', {{ $product->produk_id }});
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
