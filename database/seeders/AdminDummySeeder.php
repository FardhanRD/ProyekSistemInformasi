<?php

namespace Database\Seeders;

use App\Models\AdminActivityLog;
use App\Models\AuditTrail;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\MasterProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductDiscount;
use App\Models\ProductMedia;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Produk;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ShippingSetting;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\Ulasan;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminDummySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $admin = User::updateOrCreate(
            ['email' => 'admin@movr.test'],
            [
                'name' => 'Admin MOVR',
                'password' => 'password123',
                'role' => 'admin',
                'email_verified_at' => $now,
                'is_blocked' => false,
                'blocked_at' => null,
                'blocked_reason' => null,
            ]
        );

        $customers = $this->seedCustomers();
        $categories = $this->seedCategories();
        $suppliers = $this->seedSuppliers($categories);
        $legacyProducts = $this->seedLegacyProducts($admin, $categories, $suppliers);
        $masterProducts = $this->seedMasterProducts($categories);
        $variants = $this->seedVariants($masterProducts, $suppliers);

        $this->seedVariantPricing($variants, $masterProducts);
        $this->seedInventoryAndMovements($variants, $admin);
        $this->seedSupplierLinks($suppliers, $masterProducts, $variants);
        $this->seedPurchaseOrders($suppliers, $variants, $admin);
        $this->seedOrders($customers, $legacyProducts, $variants, $admin);
        $this->seedReviews($customers, $legacyProducts, $admin);
        $this->seedPromotions($masterProducts, $variants);
        $this->seedShippingSettings();
        $this->seedActivityLogs($admin, $legacyProducts, $masterProducts, $variants);
        $this->seedAuditTrails($admin, $legacyProducts, $masterProducts, $variants);
        $this->seedLegacyProdukTable($admin, $categories);
    }

    private function seedCustomers(): Collection
    {
        return collect(range(1, 49))->map(function (int $index) {
            return User::updateOrCreate(
                ['email' => sprintf('customer%02d@movr.test', $index)],
                [
                    'name' => sprintf('Customer %02d', $index),
                    'password' => 'password123',
                    'role' => 'pembeli',
                    'email_verified_at' => now(),
                    'is_blocked' => $index % 17 === 0,
                    'blocked_at' => $index % 17 === 0 ? now()->subDays($index) : null,
                    'blocked_reason' => $index % 17 === 0 ? 'Akun percobaan untuk demo admin' : null,
                ]
            );
        });
    }

    private function seedCategories(): Collection
    {
        $roots = [
            ['name' => 'Sepatu', 'description' => 'Kategori produk sepatu olahraga dan kasual.'],
            ['name' => 'Pakaian', 'description' => 'Kategori pakaian aktif untuk pria dan wanita.'],
            ['name' => 'Aksesoris', 'description' => 'Kategori aksesoris olahraga dan lifestyle.'],
            ['name' => 'Perlengkapan', 'description' => 'Kategori perlengkapan latihan dan outdoor.'],
            ['name' => 'Kebugaran', 'description' => 'Kategori alat bantu fitness dan recovery.'],
            ['name' => 'Outdoor', 'description' => 'Kategori perlengkapan luar ruang dan adventure.'],
        ];

        $rootCategories = collect($roots)->map(function (array $data) {
            return Category::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'parent_id' => null,
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'description' => $data['description'],
                ]
            );
        });

        $childrenMap = [
            'Sepatu' => ['Lari', 'Training'],
            'Pakaian' => ['Pria', 'Wanita'],
            'Aksesoris' => ['Jam Tangan', 'Tas'],
            'Perlengkapan' => ['Gym', 'Lapangan'],
            'Kebugaran' => ['Recovery', 'Home Workout'],
            'Outdoor' => ['Hiking', 'Camping'],
        ];

        $childCategories = collect();
        foreach ($childrenMap as $rootName => $children) {
            $parent = $rootCategories->firstWhere('name', $rootName);
            foreach ($children as $childName) {
                $childCategories->push(
                    Category::updateOrCreate(
                        ['slug' => Str::slug($rootName . ' ' . $childName)],
                        [
                            'parent_id' => $parent?->id,
                            'name' => $rootName . ' ' . $childName,
                            'slug' => Str::slug($rootName . ' ' . $childName),
                            'description' => 'Sub kategori ' . $childName . ' untuk ' . $rootName . '.',
                        ]
                    )
                );
            }
        }

        return $rootCategories->merge($childCategories)->values();
    }

    private function seedSuppliers(Collection $categories): Collection
    {
        $stores = [
            'SerbaSport',
            'MotionLab',
            'UrbanPeak',
            'Kinetic House',
            'Aksi Prima',
            'Stride Studio',
            'NextMove Supply',
            'ProGear Center',
            'Active Route',
            'Flex Nation',
        ];

        return collect($stores)->map(function (string $storeName, int $index) use ($categories) {
            $category = $categories->firstWhere('parent_id', null) ?? $categories->first();

            return Supplier::updateOrCreate(
                ['store_name' => $storeName],
                [
                    'category' => $category?->name ?? 'Umum',
                    'owner_name' => 'Owner ' . ($index + 1),
                    'address' => sprintf('Jl. Demo No. %d, Jakarta Selatan', $index + 10),
                    'phone_number' => sprintf('0812%08d', $index + 1000000),
                    'email' => sprintf('supplier%02d@movr.test', $index + 1),
                    'is_active' => true,
                ]
            );
        });
    }

    private function seedLegacyProducts(User $admin, Collection $categories, Collection $suppliers): Collection
    {
        $prefixes = ['Alpha', 'Prime', 'Urban', 'Motion', 'Power', 'Swift', 'Fusion', 'Core', 'Edge', 'Pulse'];
        $suffixes = ['Lite', 'Pro', 'Flex', 'Max', 'Flow', 'X', 'Series', 'One', 'Boost', 'Elite'];
        $products = collect();

        foreach (range(1, 50) as $index) {
            $category = $categories->random();
            $supplier = $suppliers->random();
            $name = sprintf('%s %s %02d', $category->name, $prefixes[$index % count($prefixes)] . ' ' . $suffixes[$index % count($suffixes)], $index);
            $price = 150000 + ($index * 12500);
            $stock = (($index * 7) % 120) + ($index % 5 === 0 ? 4 : 18);

            $product = Product::updateOrCreate(
                ['name' => $name],
                [
                    'description' => sprintf('%s dirancang sebagai produk demo untuk halaman admin. %s', $name, Str::limit(fake('id_ID')->sentence(18), 160, '')),
                    'price' => $price,
                    'stock' => $stock,
                    'image' => null,
                    'category_id' => $category->id,
                    'supplier_id' => $supplier->id,
                    'user_id' => $admin->id,
                ]
            );

            $products->push($product);
        }

        return $products;
    }

    private function seedMasterProducts(Collection $categories): Collection
    {
        $brands = ['MOVR', 'AeroFit', 'Stride', 'Kinetic', 'Pace', 'Vanta'];
        $sportTypes = ['Lifestyle', 'Running', 'Training', 'Gym', 'Outdoor', 'Cycling'];
        $genders = ['unisex', 'male', 'female', 'kids'];
        $masterProducts = collect();

        foreach (range(1, 12) as $index) {
            $category = $categories->random();
            $name = sprintf('%s Master %02d', $category->name, $index);
            $masterProducts->push(
                MasterProduct::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'description' => fake('id_ID')->paragraph(3),
                        'category_id' => $category->id,
                        'brand' => $brands[$index % count($brands)],
                        'specifications' => [
                            'material' => ['Mesh', 'Polyester', 'Cotton', 'Leather'][$index % 4],
                            'weight' => ($index * 120) . 'g',
                            'water_resistant' => $index % 2 === 0,
                        ],
                        'gender' => $genders[$index % count($genders)],
                        'sport_type' => $sportTypes[$index % count($sportTypes)],
                        'is_active' => true,
                    ]
                )
            );
        }

        return $masterProducts;
    }

    private function seedVariants(Collection $masterProducts, Collection $suppliers): Collection
    {
        $sizes = ['S', 'M', 'L', 'XL'];
        $colors = ['Hitam', 'Putih', 'Biru', 'Abu-abu', 'Hijau', 'Merah'];
        $variants = collect();

        foreach ($masterProducts as $index => $masterProduct) {
            foreach ([0, 1] as $variantIndex) {
                $size = $sizes[($index + $variantIndex) % count($sizes)];
                $color = $colors[($index + $variantIndex) % count($colors)];
                $sku = sprintf('SKU-%03d-%s-%s', $masterProduct->id, $size, Str::slug($color));

                $variants->push(
                    ProductVariant::updateOrCreate(
                        ['sku' => $sku],
                        [
                            'master_product_id' => $masterProduct->id,
                            'size' => $size,
                            'color' => $color,
                            'sku' => $sku,
                            'is_active' => true,
                        ]
                    )
                );
            }
        }

        return $variants;
    }

    private function seedVariantPricing(Collection $variants, Collection $masterProducts): void
    {
        foreach ($variants as $index => $variant) {
            $masterProduct = $masterProducts->firstWhere('id', $variant->master_product_id);
            $basePrice = 250000 + (($index + 1) * 17500);
            $salePrice = $index % 3 === 0 ? $basePrice * 0.92 : null;
            $discount = $index % 4 === 0 ? 15 : null;

            ProductVariantPrice::updateOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'product_variant_id' => $variant->id,
                    'base_price' => $basePrice,
                    'sale_price' => $salePrice,
                    'discount_percent' => $discount,
                    'flash_sale_price' => $index % 5 === 0 ? $basePrice * 0.85 : null,
                    'flash_sale_start' => $index % 5 === 0 ? now()->subDays(1) : null,
                    'flash_sale_end' => $index % 5 === 0 ? now()->addDays(2) : null,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedInventoryAndMovements(Collection $variants, User $admin): void
    {
        foreach ($variants as $index => $variant) {
            $quantity = $index % 4 === 0 ? 4 + $index : 18 + ($index * 2);
            $inventory = InventoryItem::updateOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'min_stock' => 10,
                    'last_restock_at' => now()->subDays($index + 1),
                ]
            );

            StockMovement::updateOrCreate(
                [
                    'inventory_item_id' => $inventory->id,
                    'product_variant_id' => $variant->id,
                    'movement_type' => 'in',
                    'reference_type' => 'seed',
                    'reference_id' => $variant->id,
                ],
                [
                    'quantity' => 20 + $index,
                    'before_qty' => max(0, $quantity - 20),
                    'after_qty' => $quantity,
                    'note' => 'Seed stok awal untuk demo admin',
                    'created_by' => $admin->id,
                ]
            );

            if ($index % 2 === 0) {
                StockMovement::create([
                    'inventory_item_id' => $inventory->id,
                    'product_variant_id' => $variant->id,
                    'movement_type' => 'out',
                    'quantity' => 2 + ($index % 4),
                    'before_qty' => $quantity,
                    'after_qty' => max(0, $quantity - (2 + ($index % 4))),
                    'reference_type' => 'order_seed',
                    'reference_id' => $index + 1,
                    'note' => 'Penyesuaian keluar untuk simulasi stok',
                    'created_by' => $admin->id,
                ]);
            }
        }
    }

    private function seedSupplierLinks(Collection $suppliers, Collection $masterProducts, Collection $variants): void
    {
        foreach ($variants as $index => $variant) {
            $supplier = $suppliers[$index % $suppliers->count()];
            $masterProduct = $masterProducts->firstWhere('id', $variant->master_product_id);
            SupplierProduct::updateOrCreate(
                [
                    'supplier_id' => $supplier->id,
                    'master_product_id' => $masterProduct->id,
                    'product_variant_id' => $variant->id,
                ],
                [
                    'purchase_price' => 180000 + ($index * 9000),
                    'stock' => 20 + ($index * 3),
                    'min_stock' => 5,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedPurchaseOrders(Collection $suppliers, Collection $variants, User $admin): void
    {
        foreach (range(1, 10) as $index) {
            $supplier = $suppliers[$index % $suppliers->count()];
            $selectedVariants = $variants->slice(($index - 1) * 2, 2)->values();
            if ($selectedVariants->isEmpty()) {
                $selectedVariants = $variants->take(2);
            }

            $items = [];
            $subtotal = 0;
            foreach ($selectedVariants as $variantIndex => $variant) {
                $purchasePrice = 160000 + (($index + $variantIndex) * 13000);
                $qty = 4 + $variantIndex + $index;
                $lineSubtotal = $purchasePrice * $qty;
                $subtotal += $lineSubtotal;
                $items[] = [
                    'product_variant_id' => $variant->id,
                    'qty' => $qty,
                    'purchase_price' => $purchasePrice,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $purchaseOrder = PurchaseOrder::updateOrCreate(
                ['invoice_number' => sprintf('PO-20260428-%04d', $index)],
                [
                    'supplier_id' => $supplier->id,
                    'order_date' => now()->subDays(10 + $index)->toDateString(),
                    'status' => ['draft', 'ordered', 'received', 'cancelled'][$index % 4],
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'notes' => 'Purchase order demo #' . $index,
                    'created_by' => $admin->id,
                ]
            );

            foreach ($items as $item) {
                PurchaseOrderItem::updateOrCreate(
                    [
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_variant_id' => $item['product_variant_id'],
                    ],
                    $item + ['purchase_order_id' => $purchaseOrder->id]
                );
            }
        }
    }

    private function seedOrders(Collection $customers, Collection $legacyProducts, Collection $variants, User $admin): void
    {
        $statuses = ['pending', 'paid', 'cancelled', 'processing', 'shipped', 'delivered'];
        $shippingStatuses = ['pending', 'packed', 'shipped', 'in_transit', 'delivered'];
        $paymentMethods = ['transfer', 'cod', 'kartu_kredit', 'ewallet'];

        foreach (range(1, 20) as $index) {
            $user = $customers[$index % $customers->count()];
            $selectedProducts = $legacyProducts->slice(($index - 1) * 2, 2)->values();
            if ($selectedProducts->isEmpty()) {
                $selectedProducts = $legacyProducts->take(2);
            }

            $selectedVariants = $variants->slice(($index - 1) * 2, 2)->values();
            if ($selectedVariants->isEmpty()) {
                $selectedVariants = $variants->take(2);
            }

            $orderItems = [];
            $subtotal = 0;
            foreach ($selectedProducts as $itemIndex => $product) {
                $qty = 1 + (($index + $itemIndex) % 3);
                $price = (float) $product->price;
                $lineSubtotal = $price * $qty;
                $subtotal += $lineSubtotal;
                $variant = $selectedVariants[$itemIndex % $selectedVariants->count()];

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'cost_price' => $price * 0.75,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $shippingCost = 15000 + ($index * 2500);
            $status = $statuses[$index % count($statuses)];
            $shippingStatus = $shippingStatuses[$index % count($shippingStatuses)];
            $invoice = sprintf('INV-DM-%04d', $index);
            $totalAmount = $subtotal + $shippingCost;

            $order = Order::updateOrCreate(
                ['invoice_number' => $invoice],
                [
                    'user_id' => $user->id,
                    'total_amount' => $totalAmount,
                    'status' => $status,
                    'payment_method' => $paymentMethods[$index % count($paymentMethods)],
                    'midtrans_order_id' => 'MID-' . now()->format('Ymd') . '-' . str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                    'payment_url' => 'https://demo.midtrans.test/order/' . $index,
                    'transaction_time' => now()->subDays($index),
                    'invoice_number' => $invoice,
                    'verified_paid_at' => in_array($status, ['paid', 'processing', 'shipped', 'delivered'], true) ? now()->subDays($index - 1) : null,
                    'stock_reduced_at' => in_array($status, ['paid', 'processing', 'shipped', 'delivered'], true) ? now()->subDays($index - 1) : null,
                    'courier_service' => ['JNE REG', 'J&T Express', 'SiCepat', 'AnterAja'][$index % 4],
                    'tracking_number' => 'TRK' . now()->format('ymd') . str_pad((string) $index, 5, '0', STR_PAD_LEFT),
                    'shipping_status' => $shippingStatus,
                    'shipping_cost' => $shippingCost,
                ]
            );

            foreach ($orderItems as $item) {
                OrderItem::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'],
                    ],
                    $item + ['order_id' => $order->id]
                );
            }
        }
    }

    private function seedReviews(Collection $customers, Collection $legacyProducts, User $admin): void
    {
        $statuses = ['approved', 'pending', 'rejected'];

        foreach (range(1, 20) as $index) {
            $customer = $customers[$index % $customers->count()];
            $product = $legacyProducts[$index % $legacyProducts->count()];
            Ulasan::updateOrCreate(
                [
                    'produk_id' => $product->id,
                    'pembeli_id' => $customer->id,
                ],
                [
                    'rating' => ($index % 5) + 1,
                    'komentar' => fake('id_ID')->sentence(12),
                    'moderation_status' => $statuses[$index % count($statuses)],
                    'admin_reply' => $index % 2 === 0 ? 'Terima kasih atas ulasannya.' : null,
                    'moderated_at' => now()->subDays($index),
                    'moderated_by' => $admin->id,
                ]
            );
        }
    }

    private function seedPromotions(Collection $masterProducts, Collection $variants): void
    {
        foreach (range(1, 8) as $index) {
            Voucher::updateOrCreate(
                ['code' => 'MOVR' . str_pad((string) ($index * 10), 2, '0', STR_PAD_LEFT)],
                [
                    'discount_type' => $index % 2 === 0 ? 'percent' : 'fixed',
                    'discount_value' => $index % 2 === 0 ? 10 + $index : 15000 + ($index * 2500),
                    'min_order' => 100000 + ($index * 50000),
                    'max_discount' => $index % 2 === 0 ? 75000 : null,
                    'quota' => 100 + ($index * 25),
                    'used_count' => 10 + $index,
                    'start_at' => now()->subDays(5 + $index),
                    'end_at' => now()->addDays(20 + $index),
                    'is_active' => true,
                ]
            );
        }

        foreach (range(1, 8) as $index) {
            $masterProduct = $masterProducts[$index % $masterProducts->count()];
            $variant = $variants[$index % $variants->count()];
            ProductDiscount::updateOrCreate(
                [
                    'master_product_id' => $index % 2 === 0 ? $masterProduct->id : null,
                    'product_variant_id' => $index % 2 === 1 ? $variant->id : null,
                    'discount_type' => $index % 2 === 0 ? 'percent' : 'fixed',
                ],
                [
                    'discount_value' => $index % 2 === 0 ? 10 + ($index * 2) : 10000 + ($index * 1500),
                    'is_flash_sale' => $index % 3 === 0,
                    'start_at' => now()->subDays($index),
                    'end_at' => now()->addDays(15 + $index),
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedShippingSettings(): void
    {
        $settings = [
            ['Jakarta Raya', 'JNE REG', 18000, 2],
            ['Jawa Barat', 'SiCepat BEST', 22000, 3],
            ['Jawa Tengah', 'J&T Express', 25000, 3],
            ['Jawa Timur', 'AnterAja', 26000, 4],
            ['Bali', 'TIKI', 32000, 4],
            ['Luar Jawa', 'POS Indonesia', 38000, 5],
        ];

        foreach ($settings as [$zone, $courier, $cost, $days]) {
            ShippingSetting::updateOrCreate(
                [
                    'destination_zone' => $zone,
                    'courier_service' => $courier,
                ],
                [
                    'cost' => $cost,
                    'estimated_days' => $days,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedActivityLogs(User $admin, Collection $legacyProducts, Collection $masterProducts, Collection $variants): void
    {
        $modules = ['dashboard', 'master_product', 'category', 'supplier', 'inventory', 'order', 'promo', 'review'];
        $actions = ['create', 'update', 'delete', 'sync', 'approve', 'publish'];

        foreach (range(1, 20) as $index) {
            AdminActivityLog::updateOrCreate(
                [
                    'module' => $modules[$index % count($modules)],
                    'action' => $actions[$index % count($actions)],
                    'description' => sprintf('Log demo #%d', $index),
                ],
                [
                    'admin_id' => $admin->id,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Demo Seeder',
                    'metadata' => [
                        'legacy_product_id' => $legacyProducts[$index % $legacyProducts->count()]->id,
                        'master_product_id' => $masterProducts[$index % $masterProducts->count()]->id,
                        'variant_id' => $variants[$index % $variants->count()]->id,
                    ],
                ]
            );
        }
    }

    private function seedAuditTrails(User $admin, Collection $legacyProducts, Collection $masterProducts, Collection $variants): void
    {
        foreach (range(1, 20) as $index) {
            AuditTrail::updateOrCreate(
                [
                    'table_name' => $index % 2 === 0 ? 'products' : 'master_products',
                    'row_id' => $index,
                    'action' => $index % 3 === 0 ? 'update' : 'create',
                ],
                [
                    'before_data' => [
                        'name' => 'Before ' . $index,
                        'stock' => 10 + $index,
                    ],
                    'after_data' => [
                        'name' => $index % 2 === 0 ? $legacyProducts[$index % $legacyProducts->count()]->name : $masterProducts[$index % $masterProducts->count()]->name,
                        'stock' => 20 + $index,
                    ],
                    'changed_by' => $admin->id,
                ]
            );
        }
    }

    private function seedLegacyProdukTable(User $admin, Collection $categories): void
    {
        $categoryNames = $categories->whereNull('parent_id')->pluck('name')->values();
        if ($categoryNames->isEmpty()) {
            $categoryNames = collect(['Sepatu', 'Pakaian', 'Aksesoris']);
        }

        foreach (range(1, 50) as $index) {
            $categoryName = $categoryNames[$index % $categoryNames->count()];
            Produk::updateOrCreate(
                ['slug' => 'produk-dummy-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
                [
                    'penjual_id' => $admin->id,
                    'nama_produk' => sprintf('Produk Dummy %02d', $index),
                    'slug' => 'produk-dummy-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                    'deskripsi' => fake('id_ID')->sentence(18),
                    'harga' => 95000 + ($index * 11000),
                    'stok' => 15 + ($index * 2),
                    'gambar' => null,
                    'kategori' => $categoryName,
                ]
            );
        }
    }
}
