@extends('movr.layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-semibold">Product Variants</h1>
            <p class="text-gray-500 mt-1">Add different versions of your product such as sizes and colors.</p>
        </div>
        <button type="button" id="save-progress-btn" class="px-6 py-2 rounded text-white font-medium" style="background:#63a2bb">
            <i class="fas fa-check mr-2"></i>Save Progress
        </button>
    </div>

    <form id="variants-form" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}" />

        <!-- Variants Container -->
        <div id="variants-container"></div>

        <!-- Add Another Variant -->
        <div class="border-2 border-dashed border-gray-200 rounded p-12 text-center mb-8 cursor-pointer hover:border-gray-300 transition" id="add-variant-btn">
            <i class="fas fa-plus text-3xl text-gray-300 mb-3 block"></i>
            <p class="text-gray-600 font-medium">Add Another Variant</p>
            <p class="text-xs text-gray-400 mt-1">MAXIMUM 50 VARIANTS PER PRODUCT</p>
        </div>

        <!-- Bottom Buttons -->
        <div class="flex justify-between items-center">
            <button type="button" id="back-btn" class="px-6 py-2 rounded border text-gray-700 bg-white font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Basic Info
            </button>
            <div class="flex gap-3">
                <button type="button" id="discard-btn" class="px-6 py-2 rounded border text-gray-700 bg-white font-medium">
                    Discard Changes
                </button>
                <button type="submit" id="next-btn" class="px-6 py-2 rounded text-white font-medium" style="background:#63a2bb">
                    Next: Pricing & Shipping <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const variantsForm = document.getElementById('variants-form');
    const variantsContainer = document.getElementById('variants-container');
    const addVariantBtn = document.getElementById('add-variant-btn');
    const backBtn = document.getElementById('back-btn');
    const discardBtn = document.getElementById('discard-btn');
    const nextBtn = document.getElementById('next-btn');
    const saveProgressBtn = document.getElementById('save-progress-btn');
    let variantCount = 0;

    // Variant card template
    const createVariantCard = (index) => {
        return `
            <div class="variant-card bg-white rounded shadow p-6 mb-6" data-variant-index="${index}">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-cube text-gray-400"></i>
                        <h3 class="text-lg font-semibold">Variant #${index}</h3>
                    </div>
                    <button type="button" class="delete-variant text-red-500 hover:text-red-700">
                        <i class="fas fa-trash-alt text-xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-6">
                    <div class="col-span-2">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Variant Name</label>
                            <input type="text" name="variants[${index}][variant_name]" placeholder="e.g. Midnight Blue Slim Fit" class="w-full border rounded p-3" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Initial Stock</label>
                                <input type="number" name="variants[${index}][initial_stock]" value="0" class="w-full border rounded p-3" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Color</label>
                                <select name="variants[${index}][color]" class="w-full border rounded p-3">
                                    <option value="">Select Color</option>
                                    <option value="black">Black</option>
                                    <option value="white">White</option>
                                    <option value="blue">Blue</option>
                                    <option value="red">Red</option>
                                    <option value="green">Green</option>
                                    <option value="gray">Gray</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Size</label>
                                <select name="variants[${index}][size]" class="w-full border rounded p-3">
                                    <option value="">Select Size</option>
                                    <option value="xs">XS</option>
                                    <option value="s">S</option>
                                    <option value="m">M</option>
                                    <option value="l">L</option>
                                    <option value="xl">XL</option>
                                    <option value="xxl">XXL</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Minimum Stock (Low Alert)</label>
                                <input type="number" name="variants[${index}][min_stock]" value="10" class="w-full border rounded p-3" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Optional Price Adjustment</label>
                            <input type="text" name="variants[${index}][price_adjustment]" placeholder="$ Leave empty for default" class="w-full border rounded p-3" />
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Variant Media</h4>
                        <div class="border-dashed border-2 border-gray-200 rounded p-8 text-center mb-4">
                            <i class="fas fa-camera text-3xl text-gray-300 mb-3 block"></i>
                            <p class="text-sm text-gray-500">Click or drag images here</p>
                            <p class="text-xs text-gray-400">(PNG, JPG/JPEG)</p>
                            <input type="file" name="variants[${index}][images][]" accept="image/*" multiple class="mt-3 w-full" />
                        </div>

                        <div class="flex gap-2">
                            <button type="button" class="w-20 h-20 border-2 border-dashed border-gray-200 rounded flex items-center justify-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-plus text-2xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    };

    // Add initial variant
    variantCount = 1;
    variantsContainer.innerHTML = createVariantCard(variantCount);
    attachDeleteListener();

    // Add variant button click
    addVariantBtn.addEventListener('click', () => {
        if (variantCount >= 50) {
            alert('Maximum 50 variants per product');
            return;
        }
        variantCount++;
        variantsContainer.insertAdjacentHTML('beforeend', createVariantCard(variantCount));
        attachDeleteListener();
    });

    // Attach delete listeners
    function attachDeleteListener() {
        document.querySelectorAll('.delete-variant').forEach(btn => {
            btn.removeEventListener('click', deleteVariant);
            btn.addEventListener('click', deleteVariant);
        });
    }

    function deleteVariant(e) {
        e.preventDefault();
        const card = e.currentTarget.closest('.variant-card');
        if (document.querySelectorAll('.variant-card').length > 1) {
            card.remove();
        } else {
            alert('You must have at least one variant');
        }
    }

    // Back button
    backBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.history.back();
    });

    // Discard button
    discardBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (confirm('Are you sure you want to discard all changes?')) {
            window.history.back();
        }
    });

    // Save progress button
    saveProgressBtn.addEventListener('click', (e) => {
        e.preventDefault();
        alert('Progress saved! You can continue editing later.');
    });

    // Form submission
    variantsForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        let hasValidVariant = false;
        document.querySelectorAll('.variant-card').forEach((card) => {
            const nameInput = card.querySelector('input[name*="variant_name"]');
            if (nameInput && nameInput.value.trim() !== '') {
                hasValidVariant = true;
            }
        });

        if (!hasValidVariant) {
            alert('Please enter at least one variant name');
            return;
        }

        variantsForm.submit();
    });
});
</script>
@endpush

@endsection

    <form id="variants-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.produk.variants.store', $product->id) }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}" />
