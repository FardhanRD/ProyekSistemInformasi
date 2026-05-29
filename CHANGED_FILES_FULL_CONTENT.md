# Full Content of Changed Files


## FILE: app/Http/Controllers/ProfileController.php

----- START app/Http/Controllers/ProfileController.php -----
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\AlamatPengguna;
use App\Models\Transaksi;
use App\Models\Buyer;
use App\Models\AkunPembayaran;
use App\Models\Wishlist;
use App\Services\PenggunaSyncService;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $buyer = Buyer::where('pengguna_id', $user->pengguna_id)->first();
        $addresses = [];
        $orders = [];
        $paymentMethods = [];
        $semuaPesanan = collect();
        $orderCounts = [
            '' => 0,
            'menunggu_pembayaran' => 0,
            'pembayaran_dikonfirmasi' => 0,
            'dikirim' => 0,
            'selesai' => 0,
            'dibatalkan' => 0,
        ];

        if (Schema::hasTable('alamat_pengguna')) {
            $addresses = AlamatPengguna::where('pengguna_id', $user->pengguna_id)->get();
        }

        if (Schema::hasTable('transaksi')) {
            $orders = Transaksi::with([
                    'details.detailProduk.produk.gambarUtama',
                    'alamat',
                    'ekspedisi',
                    'pembayaran.metodePembayaran',
                    'pesanan.trackingLog',
                    'voucher',
                ])
                ->where('pengguna_id', $user->pengguna_id)
                ->orderBy('tanggal', 'desc')
                ->get();

            $semuaPesanan = Transaksi::with([
                    'transaksiDetail.detailProduk.produk.gambarUtama',
                    'transaksiDetail.detailProduk.warna',
                ])
                ->where('pengguna_id', $user->pengguna_id)
                ->orderBy('tanggal', 'desc')
                ->get();

            $orderCounts = [
                '' => $semuaPesanan->count(),
                'menunggu_pembayaran' => $semuaPesanan->where('status', 'menunggu_pembayaran')->count(),
                'pembayaran_dikonfirmasi' => $semuaPesanan->where('status', 'pembayaran_dikonfirmasi')->count(),
                'dikirim' => $semuaPesanan->where('status', 'dikirim')->count(),
                'selesai' => $semuaPesanan->where('status', 'selesai')->count(),
                'dibatalkan' => $semuaPesanan->where('status', 'dibatalkan')->count(),
            ];
        }

        if (Schema::hasTable('akun_pembayaran')) {
            $paymentMethods = AkunPembayaran::with('metodePembayaran')
                                ->where('pengguna_id', $user->pengguna_id)
                                ->get();
        }
        
        $availableMethods = \App\Models\MetodePembayaran::where('is_active', 1)->get();

        $orderCount = $semuaPesanan->count();
        $wishlistOwnerColumn = Wishlist::ownerColumn();
        $wishlistOwnerId = Wishlist::resolveOwnerId($user);
        $wishlistCount = $wishlistOwnerId ? Wishlist::where($wishlistOwnerColumn, $wishlistOwnerId)->count() : 0;

        $alamats = $addresses;
        $akunPembayaran = $paymentMethods;
        $metodePembayarans = $availableMethods;

        return view('buyer.profile.index', compact(
            'user',
            'buyer',
            'addresses',
            'orders',
            'paymentMethods',
            'availableMethods',
            'semuaPesanan',
            'orderCounts',
            'orderCount',
            'wishlistCount',
            'alamats',
            'akunPembayaran',
            'metodePembayarans'
        ));
    }

    public function updateProfile(Request $request, PenggunaSyncService $penggunaSyncService)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:pengguna,username,' . $user->pengguna_id . ',pengguna_id', // Assuming 'pengguna_id' is the primary key
            'email' => 'required|string|email|max:255|unique:pengguna,email,' . $user->pengguna_id . ',pengguna_id',
            'no_telepon' => 'nullable|string|max:25',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P', // Assuming 'L' for Male, 'P' for Female
            'foto_profil' => 'nullable|image|max:10240',
            'foto_profil_position' => 'nullable|string|max:50',
        ], [
            'foto_profil.max' => 'Ukuran foto profil maksimal adalah 10MB.',
            'foto_profil.image' => 'File harus berupa gambar.',
            'foto_profil.uploaded' => 'Gagal mengupload foto profil. Pastikan ukuran file tidak lebih dari 10MB.'
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->no_telepon = $validated['no_telepon'] ?? null;
        $user->tanggal_lahir = $validated['tanggal_lahir'] ?? null;
        $user->jenis_kelamin = $validated['jenis_kelamin'] ?? null;
        $user->foto_profil_position = $request->input('foto_profil_position', '50% 50%');

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            
            $file = $request->file('foto_profil');
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (in_array($extension, ['heic', 'heif'])) {
                // Transcode HEIC to JPG using macOS native sips utility
                $tempPath = $file->getRealPath();
                $newFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.jpg';
                $targetPath = storage_path('app/public/profile/' . $newFilename);
                
                if (!file_exists(storage_path('app/public/profile'))) {
                    mkdir(storage_path('app/public/profile'), 0755, true);
                }
                
                $cmd = "sips -s format jpeg " . escapeshellarg($tempPath) . " --out " . escapeshellarg($targetPath);
                exec($cmd);
                
                if (file_exists($targetPath)) {
                    $user->foto_profil = 'profile/' . $newFilename;
                } else {
                    $user->foto_profil = $file->store('profile', 'public');
                }
            } else {
                $user->foto_profil = $file->store('profile', 'public');
            }
        }

        $user->save();

        $penggunaSyncService->ensureForAuthUser($user, 'buyer', [
            'nama_pengguna' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'no_telepon' => $user->no_telepon,
            'tanggal_lahir' => $user->tanggal_lahir,
            'jenis_kelamin' => $user->jenis_kelamin,
            'foto_profil' => $user->foto_profil,
            'foto_profil_position' => $user->foto_profil_position,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    public function addresses()
    {
        return redirect()->to(route('profile.index') . '#addresses');
    }

    public function createAddress(Request $request)
    {
        // Ambil parameter 'return' dari URL dan teruskan ke view
        // agar bisa dimasukkan ke dalam form sebagai hidden input.
        return view('profile.address-form', ['return_url' => $request->query('return')]);
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:20',
            'nama_penerima' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'provinsi' => 'required|string|max:50',
            'kota' => 'required|string|max:50',
            'kecamatan' => 'required|string|max:50',
            'kelurahan' => 'required|string|max:50',
            'kode_pos' => 'required|string|max:10',
            'alamat_lengkap' => 'required|string|max:255',
            'is_utama' => 'nullable|boolean',
        ]);

        $validated['pengguna_id'] = Auth::user()->pengguna_id;

        // If this is marked as primary, unmark others
        if ($request->is_utama) {
            AlamatPengguna::where('pengguna_id', Auth::user()->pengguna_id)->update(['is_utama' => false]);
            $validated['is_utama'] = true;
        }

        AlamatPengguna::create($validated);

        // Cek jika ada parameter 'return' untuk redirect kembali ke checkout
        if ($request->input('return') === 'checkout') {
            return redirect()->route('checkout.index')->with('success', 'Alamat berhasil ditambahkan.');
        }

        // Redirect default ke halaman daftar alamat
        return redirect()->route('profile.addresses')->with('success', 'Alamat berhasil ditambahkan');
    }

    public function editAddress($id)
    {
        $user = Auth::user();
        $address = AlamatPengguna::findOrFail($id);

        if ($address->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        return view('profile.address-form', compact('address'));
    }

    public function updateAddress(Request $request, $id)
    {
        $user = Auth::user();
        $address = AlamatPengguna::findOrFail($id);

        if ($address->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'label' => 'required|string|max:20',
            'nama_penerima' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'provinsi' => 'required|string|max:50',
            'kota' => 'required|string|max:50',
            'kecamatan' => 'required|string|max:50',
            'kelurahan' => 'required|string|max:50',
            'kode_pos' => 'required|string|max:10',
            'alamat_lengkap' => 'required|string|max:255',
            'is_utama' => 'nullable|boolean',
        ]);

        // If this is marked as primary, unmark others
        if ($request->is_utama) {
            AlamatPengguna::where('pengguna_id', $user->pengguna_id)->where('alamat_id', '!=', $id)->update(['is_utama' => false]);
            $validated['is_utama'] = true;
        }

        $address->update($validated);

        // Menggunakan route name untuk konsistensi
        return redirect()->route('profile.addresses')->with('success', 'Alamat berhasil diperbarui');
    }

    public function deleteAddress($id)
    {
        $user = Auth::user();
        $address = AlamatPengguna::findOrFail($id);

        if ($address->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        $address->delete();

        // Menggunakan route name untuk konsistensi
        return redirect()->route('profile.addresses')->with('success', 'Alamat berhasil dihapus');
    }

    /**
     * Set a specific address as primary.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setPrimaryAddress($id)
    {
        $user = Auth::user();
        $address = AlamatPengguna::findOrFail($id);

        if ($address->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        DB::transaction(function () use ($user, $address) {
            AlamatPengguna::where('pengguna_id', $user->pengguna_id)->update(['is_utama' => false]);
            $address->update(['is_utama' => true]);
        });

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }

    /**
     * Show the saved payment methods.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function paymentMethods()
    {
        return redirect()->to(route('profile.index') . '#payment-methods');
    }

    /**
     * Store a new payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePaymentMethod(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'metode_id' => 'required|integer|exists:metode_pembayaran,metode_id',
            'nomor_akun' => 'required|string|max:255',
            'nama_akun' => 'required|string|max:255',
        ]);

        AkunPembayaran::create([
            'pengguna_id' => $user->pengguna_id,
            'metode_id' => $validated['metode_id'],
            'nomor_akun' => $validated['nomor_akun'],
            'nama_akun' => $validated['nama_akun'],
        ]);

        return back()->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    /**
     * Delete a saved payment method.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePaymentMethod($id)
    {
        $user = Auth::user();
        $account = AkunPembayaran::findOrFail($id);

        if ($account->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        $account->delete();
        return back()->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    /**
     * Handle a change password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('Password lama tidak sesuai.');
                }
            }],
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
----- END app/Http/Controllers/ProfileController.php -----


## FILE: app/Http/Controllers/OrderController.php

----- START app/Http/Controllers/OrderController.php -----
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Buyer;
use App\Models\RatingProduk;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) return redirect('/login');

        $buyer = $user->buyer;
        if (!$buyer) {
            return view('buyer.order.index', [
                'transaksis' => collect(),
                'orderCounts' => [],
            ]);
        }

        // Get order counts for each status
        $allStatuses = ['', 'menunggu_pembayaran', 'pembayaran_dikonfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
        $orderCounts = [];
        
        foreach ($allStatuses as $status) {
            $query = Transaksi::where('pengguna_id', $user->pengguna_id);
            if ($status !== '') {
                $query->where('status', $status);
            }
            $orderCounts[$status] = $query->count();
        }

        // Get filtered orders
        $query = Transaksi::where('pengguna_id', $user->pengguna_id)
            ->with([
                'details.detailProduk.produk.gambarUtama',
                'pembayaran.metode',
                'ekspedisi'
            ]);
        
        $currentStatus = $request->input('status', '');
        if ($currentStatus !== '') {
            $query->where('status', $currentStatus);
        }

        $transaksis = $query->orderBy('tanggal', 'desc')->get();

        return view('buyer.order.index', compact('transaksis', 'orderCounts'));
    }

    public function show($kode_transaksi)
    {
        $user = Auth::user();
        if (! $user) return redirect('/login');
        $buyer = $user->buyer;
        if (! $buyer) {
            abort(403, 'Akses ditolak.');
        }

        $transaksi = Transaksi::with([
            'transaksiDetail.detailProduk.produk.gambarUtama',
            'transaksiDetail.detailProduk.warna',
            'alamat',
            'ekspedisi',
            'pembayaran.metodePembayaran',
            'pesanan.trackingLog',
            'voucher',
        ])
        ->where('kode_transaksi', $kode_transaksi)
        ->where('pengguna_id', $buyer->pengguna_id)
        ->firstOrFail();

        return view('buyer.order.detail', compact('transaksi'));
    }

    public function showJson($kode)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $t = Transaksi::with([
                'transaksiDetail.detailProduk.produk.gambarUtama',
                'transaksiDetail.detailProduk.warna',
                'alamat',
                'ekspedisi',
                'pembayaran.metodePembayaran',
                'pesanan',
                'voucher',
            ])
            ->where('kode_transaksi', $kode)
            ->where('pengguna_id', $user->pengguna_id)
            ->firstOrFail();

        $statusLabel = [
            'menunggu_pembayaran' => 'Belum Bayar',
            'pembayaran_dikonfirmasi' => 'Dikonfirmasi',
            'diproses' => 'Diproses',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        $sudahRating = RatingProduk::where('transaksi_id', $t->transaksi_id)
            ->where('buyer_id', optional($user->buyer)->buyer_id)
            ->exists();

        return response()->json([
            'kode_transaksi' => $t->kode_transaksi,
            'status' => $t->status,
            'status_label' => $statusLabel[$t->status] ?? ucfirst($t->status),
            'tanggal' => $t->tanggal ? Carbon::parse($t->tanggal)->isoFormat('D MMM YYYY, HH:mm') : '-',
            'sudah_rating' => $sudahRating,
            'items' => $t->transaksiDetail->map(fn ($d) => [
                'id' => $d->detail_id,
                'nama' => $d->nama_produk_snap,
                'gambar' => $d->detailProduk->produk->gambarUtama?->url_safe ?? asset('images/placeholder.png'),
                'ukuran' => $d->ukuran_snap ?? '-',
                'warna' => $d->warna_snap ?? 'No Color',
                'qty' => $d->quantity,
                'subtotal' => (int) $d->subtotal,
            ])->values(),
            'penerima' => $t->alamat->nama_penerima ?? '-',
            'telepon' => $t->alamat->no_telepon ?? '-',
            'alamat' => trim(
                implode(', ', array_filter([
                    $t->alamat->alamat_lengkap ?? null,
                    $t->alamat->kota ?? null,
                    $t->alamat->provinsi ?? null,
                ]))
            ),
            'ekspedisi' => trim(($t->ekspedisi->nama_ekspedisi ?? '-') . ' ' . ($t->ekspedisi->jenis_layanan ?? '')),
            'resi' => $t->pesanan->no_resi ?? null,
            'estimasi' => $t->pesanan?->estimasi_tiba
                ? Carbon::parse($t->pesanan->estimasi_tiba)->isoFormat('D MMM YYYY')
                : null,
            'subtotal' => (int) $t->subtotal,
            'ongkir' => (int) $t->ongkos_kirim,
            'diskon' => (int) $t->diskon_voucher,
            'total' => (int) $t->total_harga,
            'metode_bayar' => $t->pembayaran?->metodePembayaran?->metode ?? '-',
        ]);
    }

    // Metode untuk rating produk dan toko akan dipindahkan ke API atau controller terpisah
    // agar lebih modular dan sesuai dengan praktik terbaik.
    // Untuk saat ini, kita akan mengasumsikan rating dilakukan melalui API atau form terpisah.
    public function ratingProduk(Request $request, $id) { /* ... */ }
    public function ratingToko(Request $request, $id) { /* ... */ }
}
----- END app/Http/Controllers/OrderController.php -----


## FILE: app/Http/Controllers/NotificationBuyerController.php

----- START app/Http/Controllers/NotificationBuyerController.php -----
<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class NotificationBuyerController extends Controller
{
    public function unread()
    {
        $notifs = Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->where('is_read', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->notifikasi_id,
                'judul' => $n->judul,
                'pesan' => $n->pesan,
                'jenis' => $n->jenis,
                'url' => $n->url_redirect,
                'waktu' => optional($n->created_at)->diffForHumans() ?? '-',
            ])
            ->values();

        return response()->json([
            'notifs' => $notifs,
            'count' => $notifs->count(),
        ]);
    }

    public function markRead($id)
    {
        Notifikasi::where('notifikasi_id', $id)
            ->where('pengguna_id', auth()->user()->pengguna_id)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }

    public function readAll()
    {
        Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }

    public function index()
    {
        $notifikasis = Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return view('buyer.notifications.index', compact('notifikasis'));
    }
}
----- END app/Http/Controllers/NotificationBuyerController.php -----


## FILE: app/Http/Controllers/Admin/NotificationAdminController.php

----- START app/Http/Controllers/Admin/NotificationAdminController.php -----
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotificationAdminController extends Controller
{
    public function unread()
    {
        $notifs = Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->where('is_read', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->notifikasi_id,
                'judul' => $n->judul,
                'pesan' => $n->pesan,
                'jenis' => $n->jenis,
                'url' => $n->url_redirect,
                'waktu' => optional($n->created_at)->diffForHumans() ?? '-',
            ])
            ->values();

        return response()->json([
            'notifs' => $notifs,
            'count' => $notifs->count(),
        ]);
    }

    public function markRead($id)
    {
        Notifikasi::where('notifikasi_id', $id)
            ->where('pengguna_id', auth()->user()->pengguna_id)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }

    public function readAll()
    {
        Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }
}
----- END app/Http/Controllers/Admin/NotificationAdminController.php -----


## FILE: routes/web.php

----- START routes/web.php -----
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RatingTokoController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\NotificationBuyerController;
use App\Http\Controllers\Admin\NotificationAdminController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\WishlistController as ApiWishlistController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\ReviewController as ApiReviewController;
use Illuminate\Http\Request;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/language/{locale}', function (Request $request, string $locale) {
    if (! in_array($locale, ['id', 'en'], true)) {
        abort(404);
    }

    $request->session()->put('locale', $locale);

    return back();
})->name('language.switch');
Route::get('/kategori/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show.alias');
Route::get('/category/all', [ProductController::class, 'index'])->name('category.all');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show.alias');
Route::get('/produk', [ProductController::class, 'index'])->name('product.index');
Route::get('/search', [ProductController::class, 'search'])->name('product.search');
Route::post('/voucher/check', [VoucherController::class, 'check'])->name('voucher.check');
Route::get('/voucher', [VoucherController::class, 'index'])->name('voucher.index');
Route::post('/voucher/apply', [VoucherController::class, 'apply'])->name('voucher.apply');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.post');
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
});

// Protected (buyer routes)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');


    // Keranjang
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index.alias');
    Route::post('/keranjang', [CartController::class, 'add'])->name('cart.store');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/keranjang/{id}', [CartController::class, 'remove'])->name('cart.destroy');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');

    // Checkout & Payment
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'storeSelection'])->name('checkout.store');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/apply-voucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.apply_voucher');

    Route::get('/pay/{kode_transaksi}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{kode}/upload-proof', [PaymentController::class, 'uploadProof'])->name('payment.upload-proof')->middleware(['auth']);
    Route::post('/payment/{kode_transaksi}/confirm', [PaymentController::class, 'confirmByBuyer'])->name('payment.confirm');

    Route::get('/profile/addresses', [ProfileController::class, 'addresses'])->name('profile.addresses');
    Route::get('/profile/alamat/create', [ProfileController::class, 'createAddress'])->name('profile.alamat.create');
    Route::get('/profile/alamat/{id}/edit', [ProfileController::class, 'editAddress'])->name('profile.alamat.edit');
    Route::put('/profile/alamat/{id}/utama', [ProfileController::class, 'setPrimaryAddress'])->name('alamat.utama');

    // Order & Tracking
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{kode_transaksi}', [OrderController::class, 'show'])->name('orders.show')->middleware(['auth']);
    Route::get('/orders/{kode}/json', [OrderController::class, 'showJson'])->name('orders.show.json');
    Route::get('/tracking/{kode_transaksi}', [TrackingController::class, 'show'])->name('tracking.show');
    Route::get('/orders/{kode_transaksi}/tracking', [TrackingController::class, 'show'])->name('order.tracking');

    // Rating for completed orders
    Route::get('/orders/{kode_transaksi}/rating', [RatingController::class, 'show'])->name('orders.rating');
    Route::post('/orders/{kode_transaksi}/rating', [RatingController::class, 'store'])->name('orders.rating.store');

    Route::get('/rating/produk/{produkId}', [RatingController::class, 'form'])->name('order.rating.produk');
    Route::post('/rating/produk/{produkId}', [RatingController::class, 'submit'])->name('rating.product.submit');
    Route::get('/rating/toko/{supplierId}', [RatingTokoController::class, 'form'])->name('rating.toko.form');
    Route::post('/rating/toko/{supplierId}', [RatingTokoController::class, 'submit'])->name('rating.toko.submit');

    // Profile & Alamat
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    Route::get('/profile/addresses/create', [ProfileController::class, 'createAddress'])->name('profile.address.create');
    Route::get('/profile/address/create', [ProfileController::class, 'createAddress'])->name('profile.address.create.alias');
    Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])->name('profile.address.store');
    Route::get('/profile/addresses/{id}/edit', [ProfileController::class, 'editAddress'])->name('profile.address.edit');
    Route::put('/profile/addresses/{id}', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
    Route::delete('/profile/addresses/{id}', [ProfileController::class, 'deleteAddress'])->name('profile.address.delete');
    Route::put('/profile/addresses/{id}/set-primary', [ProfileController::class, 'setPrimaryAddress'])->name('profile.address.set-primary');

    Route::get('/profile/payment-methods', [ProfileController::class, 'paymentMethods'])->name('profile.payment-methods');
    Route::post('/profile/payment-methods', [ProfileController::class, 'storePaymentMethod'])->name('profile.payment-methods.store');
    Route::delete('/profile/payment-methods/{id}', [ProfileController::class, 'deletePaymentMethod'])->name('profile.payment-methods.delete');

    Route::get('/notifications/unread', [NotificationBuyerController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationBuyerController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationBuyerController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/notifications', [NotificationBuyerController::class, 'index'])->name('notifications.index');
});

// Admin routes (prefix /admin)
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [\App\Http\Controllers\Admin\DashboardExportController::class, 'export'])->name('dashboard.export');

    Route::get('/notifications/unread', [NotificationAdminController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationAdminController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationAdminController::class, 'readAll'])->name('notifications.read-all');

    

    // Master Product (admin)
    Route::get('/master-product', [\App\Http\Controllers\Admin\MasterProductController::class, 'index'])->name('master-product.index');
    Route::get('/master-product/export', [\App\Http\Controllers\Admin\MasterProductController::class, 'export'])->name('master-product.export');
    Route::get('/master-product/events', [\App\Http\Controllers\Admin\MasterProductController::class, 'events'])->name('master-product.events');
    // Create routes MUST come before {id} param routes
    Route::get('/master-product/create', [\App\Http\Controllers\Admin\MasterProductCreateController::class, 'create'])->name('master-product.create');
    Route::post('/master-product', [\App\Http\Controllers\Admin\MasterProductCreateController::class, 'store'])->name('master-product.store');
    Route::get('/master-product/create/variant', [\App\Http\Controllers\Admin\MasterProductCreateController::class, 'createVariant'])->name('master-product.variant.create');
    Route::post('/master-product/variant', [\App\Http\Controllers\Admin\MasterProductCreateController::class, 'storeVariant'])->name('master-product.variant.store');
    Route::get('/master-product/create/media', [\App\Http\Controllers\Admin\MasterProductCreateController::class, 'createMedia'])->name('master-product.media.create');
    Route::post('/master-product/media', [\App\Http\Controllers\Admin\MasterProductCreateController::class, 'storeMedia'])->name('master-product.media.store');
    // Parameterized routes come last
    Route::get('/master-product/{id}', [\App\Http\Controllers\Admin\MasterProductController::class, 'show'])->name('master-product.detail');
    Route::get('/master-product/{id}/edit', [\App\Http\Controllers\Admin\MasterProductController::class, 'edit'])->name('master-product.edit');
    Route::put('/master-product/{id}', [\App\Http\Controllers\Admin\MasterProductController::class, 'update'])->name('master-product.update');
    Route::delete('/master-product/{id}', [\App\Http\Controllers\Admin\MasterProductController::class, 'destroy'])->name('master-product.destroy');

    // Category Management
    Route::get('/category', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/events', [\App\Http\Controllers\Admin\CategoryController::class, 'events'])->name('category.events');
    Route::post('/category/store', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('category.store');
    Route::put('/category/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('category.destroy');

    // Supplier Management
    Route::get('/supplier', [\App\Http\Controllers\Admin\SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/create', [\App\Http\Controllers\Admin\SupplierController::class, 'create'])->name('supplier.create');
    Route::post('/supplier/store', [\App\Http\Controllers\Admin\SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/supplier/{id}', [\App\Http\Controllers\Admin\SupplierController::class, 'show'])->name('supplier.detail');
    Route::delete('/supplier/{id}', [\App\Http\Controllers\Admin\SupplierController::class, 'destroy'])->name('supplier.destroy');

    // Variant Management
    Route::get('/variant', [\App\Http\Controllers\Admin\VariantController::class, 'index'])->name('variant.index');
    Route::get('/variant/events', [\App\Http\Controllers\Admin\VariantController::class, 'events'])->name('variant.events');
    Route::post('/variant/store', [\App\Http\Controllers\Admin\VariantController::class, 'store'])->name('variant.store');
    Route::put('/variant/{id}', [\App\Http\Controllers\Admin\VariantController::class, 'update'])->name('variant.update');
    Route::delete('/variant/{id}', [\App\Http\Controllers\Admin\VariantController::class, 'destroy'])->name('variant.destroy');

    // Media Management (AD6)
    Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('media.upload');
    Route::put('/media/{id}/set-thumbnail', [\App\Http\Controllers\Admin\MediaController::class, 'setThumbnail'])->name('media.set-thumbnail');
    Route::delete('/media/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

    // Pricing Management (AD7)
    Route::get('/pricing', [\App\Http\Controllers\Admin\PricingController::class, 'index'])->name('pricing.index');
    Route::put('/pricing/{id}', [\App\Http\Controllers\Admin\PricingController::class, 'update'])->name('pricing.update');
    Route::post('/pricing/bulk-update', [\App\Http\Controllers\Admin\PricingController::class, 'bulkUpdate'])->name('pricing.bulk-update');

    // Supplier Product (AD8)
    Route::get('/supplier-product', [\App\Http\Controllers\Admin\SupplierProductController::class, 'index'])->name('supplier-product.index');
    Route::post('/supplier-product/store', [\App\Http\Controllers\Admin\SupplierProductController::class, 'store'])->name('supplier-product.store');
    Route::put('/supplier-product/{id}', [\App\Http\Controllers\Admin\SupplierProductController::class, 'update'])->name('supplier-product.update');
    Route::delete('/supplier-product/{id}', [\App\Http\Controllers\Admin\SupplierProductController::class, 'destroy'])->name('supplier-product.destroy');

    // Stock Management (AD9)
    Route::get('/stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('stock.index');
    Route::post('/stock/adjust', [\App\Http\Controllers\Admin\StockController::class, 'adjust'])->name('stock.adjust');

    // Stock Movement Log (AD10)
    Route::get('/stock-movement', [\App\Http\Controllers\Admin\StockMovementController::class, 'index'])->name('stock-movement.index');
    Route::get('/stock-movement/export', [\App\Http\Controllers\Admin\StockMovementController::class, 'export'])->name('stock-movement.export');

    // Promotion Management (AD15)
    Route::get('/promotion', [\App\Http\Controllers\Admin\PromotionController::class, 'index'])->name('promotion.index');
    Route::post('/promotion/voucher', [\App\Http\Controllers\Admin\PromotionController::class, 'storeVoucher'])->name('promotion.voucher.store');
    Route::put('/promotion/voucher/{id}', [\App\Http\Controllers\Admin\PromotionController::class, 'updateVoucher'])->name('promotion.voucher.update');
    Route::delete('/promotion/voucher/{id}', [\App\Http\Controllers\Admin\PromotionController::class, 'destroyVoucher'])->name('promotion.voucher.destroy');
    Route::post('/promotion/promo', [\App\Http\Controllers\Admin\PromotionController::class, 'storePromo'])->name('promotion.promo.store');
    Route::put('/promotion/promo/{id}', [\App\Http\Controllers\Admin\PromotionController::class, 'updatePromo'])->name('promotion.promo.update');
    Route::delete('/promotion/promo/{id}', [\App\Http\Controllers\Admin\PromotionController::class, 'destroyPromo'])->name('promotion.promo.destroy');

    // Shipping Management (AD16)
    Route::get('/shipping', [\App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('shipping.index');
    Route::post('/shipping/ekspedisi', [\App\Http\Controllers\Admin\ShippingController::class, 'storeEkspedisi'])->name('shipping.ekspedisi.store');
    Route::put('/shipping/ekspedisi/{id}', [\App\Http\Controllers\Admin\ShippingController::class, 'updateEkspedisi'])->name('shipping.ekspedisi.update');
    Route::delete('/shipping/ekspedisi/{id}', [\App\Http\Controllers\Admin\ShippingController::class, 'destroyEkspedisi'])->name('shipping.ekspedisi.destroy');
    Route::put('/shipping/ekspedisi/{id}/toggle', [\App\Http\Controllers\Admin\ShippingController::class, 'toggleEkspedisi'])->name('shipping.ekspedisi.toggle');
    Route::post('/shipping/update-resi', [\App\Http\Controllers\Admin\ShippingController::class, 'updateResi'])->name('shipping.update-resi');
    Route::post('/shipping/update-status', [\App\Http\Controllers\Admin\ShippingController::class, 'updateStatus'])->name('shipping.update-status');
    Route::post('/shipping/tracking-log', [\App\Http\Controllers\Admin\ShippingController::class, 'storeTrackingLog'])->name('shipping.tracking-log.store');

    // Report & Analytics (AD17)
    Route::get('/report', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('report.index');
    Route::get('/report/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('report.export');

    // Security & Audit Log (AD18)
    Route::get('/audit-log', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-log.index');

    // Customer Management (AD14)
    Route::get('/customer', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customer.index');
    Route::put('/customer/{id}/block', [\App\Http\Controllers\Admin\CustomerController::class, 'block'])->name('customer.block');

    // Review & Rating Moderation (AD13)
    Route::get('/review', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('review.index');
    Route::get('/review/{id}', [\App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('review.show');
    Route::delete('/review/{id}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('review.destroy');
    Route::post('/review/{id}/reply', [\App\Http\Controllers\Admin\ReviewController::class, 'reply'])->name('review.reply');

    // Customer Order Management (AD12)
    Route::get('/customer-order', [\App\Http\Controllers\Admin\CustomerOrderController::class, 'index'])->name('customer-order.index');
    Route::get('/customer-order/{kode_transaksi}', [\App\Http\Controllers\Admin\CustomerOrderController::class, 'show'])->name('customer-order.show');
    Route::post('/customer-order/{id}/verify-payment', [\App\Http\Controllers\Admin\CustomerOrderController::class, 'verifyPayment'])->name('customer-order.verify-payment');
    Route::put('/customer-order/{id}/status', [\App\Http\Controllers\Admin\CustomerOrderController::class, 'updateStatus'])->name('customer-order.update-status');
    Route::put('/customer-order/{id}/resi', [\App\Http\Controllers\Admin\CustomerOrderController::class, 'updateResi'])->name('customer-order.update-resi');
    Route::get('/customer-order/{id}/invoice', [\App\Http\Controllers\Admin\CustomerOrderController::class, 'invoicePdf'])->name('customer-order.invoice-pdf');

    // Supplier Order (AD11)
    Route::get('/supplier-order', [\App\Http\Controllers\Admin\SupplierOrderController::class, 'index'])->name('supplier-order.index');
    Route::get('/supplier-order/create', [\App\Http\Controllers\Admin\SupplierOrderController::class, 'create'])->name('supplier-order.create');
    Route::post('/supplier-order', [\App\Http\Controllers\Admin\SupplierOrderController::class, 'store'])->name('supplier-order.store');
    Route::get('/supplier-order/{id}', [\App\Http\Controllers\Admin\SupplierOrderController::class, 'show'])->name('supplier-order.show');
    Route::post('/supplier-order/{id}/receive', [\App\Http\Controllers\Admin\SupplierOrderController::class, 'receive'])->name('supplier-order.receive');
    Route::get('/supplier-order/{id}/invoice', [\App\Http\Controllers\Admin\SupplierOrderController::class, 'invoicePdf'])->name('supplier-order.invoice-pdf');
});
Route::get('/api/search-suggest', [SearchController::class, 'suggest']);

Route::get('/api/cart-count', function (Request $request) {
    if (!auth()->check()) {
        return response()->json(['count' => 0]);
    }
    $ownerColumn = \App\Models\Keranjang::ownerColumn();
    $ownerId = \App\Models\Keranjang::resolveOwnerId(auth()->user());
    $count = $ownerId
        ? \App\Models\Keranjang::where($ownerColumn, $ownerId)->distinct()->count('detail_produk_id')
        : 0;
    return response()->json(['count' => $count]);
});

Route::get('/api/wishlist-count', function (Request $request) {
    if (!auth()->check()) {
        return response()->json(['count' => 0]);
    }
    $ownerColumn = \App\Models\Wishlist::ownerColumn();
    $ownerId = \App\Models\Wishlist::resolveOwnerId(auth()->user());
    $count = $ownerId ? \App\Models\Wishlist::where($ownerColumn, $ownerId)->count() : 0;
    return response()->json(['count' => $count]);
});

Route::post('/api/wishlist/toggle', [ApiWishlistController::class, 'toggle'])->middleware('auth');

Route::post('/api/cart/add', [ApiCartController::class, 'add'])->middleware('auth');

Route::post('/api/cart/update', [ApiCartController::class, 'update'])->middleware('auth');
Route::delete('/api/cart/remove/{id}', [ApiCartController::class, 'remove'])->middleware('auth');

Route::post('/api/review/store', [ApiReviewController::class, 'store'])->middleware('auth');
Route::post('/review/store', [ApiReviewController::class, 'store'])->middleware('auth')->name('review.store');
----- END routes/web.php -----


## FILE: resources/views/buyer/profile/index.blade.php

----- START resources/views/buyer/profile/index.blade.php -----
@extends('layouts.buyer')

@section('title', __('ui.profile_my') . ' — MOVR')

@section('content')
@php
    use App\Models\Wishlist;

    $user = auth()->user();
    $userName = $user->nama_pengguna ?? $user->name ?? $user->username ?? 'User';
    $userEmail = $user->email ?? '';
    $userPhoto = $user->foto_profil ?? null;
    $orderCount = isset($orderCount) ? $orderCount : count($orders ?? []);
    $wishlistOwnerColumn = Wishlist::ownerColumn();
    $wishlistOwnerId = Wishlist::resolveOwnerId($user);
    $wishlistCount = $wishlistOwnerId ? Wishlist::where($wishlistOwnerColumn, $wishlistOwnerId)->count() : 0;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="{ activeTab: '{{ request('tab', 'profil') }}' }"
     x-init="if (window.location.hash) activeTab = window.location.hash.substring(1)"
     @hashchange.window="activeTab = window.location.hash.substring(1) || activeTab">
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <div class="lg:col-span-3 space-y-4 lg:sticky lg:top-8 self-start">
      <div class="bg-white rounded-3xl p-5 shadow-sm text-center">
        <div class="relative w-20 h-20 mx-auto mb-3">
          @if($userPhoto)
            <img src="{{ Storage::url($userPhoto) }}"
                 alt="Profile"
                 class="w-20 h-20 rounded-full object-cover ring-4 ring-[#63A2BB]/20">
          @else
            <div class="w-20 h-20 rounded-full bg-[#63A2BB] flex items-center justify-center text-white text-2xl font-black">
              {{ strtoupper(substr($userName, 0, 2)) }}
            </div>
          @endif
          <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-400 rounded-full border-2 border-white"></div>
        </div>

        <p class="font-bold text-gray-800 text-base">{{ $userName }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $user->username ?? '' }}</p>
        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $userEmail }}</p>

        <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-gray-100">
          <div class="text-center">
            <p class="font-black text-[#63A2BB] text-lg">{{ $orderCount }}</p>
            <p class="text-[11px] text-gray-400">{{ __('ui.orders_total') }}</p>
          </div>
          <div class="text-center">
            <p class="font-black text-[#63A2BB] text-lg">{{ $wishlistCount }}</p>
            <p class="text-[11px] text-gray-400">Wishlist</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-3 shadow-sm">
        <button @click="activeTab = 'profil'; history.replaceState({}, '', '?tab=profil')"
                :class="activeTab === 'profil' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          {{ __('ui.data_diri') }}
        </button>
        <button @click="activeTab = 'pesanan'; history.replaceState({}, '', '?tab=pesanan')"
                :class="activeTab === 'pesanan' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7l1 13h12l1-13M9 11h6"/>
          </svg>
          My Orders
        </button>
        <button @click="activeTab = 'alamat'; history.replaceState({}, '', '?tab=alamat')"
                :class="activeTab === 'alamat' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          </svg>
          {{ __('ui.alamat') }}
        </button>
        <button @click="activeTab = 'pembayaran'; history.replaceState({}, '', '?tab=pembayaran')"
                :class="activeTab === 'pembayaran' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          {{ __('ui.payment_method') }}
        </button>
        <button @click="activeTab = 'keamanan'; history.replaceState({}, '', '?tab=keamanan')"
                :class="activeTab === 'keamanan' ? 'bg-[#63A2BB]/10 text-[#63A2BB]' : 'text-gray-500 hover:bg-gray-50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all text-left mb-1 last:mb-0">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          {{ __('ui.security') }}
        </button>

        <div class="mt-3 pt-3 border-t border-gray-100">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-all text-left">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              {{ __('ui.logout') }}
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="lg:col-span-9 space-y-6">
      @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
          {{ session('success') }}
        </div>
      @endif
      @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div x-show="activeTab === 'profil'"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0">
        <div class="bg-white rounded-3xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-gray-800 text-lg">{{ __('ui.data_diri') }}</h2>
          </div>

          <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.full_name') }}</label>
                <input type="text" name="name"
                       value="{{ old('name', $user->nama_pengguna ?? $user->name ?? '') }}"
                       placeholder="{{ __('ui.full_name_placeholder') }}"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition @error('name') border-red-300 @enderror">
                @error('name')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.username') }}</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">@</span>
                  <input type="text" name="username"
                         value="{{ old('username', $user->username ?? '') }}"
                         placeholder="{{ __('ui.username') }}"
                         class="w-full pl-8 pr-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition @error('username') border-red-300 @enderror">
                </div>
                @error('username')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.email') }}</label>
                <input type="email" name="email"
                       value="{{ old('email', $userEmail) }}"
                       placeholder="email@contoh.com"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition @error('email') border-red-300 @enderror">
                @error('email')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.phone_number') }}</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">+62</span>
                  <input type="tel" name="no_telepon"
                         value="{{ old('no_telepon', ltrim($user->no_telepon ?? '', '+62')) }}"
                         placeholder="{{ __('ui.phone_placeholder') }}"
                         class="w-full pl-12 pr-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition @error('no_telepon') border-red-300 @enderror">
                </div>
                @error('no_telepon')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.birth_date') }}</label>
                <input type="date" name="tanggal_lahir"
                       value="{{ old('tanggal_lahir', $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('Y-m-d') : '') }}"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:outline-none text-sm transition @error('tanggal_lahir') border-red-300 @enderror">
                @error('tanggal_lahir')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ui.gender') }}</label>
                <select name="jenis_kelamin"
                        class="w-full px-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:outline-none text-sm transition bg-white @error('jenis_kelamin') border-red-300 @enderror">
                  <option value="">{{ __('ui.select_gender') }}</option>
                  <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'L' ? 'selected' : '' }}>{{ __('ui.male') }}</option>
                  <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'P' ? 'selected' : '' }}>{{ __('ui.female') }}</option>
                </select>
                @error('jenis_kelamin')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="border-t border-gray-100 pt-5 mb-5"
                 x-data="{
                   preview: '{{ $userPhoto ? Storage::url($userPhoto) : '' }}',
                   hasFile: false,
                   posX: '{{ trim(explode(' ', $user->foto_profil_position ?? '50% 50%')[0] ?? '50%') }}'.replace('%', ''),
                   posY: '{{ trim(explode(' ', $user->foto_profil_position ?? '50% 50%')[1] ?? '50%') }}'.replace('%', ''),
                   handleFile(e) {
                     const f = e.target.files[0];
                     if (!f) return;
                     this.hasFile = true;
                     const r = new FileReader();
                     r.onload = ev => this.preview = ev.target.result;
                     r.readAsDataURL(f);
                   }
                 }">
              <label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('ui.photo_profile') }}</label>
              <div class="rounded-3xl border border-gray-100 bg-[#F8FAFB] p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row items-center gap-5 sm:gap-6">
                  <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden bg-[#D9D9D9] flex-shrink-0 ring-4 ring-white shadow-sm">
                    <div x-show="preview"
                         x-cloak
                         class="w-full h-full bg-no-repeat bg-cover"
                         :style="`background-image: url('${preview}'); background-position: ${posX}% ${posY}%;`">
                    </div>
                    <div x-show="!preview" class="w-full h-full flex items-center justify-center text-[#1F2937] text-3xl sm:text-4xl font-light tracking-[0.05em]">
                      {{ strtoupper(substr($userName, 0, 2)) }}
                    </div>
                  </div>

                  <div class="flex-1 w-full space-y-4">
                    <div class="space-y-1.5">
                      <div class="flex items-center justify-between gap-3 text-xs font-semibold text-gray-500">
                        <span>{{ __('ui.drag_horizontal') }}</span>
                        <span x-text="posX + '%'">50%</span>
                      </div>
                      <input type="range" min="0" max="100" x-model="posX" class="w-full accent-[#63A2BB] h-2 rounded-full cursor-pointer">
                    </div>

                    <div class="space-y-1.5">
                      <div class="flex items-center justify-between gap-3 text-xs font-semibold text-gray-500">
                        <span>{{ __('ui.drag_vertical') }}</span>
                        <span x-text="posY + '%'">50%</span>
                      </div>
                      <input type="range" min="0" max="100" x-model="posY" class="w-full accent-[#63A2BB] h-2 rounded-full cursor-pointer">
                    </div>

                    <label class="flex items-center gap-3 px-5 py-3 bg-white border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-[#63A2BB] hover:bg-[#63A2BB]/5 transition group">
                      <svg class="w-5 h-5 text-gray-400 group-hover:text-[#63A2BB] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                      </svg>
                      <div>
                        <p class="text-sm font-semibold text-gray-600 group-hover:text-[#63A2BB] transition">
                          <span x-text="hasFile ? 'Ganti foto' : 'Upload foto baru'"></span>
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG, GIF · Maks 10MB</p>
                      </div>
                      <input type="file" name="foto_profil" accept="image/*" class="hidden" @change="handleFile($event)">
                    </label>
                  </div>
                </div>

                <input type="hidden" name="foto_profil_position" :value="`${posX}% ${posY}%`">
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit" id="btn-submit-profile"
                      class="px-8 py-3.5 bg-[#63A2BB] text-white rounded-2xl font-bold text-sm hover:-translate-y-0.5 hover:bg-[#4A8BA3] hover:shadow-lg hover:shadow-[#63A2BB]/30 transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('ui.save_changes') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <div x-show="activeTab === 'pesanan'" x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-data="{
             activeStatus: '',
             selectedOrder: null,
             loadingDetail: false,

             async openDetail(kode) {
               this.loadingDetail = true;
               try {
                 const res = await fetch(
                   '/orders/' + kode + '/json',
                   { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                 );
                 const data = await res.json();
                 this.selectedOrder = data;
               } catch (e) {
                 showToast('Gagal memuat detail', 'error');
               } finally {
                 this.loadingDetail = false;
               }
             },

             closeDetail() {
               this.selectedOrder = null;
             }
           }">

        <div class="flex items-center justify-between mb-4">
          <h2 class="font-bold text-gray-800 text-lg">
            My Orders
          </h2>
          <p class="text-xs text-gray-400">
            Klik Detail untuk melihat pesanan
          </p>
        </div>

        <div class="flex gap-2 overflow-x-auto pb-2 mb-5 scrollbar-hide -mx-1 px-1">
          @php
            $statusTabs = [
              '' => 'Semua',
              'menunggu_pembayaran' => 'Belum Bayar',
              'pembayaran_dikonfirmasi' => 'Dikonfirmasi',
              'dikirim' => 'Dikirim',
              'selesai' => 'Selesai',
              'dibatalkan' => 'Dibatalkan',
            ];
          @endphp
          @foreach($statusTabs as $val => $label)
            @php
              $count = isset($orderCounts[$val]) ? $orderCounts[$val] : 0;
            @endphp
            <button @click="activeStatus = '{{ $val }}'; selectedOrder = null"
                    :class="activeStatus === '{{ $val }}'
                      ? 'bg-[#63A2BB] text-white shadow-sm'
                      : 'bg-white text-gray-500 border border-gray-200 hover:border-[#63A2BB] hover:text-[#63A2BB]'"
                    class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all whitespace-nowrap">
              {{ $label }}
              @if($val === 'menunggu_pembayaran' && $count > 0)
                <span class="bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                  {{ $count }}
                </span>
              @endif
            </button>
          @endforeach
        </div>

        <div class="grid grid-cols-1 gap-4"
             :class="selectedOrder ? 'lg:grid-cols-2' : 'lg:grid-cols-1'">

          <div class="space-y-3">
            @forelse($semuaPesanan ?? [] as $t)
              @php
                $statusConfig = [
                  'menunggu_pembayaran' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'Belum Bayar'],
                  'pembayaran_dikonfirmasi' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'Dikonfirmasi'],
                  'diproses' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'label' => 'Diproses'],
                  'dikirim' => ['bg' => 'bg-[#63A2BB]/10', 'text' => 'text-[#63A2BB]', 'label' => 'Dikirim'],
                  'selesai' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'label' => 'Selesai'],
                  'dibatalkan' => ['bg' => 'bg-red-50', 'text' => 'text-red-500', 'label' => 'Dibatalkan'],
                ];
                $sc = $statusConfig[$t->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'label' => ucfirst($t->status)];
              @endphp
              <div x-show="activeStatus === '' || activeStatus === '{{ $t->status }}'"
                   class="bg-white rounded-2xl p-4 shadow-sm border-2 transition-all cursor-pointer hover:border-[#63A2BB]/30"
                   :class="selectedOrder?.kode_transaksi === '{{ $t->kode_transaksi }}'
                     ? 'border-[#63A2BB]'
                     : 'border-transparent'">

                <div class="flex items-center justify-between mb-3">
                  <div>
                    <p class="text-xs text-gray-400">
                      {{ is_string($t->tanggal) ? \Carbon\Carbon::parse($t->tanggal)->format('d M Y, H:i') : $t->tanggal->format('d M Y, H:i') }}
                    </p>
                    <p class="font-bold text-gray-700 text-sm mt-0.5">
                      {{ $t->kode_transaksi }}
                    </p>
                  </div>
                  <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                    {{ $sc['label'] }}
                  </span>
                </div>

                @foreach($t->transaksiDetail->take(1) as $d)
                  <div class="flex items-center gap-3 mb-3">
                    <img src="{{ $d->detailProduk->produk->gambarUtama?->url_safe ?? asset('images/placeholder.png') }}"
                         class="w-12 h-12 rounded-xl object-cover flex-shrink-0 bg-gray-50"
                         alt="Produk">
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-700 line-clamp-1">
                        {{ $d->nama_produk_snap }}
                      </p>
                      <p class="text-xs text-gray-400 mt-0.5">
                        {{ $d->ukuran_snap }} · {{ $d->warna_snap }} · x{{ $d->quantity }}
                      </p>
                    </div>
                    <p class="text-sm font-bold text-gray-700 flex-shrink-0">
                      Rp {{ number_format($d->subtotal,0,',','.') }}
                    </p>
                  </div>
                @endforeach
                @if($t->transaksiDetail->count() > 1)
                  <p class="text-xs text-gray-400 mb-3">
                    +{{ $t->transaksiDetail->count()-1 }} produk lagi
                  </p>
                @endif

                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                  <div>
                    <p class="text-xs text-gray-400">Total</p>
                    <p class="font-black text-[#63A2BB] text-sm">
                      Rp {{ number_format($t->total_harga,0,',','.') }}
                    </p>
                  </div>
                  <div class="flex gap-2">
                    @if($t->status === 'menunggu_pembayaran')
                      <a href="{{ route('payment.show', $t->kode_transaksi) }}"
                         class="px-3 py-1.5 bg-[#63A2BB] text-white text-xs font-bold rounded-full hover:bg-[#4A8BA3] transition">
                        Bayar
                      </a>
                    @endif
                    <button @click="openDetail('{{ $t->kode_transaksi }}')"
                            class="px-4 py-1.5 border-2 border-gray-200 text-gray-500 text-xs font-semibold rounded-full hover:border-[#63A2BB] hover:text-[#63A2BB] transition">
                      Detail
                    </button>
                  </div>
                </div>
              </div>
            @empty
              <div class="bg-white rounded-2xl p-10 shadow-sm text-center">
                <div class="w-14 h-14 bg-[#63A2BB]/10 rounded-full flex items-center justify-center mx-auto mb-3">
                  <svg class="w-7 h-7 text-[#63A2BB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                  </svg>
                </div>
                <p class="text-gray-500 font-semibold text-sm">
                  Belum ada pesanan
                </p>
                <a href="/" class="mt-3 inline-flex text-xs text-[#63A2BB] font-semibold hover:underline">
                  Mulai Belanja ->
                </a>
              </div>
            @endforelse
          </div>

          <div x-show="selectedOrder !== null" x-cloak
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="opacity-0 translate-x-4"
               x-transition:enter-end="opacity-100 translate-x-0"
               class="lg:sticky lg:top-24 lg:self-start">

            <div x-show="loadingDetail"
                 class="bg-white rounded-2xl p-8 shadow-sm text-center">
              <svg class="animate-spin w-8 h-8 text-[#63A2BB] mx-auto"
                   fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <p class="text-sm text-gray-400 mt-3">Memuat detail...</p>
            </div>

            <div x-show="selectedOrder && !loadingDetail" x-cloak
                 class="bg-white rounded-2xl shadow-sm overflow-hidden">

              <div class="bg-[#63A2BB] p-4 flex items-center justify-between">
                <div>
                  <p class="text-white/70 text-xs">Detail Pesanan</p>
                  <p class="text-white font-bold text-sm mt-0.5"
                     x-text="selectedOrder?.kode_transaksi">
                  </p>
                </div>
                <button @click="closeDetail()"
                        class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition">
                  <svg class="w-4 h-4 text-white" fill="none"
                       stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

              <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="flex items-center gap-2">
                  <span class="text-xs font-bold px-3 py-1.5 rounded-full"
                        :class="{
                          'bg-amber-50 text-amber-600': selectedOrder?.status === 'menunggu_pembayaran',
                          'bg-blue-50 text-blue-600': selectedOrder?.status === 'pembayaran_dikonfirmasi',
                          'bg-[#63A2BB]/10 text-[#63A2BB]': selectedOrder?.status === 'dikirim',
                          'bg-green-50 text-green-600': selectedOrder?.status === 'selesai',
                          'bg-red-50 text-red-500': selectedOrder?.status === 'dibatalkan',
                          'bg-gray-50 text-gray-500': !['menunggu_pembayaran','pembayaran_dikonfirmasi','dikirim','selesai','dibatalkan'].includes(selectedOrder?.status),
                        }"
                        x-text="selectedOrder?.status_label">
                  </span>
                </div>

                <div>
                  <p class="text-xs font-bold text-gray-400 uppercase mb-2">
                    Produk
                  </p>
                  <template x-for="item in selectedOrder?.items ?? []" :key="item.id">
                    <div class="flex items-center gap-3 mb-2 last:mb-0">
                      <img :src="item.gambar" :alt="item.nama"
                           class="w-12 h-12 rounded-xl object-cover flex-shrink-0 bg-gray-50"
                           onerror="this.src='/images/placeholder.png'">
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-700 line-clamp-1"
                           x-text="item.nama">
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5"
                           x-text="item.ukuran + ' · ' + item.warna + ' · x' + item.qty">
                        </p>
                      </div>
                      <p class="text-sm font-bold text-gray-700 flex-shrink-0"
                         x-text="'Rp ' + item.subtotal.toLocaleString('id-ID')">
                      </p>
                    </div>
                  </template>
                </div>

                <div class="bg-gray-50 rounded-xl p-3">
                  <p class="text-xs font-bold text-gray-400 uppercase mb-1.5">
                    Alamat Kirim
                  </p>
                  <p class="font-semibold text-gray-700 text-sm"
                     x-text="selectedOrder?.penerima">
                  </p>
                  <p class="text-xs text-gray-500 mt-1 leading-relaxed"
                     x-text="selectedOrder?.alamat">
                  </p>
                  <p class="text-xs text-gray-400 mt-1"
                     x-text="selectedOrder?.telepon">
                  </p>
                </div>

                <div class="bg-gray-50 rounded-xl p-3">
                  <p class="text-xs font-bold text-gray-400 uppercase mb-1.5">
                    Pengiriman
                  </p>
                  <p class="text-sm font-semibold text-gray-700"
                     x-text="selectedOrder?.ekspedisi">
                  </p>
                  <p class="text-xs text-[#63A2BB] font-mono mt-1"
                     x-show="selectedOrder?.resi"
                     x-text="'Resi: ' + selectedOrder?.resi">
                  </p>
                  <p class="text-xs text-gray-400 mt-1"
                     x-show="selectedOrder?.estimasi"
                     x-text="'Estimasi: ' + selectedOrder?.estimasi">
                  </p>
                </div>

                <div>
                  <p class="text-xs font-bold text-gray-400 uppercase mb-2">
                    Rincian Pembayaran
                  </p>
                  <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                      <span>Subtotal Produk</span>
                      <span x-text="'Rp ' + (selectedOrder?.subtotal ?? 0).toLocaleString('id-ID')">
                      </span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                      <span>Ongkos Kirim</span>
                      <span x-text="'Rp ' + (selectedOrder?.ongkir ?? 0).toLocaleString('id-ID')">
                      </span>
                    </div>
                    <div x-show="selectedOrder?.diskon > 0"
                         class="flex justify-between text-green-600">
                      <span>Diskon Voucher</span>
                      <span x-text="'-Rp ' + (selectedOrder?.diskon ?? 0).toLocaleString('id-ID')">
                      </span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 flex justify-between font-bold">
                      <span class="text-gray-800">Total</span>
                      <span class="text-[#63A2BB]"
                            x-text="'Rp ' + (selectedOrder?.total ?? 0).toLocaleString('id-ID')">
                      </span>
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2.5">
                  <svg class="w-4 h-4 text-[#63A2BB]" fill="none"
                       stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                  <span class="text-sm font-medium text-gray-600"
                        x-text="selectedOrder?.metode_bayar ?? '-'"></span>
                </div>

                <div class="space-y-2 pt-1">
                  <template x-if="selectedOrder?.status === 'menunggu_pembayaran'">
                    <a :href="'/pay/' + selectedOrder?.kode_transaksi"
                       class="w-full flex items-center justify-center gap-2 bg-[#63A2BB] text-white py-3 rounded-xl font-bold text-sm hover:bg-[#4A8BA3] transition">
                      Bayar Sekarang
                    </a>
                  </template>
                  <template x-if="selectedOrder?.status === 'dikirim'">
                    <a :href="'/tracking/' + selectedOrder?.kode_transaksi"
                       class="w-full flex items-center justify-center gap-2 border-2 border-[#63A2BB] text-[#63A2BB] py-3 rounded-xl font-bold text-sm hover:bg-[#63A2BB] hover:text-white transition">
                      Lacak Paket
                    </a>
                  </template>
                  <template x-if="selectedOrder?.status === 'selesai' && !selectedOrder?.sudah_rating">
                    <a :href="'/orders/' + selectedOrder?.kode_transaksi + '/rating'"
                       class="w-full flex items-center justify-center bg-amber-500 text-white py-3 rounded-xl font-bold text-sm hover:bg-amber-600 transition">
                      Beri Rating
                    </a>
                  </template>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div x-show="activeTab === 'alamat'"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0"
           class="space-y-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="font-bold text-gray-800 text-lg">{{ __('ui.alamat') }}</h2>
          <button type="button" @click="$dispatch('open-modal', 'add-address-modal')"
                  class="flex items-center gap-2 px-4 py-2.5 bg-[#63A2BB] text-white rounded-2xl text-sm font-bold hover:bg-[#4A8BA3] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Alamat
          </button>
        </div>

        @forelse($addresses ?? [] as $address)
          <div class="bg-white rounded-3xl p-5 shadow-sm border-2 transition-all {{ $address->is_utama ? 'border-[#63A2BB]/30' : 'border-transparent hover:border-gray-200' }}">
            <div class="flex items-start justify-between gap-4">
              <div class="flex items-start gap-4 flex-1">
                <div class="w-10 h-10 rounded-2xl flex-shrink-0 {{ $address->is_utama ? 'bg-[#63A2BB]' : 'bg-gray-100' }} flex items-center justify-center">
                  <svg class="w-5 h-5 {{ $address->is_utama ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="font-bold text-gray-800 text-sm">{{ $address->label }}</span>
                    @if($address->is_utama)
                      <span class="bg-[#63A2BB]/10 text-[#63A2BB] text-[10px] font-bold px-2 py-0.5 rounded-full">★ Utama</span>
                    @endif
                  </div>
                  <p class="font-semibold text-gray-700 text-sm">{{ $address->nama_penerima }}</p>
                  <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    {{ $address->alamat_lengkap }},
                    Kel. {{ $address->kelurahan }},
                    Kec. {{ $address->kecamatan }},
                    {{ $address->kota }},
                    {{ $address->provinsi }}
                    {{ $address->kode_pos }}
                  </p>
                  <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $address->no_telepon }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-1 flex-shrink-0">
                <a href="{{ route('profile.address.edit', $address->alamat_id) }}" class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 hover:text-[#63A2BB] hover:bg-[#63A2BB]/10 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </a>
                @if(!$address->is_utama)
                  <form action="{{ route('profile.address.delete', $address->alamat_id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </form>
                @endif
              </div>
            </div>
            @if(!$address->is_utama)
              <div class="mt-4 pt-4 border-t border-gray-100">
                <form action="{{ route('profile.address.set-primary', $address->alamat_id) }}" method="POST" class="inline">
                  @csrf
                  @method('PUT')
                  <button type="submit" class="text-xs text-[#63A2BB] font-semibold hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Jadikan Alamat Utama
                  </button>
                </form>
              </div>
            @endif
          </div>
        @empty
          <div class="bg-white rounded-3xl p-12 shadow-sm text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              </svg>
            </div>
            <p class="text-gray-500 font-semibold mb-4">Belum ada alamat tersimpan</p>
            <button type="button" @click="$dispatch('open-modal', 'add-address-modal')"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-[#63A2BB] text-white rounded-2xl text-sm font-bold hover:bg-[#4A8BA3] transition">
              + Tambah Alamat Pertama
            </button>
          </div>
        @endforelse
      </div>

      <div x-show="activeTab === 'pembayaran'"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0"
           class="space-y-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="font-bold text-gray-800 text-lg">{{ __('ui.payment_method') }}</h2>
          <button type="button" @click="$dispatch('open-modal', 'add-payment-modal')"
                  class="flex items-center gap-2 px-4 py-2.5 bg-[#63A2BB] text-white rounded-2xl text-sm font-bold hover:bg-[#4A8BA3] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Metode
          </button>
        </div>

        @forelse($paymentMethods ?? [] as $method)
          <div class="bg-white rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#63A2BB]/10 flex items-center justify-center flex-shrink-0">
              @if($method->metodePembayaran?->logo_url)
                <img src="{{ $method->metodePembayaran->logo_url }}" class="h-6 object-contain" alt="{{ $method->metodePembayaran->metode ?? 'Metode' }}">
              @else
                <svg class="w-6 h-6 text-[#63A2BB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
              @endif
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <p class="font-bold text-gray-800 text-sm">{{ $method->metodePembayaran->metode ?? '-' }}</p>
                @if($method->is_utama)
                  <span class="bg-[#63A2BB]/10 text-[#63A2BB] text-[10px] font-bold px-2 py-0.5 rounded-full">Utama</span>
                @endif
              </div>
              <p class="text-sm text-gray-500 mt-0.5">{{ substr($method->nomor_akun, 0, 4) }}****{{ substr($method->nomor_akun, -4) }}</p>
              @if($method->nama_akun)
                <p class="text-xs text-gray-400 mt-0.5">a.n. {{ $method->nama_akun }}</p>
              @endif
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full font-medium">{{ ucfirst($method->metodePembayaran->jenis ?? '-') }}</span>
              <form action="{{ route('profile.payment-methods.delete', $method->akun_pembayaran_id) }}" method="POST" onsubmit="return confirm('Hapus metode ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </form>
            </div>
          </div>
        @empty
          <div class="bg-white rounded-3xl p-12 shadow-sm text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
              </svg>
            </div>
            <p class="text-gray-500 font-semibold">{{ __('ui.no_payment_methods') }}</p>
          </div>
        @endforelse
      </div>

      <div x-show="activeTab === 'keamanan'"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0">
        <div class="bg-white rounded-3xl p-6 shadow-sm" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
          <h2 class="font-bold text-gray-800 text-lg mb-6">Ubah Password</h2>
          <form action="{{ route('profile.change-password') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4 max-w-md">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Saat Ini</label>
                <div class="relative">
                  <input :type="showCurrent ? 'text' : 'password'" name="current_password" placeholder="••••••••" class="w-full px-4 py-3 pr-12 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition">
                  <button type="button" @click="showCurrent = !showCurrent" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
                @error('current_password')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                <div class="relative">
                  <input :type="showNew ? 'text' : 'password'" name="password" placeholder="••••••••" class="w-full px-4 py-3 pr-12 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition">
                  <button type="button" @click="showNew = !showNew" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
                @error('password')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <div class="relative">
                  <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" placeholder="••••••••" class="w-full px-4 py-3 pr-12 rounded-2xl border-2 border-gray-200 focus:border-[#63A2BB] focus:ring-2 focus:ring-[#63A2BB]/20 focus:outline-none text-sm transition">
                  <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
                @error('password_confirmation')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <div class="bg-[#63A2BB]/5 rounded-2xl p-4 text-xs text-gray-500 space-y-1">
                <p class="font-semibold text-gray-600 mb-2">Syarat password:</p>
                <p>• Minimal 8 karakter</p>
                <p>• Kombinasi huruf dan angka</p>
                <p>• Tidak sama dengan password lama</p>
              </div>
            </div>

            <div class="flex justify-start mt-6">
              <button type="submit" class="px-8 py-3.5 bg-[#63A2BB] text-white rounded-2xl font-bold text-sm hover:-translate-y-0.5 hover:bg-[#4A8BA3] hover:shadow-lg transition-all duration-200">
                Update Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div x-data="{ show: false }"
     x-show="show"
     @open-modal.window="if ($event.detail === 'add-address-modal') show = true"
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false" aria-hidden="true"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div x-show="show" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
      <form action="{{ route('profile.address.store') }}" method="POST">
        @csrf
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Tambah Alamat Baru</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Label (Rumah/Kantor)</label>
              <input type="text" name="label" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm" placeholder="Contoh: Rumah">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nama Penerima</label>
              <input type="text" name="nama_penerima" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
              <input type="text" name="no_telepon" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Provinsi</label>
              <input type="text" name="provinsi" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
              <input type="text" name="kota" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
              <input type="text" name="kecamatan" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kelurahan/Desa</label>
              <input type="text" name="kelurahan" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
              <input type="text" name="kode_pos" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
              <textarea name="alamat_lengkap" rows="3" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm" placeholder="Nama jalan, gedung, no. rumah/unit"></textarea>
            </div>
            <div class="sm:col-span-2">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input id="is_utama" name="is_utama" type="checkbox" value="1" class="focus:ring-[#63A2BB] h-4 w-4 text-[#63A2BB] border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                  <label for="is_utama" class="font-medium text-gray-700">Jadikan alamat utama</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="submit" class="w-full inline-flex justify-center rounded-2xl border border-transparent shadow-sm px-4 py-2 bg-[#63A2BB] text-base font-medium text-white hover:bg-[#4A8BA3] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#63A2BB] sm:ml-3 sm:w-auto sm:text-sm">
            Simpan Alamat
          </button>
          <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-2xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#63A2BB] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Batal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div x-data="{ show: false }"
     x-show="show"
     @open-modal.window="if ($event.detail === 'add-payment-modal') show = true"
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false" aria-hidden="true"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div x-show="show" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <form action="{{ route('profile.payment-methods.store') }}" method="POST">
        @csrf
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Tambah Metode Pembayaran</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Pilih Bank / E-Wallet</label>
              <select name="metode_id" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm bg-white">
                <option value="">Pilih...</option>
                @foreach($availableMethods ?? [] as $method)
                  <option value="{{ $method->metode_id }}">{{ $method->metode }} ({{ ucfirst($method->jenis ?? '') }})</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nomor Rekening / No. Handphone</label>
              <input type="text" name="no_akun" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nama Pemilik Rekening</label>
              <input type="text" name="nama_akun" required class="mt-1 block w-full border-gray-300 rounded-2xl shadow-sm focus:ring-[#63A2BB] focus:border-[#63A2BB] sm:text-sm">
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="submit" class="w-full inline-flex justify-center rounded-2xl border border-transparent shadow-sm px-4 py-2 bg-[#63A2BB] text-base font-medium text-white hover:bg-[#4A8BA3] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#63A2BB] sm:ml-3 sm:w-auto sm:text-sm">
            Simpan Metode
          </button>
          <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-2xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#63A2BB] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Batal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
----- END resources/views/buyer/profile/index.blade.php -----


## FILE: resources/views/buyer/partials/header.blade.php

----- START resources/views/buyer/partials/header.blade.php -----
@php
    use App\Models\Wishlist;
    use App\Models\Keranjang;

    $isLoggedIn = auth()->check();
    $wishlistOwnerColumn = Wishlist::ownerColumn();
    $wishlistOwnerId = $isLoggedIn ? Wishlist::resolveOwnerId(auth()->user()) : null;
    $wishlistCount = $wishlistOwnerId ? Wishlist::where($wishlistOwnerColumn, $wishlistOwnerId)->count() : 0;

    $cartOwnerColumn = Keranjang::ownerColumn();
    $cartOwnerId = $isLoggedIn ? Keranjang::resolveOwnerId(auth()->user()) : null;
    $cartCount = $cartOwnerId ? Keranjang::where($cartOwnerColumn, $cartOwnerId)->distinct()->count('detail_produk_id') : 0;

    $user = auth()->user();
    $userName = $user->nama_pengguna ?? $user->nama ?? $user->name ?? 'User';
    $userEmail = $user->email ?? '';
    $userPhoto = $user->foto_profil ?? $user->foto ?? null;
@endphp

<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl" x-data="{ mobileOpen: false, searchQuery: '', notificationOpen: false, wishlistCount: {{ $wishlistCount }}, cartCount: {{ $cartCount }} }">
    <div class="section-shell">
        <div class="flex h-20 items-center justify-between gap-4">
            <div class="flex items-center gap-4 lg:gap-6">
                <a href="{{ route('home') }}" class="flex h-12 w-44 items-center justify-center rounded-2xl bg-slate-950 px-4 text-lg font-black tracking-[0.2em] text-white shadow-sm transition-all duration-200 hover:scale-[1.02] hover:shadow-lg hover:shadow-slate-400/20">
                    MOVR
                </a>

                <nav class="hidden xl:flex items-center gap-1" x-cloak>
                    @foreach(($menuKategori ?? []) as $kategori)
                        <div class="group relative">
                            <a href="#" class="rounded-full px-4 py-2 text-sm font-bold text-[#63A2BB] transition-all duration-200 hover:bg-[#63A2BB]/10 hover:text-[#4A8BA3]">
                                {{ $kategori->nama_kategori }}
                            </a>

                            <div class="absolute left-0 top-full pt-3 opacity-0 invisible translate-y-2 transition-all duration-200 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0">
                                <div class="w-72 rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/60">
                                    @foreach($kategori->children ?? [] as $sub)
                                        <div class="mb-4 last:mb-0">
                                            <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#63A2BB]">{{ $sub->nama_kategori }}</p>
                                            <div class="space-y-1">
                                                @foreach($sub->children ?? [] as $leaf)
                                                    <a href="{{ route('category.show', $leaf->slug) }}" class="block rounded-2xl px-3 py-2 text-sm text-slate-600 transition-all duration-200 hover:bg-[#63A2BB]/5 hover:text-[#63A2BB]">
                                                        {{ $leaf->nama_kategori }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>

            <div class="hidden flex-1 max-w-2xl xl:flex">
                <form action="{{ route('product.search') }}" method="GET" class="relative w-full">
                    <input type="text" name="q" x-model="searchQuery" placeholder="{{ __('ui.search_products') }}" class="w-full rounded-full border border-slate-200 bg-[#F1F5F8] px-5 py-3 pr-12 text-sm text-slate-700 outline-none transition-all duration-200 focus:border-[#63A2BB] focus:bg-white focus:ring-4 focus:ring-[#63A2BB]/15">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-[#63A2BB] p-2 text-white transition-all duration-200 hover:bg-[#4A8BA3] hover:scale-105">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.9-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition-all duration-200 hover:scale-105 hover:border-[#63A2BB] hover:text-[#63A2BB] xl:hidden" @click="mobileOpen = !mobileOpen">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ route('wishlist.index') }}" class="relative inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition-all duration-200 hover:scale-105 hover:border-[#63A2BB] hover:text-[#63A2BB]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 10-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span x-cloak x-show="wishlistCount > 0" x-text="wishlistCount" class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#EF4444] px-1.5 text-[10px] font-bold text-white shadow-md"></span>
                </a>

                                @auth
                                <div class="relative" x-data="{
                                    open: false,
                                    notifs: [],
                                    count: 0,
                                    async load() {
                                        try {
                                            const res = await fetch('/notifications/unread',
                                                { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                            const data = await res.json();
                                            this.notifs = data.notifs;
                                            this.count  = data.count;
                                        } catch(e) {}
                                    },
                                    async markRead(id, url) {
                                        await fetch('/notifications/' + id + '/read', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        });
                                        this.count = Math.max(0, this.count - 1);
                                        this.notifs = this.notifs.filter(n => n.id !== id);
                                        if (url) window.location.href = url;
                                    },
                                    async markAllRead() {
                                        await fetch('/notifications/read-all', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        });
                                        this.count = 0;
                                        this.notifs = [];
                                        showToast('Semua notifikasi ditandai dibaca');
                                    }
                                }" x-init="load(); setInterval(() => load(), 30000)">

                                    <button @click="open = !open"
                                                    @click.outside="open = false"
                                                    class="relative inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition-all duration-200 hover:scale-105 hover:border-[#63A2BB] hover:text-[#63A2BB]">
                                        <svg class="w-5 h-5 text-gray-600" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <span x-show="count > 0" x-cloak
                                                    x-text="count > 9 ? '9+' : count"
                                                    class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold min-w-[16px] h-4 px-0.5 rounded-full flex items-center justify-center leading-none">
                                        </span>
                                    </button>

                                    <div x-show="open" x-cloak
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                             class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">

                                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                            <span class="font-bold text-gray-800 text-sm">
                                                Notifikasi
                                            </span>
                                            <button x-show="count > 0" @click="markAllRead()"
                                                            class="text-xs text-[#63A2BB] hover:underline font-medium">
                                                Tandai semua dibaca
                                            </button>
                                        </div>

                                        <div class="max-h-72 overflow-y-auto">
                                            <template x-if="notifs.length === 0">
                                                <div class="px-4 py-8 text-center">
                                                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-2"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="1.5"
                                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                                    </svg>
                                                    <p class="text-xs text-gray-400">
                                                        Tidak ada notifikasi baru
                                                    </p>
                                                </div>
                                            </template>
                                            <template x-for="n in notifs" :key="n.id">
                                                <div @click="markRead(n.id, n.url)"
                                                         class="px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 cursor-pointer transition flex gap-3">
                                                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center mt-0.5"
                                                             :class="{
                                                                 'bg-[#63A2BB]/10': n.jenis === 'transaksi',
                                                                 'bg-green-50': n.jenis === 'pengiriman',
                                                                 'bg-amber-50': n.jenis === 'promo',
                                                                 'bg-gray-100': n.jenis === 'sistem',
                                                             }">
                                                        <span x-text="{
                                                            transaksi: '🛍️',
                                                            pengiriman: '📦',
                                                            promo: '🎁',
                                                            sistem: '⚙️'
                                                        }[n.jenis] ?? '🔔'">
                                                        </span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold text-gray-800 line-clamp-1" x-text="n.judul"></p>
                                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="n.pesan"></p>
                                                        <p class="text-[11px] text-gray-400 mt-1" x-text="n.waktu"></p>
                                                    </div>
                                                    <div class="w-2 h-2 bg-[#63A2BB] rounded-full flex-shrink-0 mt-2"></div>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                                            <a href="/notifications"
                                                 class="text-xs text-[#63A2BB] hover:underline font-medium">
                                                Lihat semua notifikasi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endauth

                <a href="{{ route('cart.index') }}" class="relative inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition-all duration-200 hover:scale-105 hover:border-[#63A2BB] hover:text-[#63A2BB]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H6.4M7 13L6.4 5M7 13l-1.5 3.5A1 1 0 007 18h10m-10 0a2 2 0 104 0m6 0a2 2 0 104 0" />
                    </svg>
                    <span x-cloak x-show="cartCount > 0" x-text="cartCount" class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#63A2BB] px-1.5 text-[10px] font-bold text-white shadow-md"></span>
                </a>

                <div class="hidden sm:flex items-center rounded-full border border-slate-200 bg-white p-1 text-xs font-semibold">
                    <a href="{{ route('language.switch', 'id') }}" class="rounded-full px-3 py-1.5 {{ app()->getLocale() === 'id' ? 'bg-[#63a2bb] text-white' : 'text-slate-600 hover:bg-slate-100' }}">ID</a>
                    <a href="{{ route('language.switch', 'en') }}" class="rounded-full px-3 py-1.5 {{ app()->getLocale() === 'en' ? 'bg-[#63a2bb] text-white' : 'text-slate-600 hover:bg-slate-100' }}">EN</a>
                </div>

                @auth
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-2 py-1.5 pr-4 transition-all duration-200 hover:scale-105 hover:border-[#63A2BB]">
                        @if($userPhoto)
                            <img src="{{ str_starts_with($userPhoto, 'http') ? $userPhoto : asset('storage/' . $userPhoto) }}" alt="avatar" class="h-10 w-10 rounded-full object-cover ring-2 ring-[#63A2BB]/20">
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#63A2BB] text-sm font-bold text-white ring-2 ring-[#63A2BB]/20">
                                {{ strtoupper(substr($userName, 0, 1)) }}
                            </div>
                        @endif
                        <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ $userName }}</span>
                    </a>
                @else
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="{{ route('login') }}" class="rounded-full px-5 py-3 text-sm font-semibold text-slate-700 transition-all duration-200 hover:bg-[#63A2BB]/10 hover:text-[#63A2BB]">{{ __('ui.login') }}</a>
                        <a href="{{ route('register') }}" class="btn-primary">{{ __('ui.register') }}</a>
                    </div>
                @endauth
            </div>
        </div>

        <div class="pb-4 xl:hidden" x-cloak x-show="mobileOpen">
            <div class="grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <form action="{{ route('product.search') }}" method="GET" class="relative">
                    <input type="text" name="q" placeholder="{{ __('ui.search_products') }}" class="w-full rounded-full border border-slate-200 bg-[#F1F5F8] px-4 py-3 text-sm outline-none focus:border-[#63A2BB] focus:ring-4 focus:ring-[#63A2BB]/15">
                </form>
                <div class="grid gap-2">
                    @foreach(($menuKategori ?? []) as $kategori)
                        <a href="#" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-[#63A2BB]/5 hover:text-[#63A2BB]">{{ $kategori->nama_kategori }}</a>
                    @endforeach
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('wishlist.index') }}" class="btn-outline">{{ __('ui.wishlist') }}</a>
                    <a href="{{ route('cart.index') }}" class="btn-outline">{{ __('ui.cart') }}</a>
                </div>
                @guest
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="btn-outline">{{ __('ui.login') }}</a>
                        <a href="{{ route('register') }}" class="btn-primary">{{ __('ui.register') }}</a>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</header>
----- END resources/views/buyer/partials/header.blade.php -----


## FILE: resources/views/layouts/admin.blade.php

----- START resources/views/layouts/admin.blade.php -----
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }

        :root {
            --admin-bg: #F8F9FA;
            --admin-brand: #2B9BAF;
            --admin-danger: #DC3545;
        }

        .sidebar-item-active { background: var(--admin-brand); color: #ffffff; }
        .sidebar-item { transition: background-color .15s ease, color .15s ease; }
    </style>
</head>
<body class="bg-[var(--admin-bg)] text-slate-900 font-sans">
    <div class="min-h-screen flex flex-col">
        {{-- TOPBAR --}}
        <header class="bg-white border-b border-slate-200">
            <div class="mx-auto max-w-7xl px-4 py-3">
                <div class="flex items-center gap-3 justify-between">
                                <div class="flex-1 flex items-center gap-3">
                        <form action="{{ url('/admin/search') }}" method="GET" class="w-full">
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="cari produk, order, atau analytics"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--admin-brand)]/25"
                            />
                        </form>
                    </div>

                    <div class="flex items-center gap-3">
                                                <div class="relative" x-data="{
                                                    open: false,
                                                    notifs: [],
                                                    count: 0,
                                                    async load() {
                                                        try {
                                                            const res = await fetch('/admin/notifications/unread',
                                                                { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                                            const data = await res.json();
                                                            this.notifs = data.notifs;
                                                            this.count  = data.count;
                                                        } catch(e) {}
                                                    },
                                                    async markRead(id, url) {
                                                        await fetch('/admin/notifications/' + id + '/read', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                                'X-Requested-With': 'XMLHttpRequest'
                                                            }
                                                        });
                                                        this.notifs = this.notifs.filter(n => n.id !== id);
                                                        this.count = Math.max(0, this.count - 1);
                                                        if (url) window.location.href = url;
                                                    },
                                                    async markAllRead() {
                                                        await fetch('/admin/notifications/read-all', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                                'X-Requested-With': 'XMLHttpRequest'
                                                            }
                                                        });
                                                        this.count = 0;
                                                        this.notifs = [];
                                                    }
                                                }" x-init="load(); setInterval(() => load(), 20000)">

                                                    <button @click="open = !open"
                                                                    @click.outside="open = false"
                                                                    class="relative p-2 rounded-xl hover:bg-gray-100 transition">
                                                        <svg class="w-5 h-5 text-gray-500" fill="none"
                                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                                        </svg>
                                                        <span x-show="count > 0" x-cloak
                                                                    x-text="count > 9 ? '9+' : count"
                                                                    class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold min-w-[16px] h-4 px-0.5 rounded-full flex items-center justify-center">
                                                        </span>
                                                    </button>

                                                    <div x-show="open" x-cloak
                                                             x-transition:enter="transition ease-out duration-150"
                                                             x-transition:enter-start="opacity-0 scale-95"
                                                             x-transition:enter-end="opacity-100 scale-100"
                                                             class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">

                                                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                                            <span class="font-bold text-gray-800 text-sm">
                                                                Notifikasi
                                                            </span>
                                                            <button x-show="count > 0" @click="markAllRead()"
                                                                            class="text-xs text-[#63A2BB] hover:underline">
                                                                Tandai semua dibaca
                                                            </button>
                                                        </div>

                                                        <div class="max-h-80 overflow-y-auto">
                                                            <template x-if="notifs.length === 0">
                                                                <div class="px-4 py-8 text-center">
                                                                    <p class="text-xs text-gray-400">
                                                                        Tidak ada notifikasi baru
                                                                    </p>
                                                                </div>
                                                            </template>
                                                            <template x-for="n in notifs" :key="n.id">
                                                                <div @click="markRead(n.id, n.url)"
                                                                         class="px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 cursor-pointer transition flex gap-3">
                                                                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center"
                                                                             :class="{
                                                                                 'bg-[#63A2BB]/10': n.jenis === 'transaksi',
                                                                                 'bg-green-50': n.jenis === 'pengiriman',
                                                                                 'bg-amber-50': n.jenis === 'promo',
                                                                                 'bg-red-50': n.jenis === 'stok',
                                                                                 'bg-gray-100': n.jenis === 'sistem',
                                                                             }">
                                                                        <span x-text="{
                                                                            transaksi: '🛒',
                                                                            pengiriman: '📦',
                                                                            promo: '🎁',
                                                                            stok: '⚠️',
                                                                            sistem: '⚙️'
                                                                        }[n.jenis] ?? '🔔'">
                                                                        </span>
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <p class="text-sm font-semibold text-gray-800 line-clamp-1" x-text="n.judul"></p>
                                                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="n.pesan"></p>
                                                                        <p class="text-[11px] text-gray-400 mt-1" x-text="n.waktu"></p>
                                                                    </div>
                                                                    <div class="w-2 h-2 bg-[#63A2BB] rounded-full mt-2 flex-shrink-0"></div>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                                                            <span class="text-xs text-gray-400 font-medium">
                                                                Notifikasi terbaru admin
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>


                        {{-- Avatar + Nama --}}
                        <div class="relative group">
                            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                <div class="h-8 w-8 rounded-full bg-[var(--admin-brand)] text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr((auth()->user()->nama ?? 'A'),0,1)) }}
                                </div>
                                <span class="hidden sm:inline">Admin Panel / Master Administrator</span>
                                <span class="text-slate-500">▼</span>
                            </button>

                            <div class="absolute right-0 mt-2 w-56 rounded-2xl border border-slate-200 bg-white shadow-sm hidden group-hover:block">
                                <a href="{{ url('/profile') }}" class="block px-4 py-3 text-sm hover:bg-slate-50">Profil</a>
                                <a href="{{ url('/profile') }}" class="block px-4 py-3 text-sm hover:bg-slate-50">Pengaturan</a>
                                <div class="border-t border-slate-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-3 text-left text-sm text-red-600 hover:bg-slate-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- BODY --}}
        <div class="flex flex-1">
            {{-- SIDEBAR --}}
            <aside class="w-80 bg-white border-r border-slate-200 shadow-sm">
                <div class="p-5">
                    <div class="flex flex-col items-start gap-2">
                        <div class="text-2xl font-black text-[var(--admin-brand)]">MOVR</div>
                        <div class="text-sm font-semibold tracking-[0.2em] text-slate-500">DASHBOARD</div>
                    </div>
                </div>

                <nav class="px-5 pb-5">
                    {{-- Grup: DASHBOARD --}}
                    <div class="mt-2">
                        <p class="text-xs font-bold uppercase text-slate-400">MOVR</p>
                        <a href="{{ url('/admin/dashboard') }}" class="sidebar-item mt-2 flex items-center justify-between rounded-xl px-3 py-2 text-sm {{ request()->is('admin/dashboard') ? 'sidebar-item-active' : 'text-slate-700' }}">
                            <span class="font-semibold">DASHBOARD</span>
                            <span class="text-slate-400">▸</span>
                        </a>
                    </div>

                    {{-- Grup: MASTER DATA --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">MASTER DATA</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.master-product.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/master-product*') ? 'sidebar-item-active' : 'text-slate-700' }}">Master Prod</a>
                            <a href="{{ route('admin.category.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/category*') ? 'sidebar-item-active' : 'text-slate-700' }}">Category</a>
                            <a href="{{ route('admin.supplier.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/supplier*') ? 'sidebar-item-active' : 'text-slate-700' }}">Supplier</a>
                        </div>
                    </div>

                    {{-- Grup: PRODUCT --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">PRODUCT</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.variant.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/variant*') ? 'sidebar-item-active' : 'text-slate-700' }}">Variant</a>
                            <a href="{{ route('admin.media.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/media*') ? 'sidebar-item-active' : 'text-slate-700' }}">Media</a>
                            <a href="{{ route('admin.pricing.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/pricing*') ? 'sidebar-item-active' : 'text-slate-700' }}">Pricing</a>
                        </div>
                    </div>

                    {{-- Grup: INVENTORY --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">INVENTORY</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.supplier-product.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/supplier-product*') ? 'sidebar-item-active' : 'text-slate-700' }}">Supplier Pr</a>
                            <a href="{{ route('admin.stock.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/stock') || request()->is('admin/stock/*') ? 'sidebar-item-active' : 'text-slate-700' }}">Stock</a>
                            <a href="{{ route('admin.stock-movement.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/stock-movement*') ? 'sidebar-item-active' : 'text-slate-700' }}">Stock Move</a>
                        </div>
                    </div>

                    {{-- Grup: TRANSACTION --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">TRANSACTION</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.supplier-order.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/supplier-order*') ? 'sidebar-item-active' : 'text-slate-700' }}">Supplier Or</a>
                            <a href="{{ route('admin.customer-order.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/customer-order*') ? 'sidebar-item-active' : 'text-slate-700' }}">Customer Or</a>
                        </div>
                    </div>

                    {{-- Grup: OTHER --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">OTHER</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.review.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/review*') ? 'sidebar-item-active' : 'text-slate-700' }}">Review</a>
                            <a href="{{ route('admin.customer.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/customer') || request()->is('admin/customer/*') ? 'sidebar-item-active' : 'text-slate-700' }}">Customer</a>
                            <a href="{{ route('admin.promotion.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/promotion*') ? 'sidebar-item-active' : 'text-slate-700' }}">Promotion</a>
                            <a href="{{ route('admin.shipping.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/shipping*') ? 'sidebar-item-active' : 'text-slate-700' }}">Shipping</a>
                            <a href="{{ route('admin.report.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/report*') ? 'sidebar-item-active' : 'text-slate-700' }}">Report</a>
                        </div>
                    </div>
                </nav>

                <div class="px-5 pb-6 mt-auto">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl bg-red-50 text-red-600 border border-red-100 px-4 py-3 text-sm font-semibold hover:bg-red-100">Sign Out</button>
                    </form>
                </div>
            </aside>

            {{-- MAIN CONTENT --}}
            <main class="flex-1">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

----- END resources/views/layouts/admin.blade.php -----


## FILE: resources/views/buyer/notifications/index.blade.php

----- START resources/views/buyer/notifications/index.blade.php -----
@extends('layouts.buyer')

@section('title', 'Notifikasi Saya — MOVR')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex items-center justify-between mb-5">
    <h1 class="text-xl font-black text-gray-800">Notifikasi Saya</h1>
    <a href="{{ route('profile.index', ['tab' => 'profil']) }}" class="text-sm font-semibold text-[#63A2BB] hover:underline">
      Kembali ke Profil
    </a>
  </div>

  <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-gray-100">
    @forelse($notifikasis as $n)
      <div class="px-5 py-4 border-b border-gray-50 last:border-b-0 flex gap-3">
        <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center mt-0.5 {{ $n->is_read ? 'bg-gray-100' : 'bg-[#63A2BB]/10' }}">
          <span>
            @switch($n->jenis)
              @case('transaksi') 🛍️ @break
              @case('pengiriman') 📦 @break
              @case('promo') 🎁 @break
              @case('sistem') ⚙️ @break
              @default 🔔
            @endswitch
          </span>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
            <p class="text-sm font-bold text-gray-800 line-clamp-1">{{ $n->judul }}</p>
            @if(! $n->is_read)
              <span class="w-2 h-2 bg-[#63A2BB] rounded-full"></span>
            @endif
          </div>
          <p class="text-sm text-gray-600 mt-0.5 line-clamp-2">{{ $n->pesan }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ optional($n->created_at)->diffForHumans() ?? '-' }}</p>
          @if($n->url_redirect)
            <a href="{{ $n->url_redirect }}" class="inline-flex mt-2 text-xs font-semibold text-[#63A2BB] hover:underline">
              Buka detail
            </a>
          @endif
        </div>
      </div>
    @empty
      <div class="px-6 py-12 text-center">
        <p class="text-sm text-gray-500">Belum ada notifikasi.</p>
      </div>
    @endforelse
  </div>

  @if(method_exists($notifikasis, 'links'))
    <div class="mt-5">
      {{ $notifikasis->links() }}
    </div>
  @endif
</div>
@endsection
----- END resources/views/buyer/notifications/index.blade.php -----

