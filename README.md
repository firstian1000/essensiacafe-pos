# POS-Caffe (Essensia Koffie)

Aplikasi Point of Sale (POS) dan Pemesanan Mandiri berbasis QR Code untuk café modern.

---

## 🏛️ Struktur & Bagian-Bagian Utama Aplikasi

Aplikasi ini berbasis **Laravel (PHP)** dengan arsitektur web modern yang memadukan **AdminLTE Dashboard**, **Vite + Tailwind CSS**, **Integrasi Midtrans Payment Gateway**, **DomPDF (Struk & Laporan)**, serta **QR Code Generator**.

### 1. Modul Otentikasi & Keamanan ([`AdminLoginController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/Auth/AdminLoginController.php))
* **URL:** `/login` & `/logout`
* **Fungsi:** Mengatur akses masuk admin dan staf kasir ke dalam panel manajemen café.

### 2. Modul Admin Panel & Dashboard ([`DashboardController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/DashboardController.php))
* **URL:** `/` (Dashboard Utama)
* **Fungsi:**
  * Menampilkan statistik penjualan, total pendapatan, jumlah pesanan hari ini, serta grafik penjualan.
  * Fitur Export Laporan Penjualan (`/dashboard/export`).

### 3. Manajemen Kategori & Menu ([`CategoryController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/CategoryController.php) & [`MenuController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/MenuController.php))
* **URL:** `/categories` & `/menus`
* **Fungsi:**
  * **Kategori:** Kelola jenis produk (Kopi, Non-Kopi, Makanan Utama, Snack).
  * **Menu:** Tambah/edit menu, atur harga, deskripsi, upload gambar produk, toggle status ketersediaan (*available/unavailable*), serta rekomendasi menu harian (*is_recommended*).

### 4. Manajemen Meja & QR Code Self-Ordering ([`CafeTableController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/CafeTableController.php) & [`QrCodeController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/QrCodeController.php))
* **URL:** `/tables`
* **Fungsi:**
  * Mendaftarkan meja café dan secara otomatis membuat `qr_token` unik untuk tiap meja.
  * Unduh QR Code per meja (`/tables/{table}/download`) atau cetak sekaligus untuk seluruh meja (`/tables/print/all`).

### 5. Sistem Kasir (Point of Sale Direct) ([`CashierController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/CashierController.php))
* **URL:** `/cashier`
* **Fungsi:** Antarmuka khusus kasir untuk menginput pesanan langsung pelanggan di meja kasir, memproses pembayaran tunai/non-tunai, dan mencetak struk belanja (`/cashier/receipt/{order}`).

### 6. Portal Pemesanan Self-Service Pelanggan ([`CustomerMenuController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/Customer/MenuController.php))
* **URL:** `/order/{token}`
* **Fungsi:**
  * Ketika pelanggan memindai QR Code di meja mereka, halaman ini terbuka otomatis terikat ke nomor meja pelanggan.
  * Pelanggan dapat melihat menu berdasarkan kategori, melihat rekomendasi, dan menambahkan ke keranjang.

### 7. Keranjang & Checkout Pelanggan ([`CartController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/CartController.php) & [`CheckoutController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/CheckoutController.php))
* **URL:** `/cart`, `/checkout`, `/order/success/{order}`
* **Fungsi:**
  * Manajemen item keranjang (tambah, kurangi, hapus item).
  * Halaman checkout untuk pengisian nama pelanggan & metode pembayaran.
  * Integrasi Midtrans untuk pembayaran online (QRIS, GoPay, ShopeePay, Bank Transfer) serta opsi bayar tunai di kasir.

### 8. Riwayat Pesanan & Pembayaran ([`OrderController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/OrderController.php) & [`PaymentController.php`](file:///d:/10%20ouners/pos-caffe/app/Http/Controllers/PaymentController.php))
* **URL:** `/orders` & `/payments`
* **Fungsi:**
  * Pelacakan status pesanan secara real-time (*Pending -> Processing -> Completed*).
  * Pengelolaan status pembayaran dan cetak PDF bukti pembayaran/invoice.

---

## 🚀 Status Server & Cara Mengaksesnya

Server aplikasi dapat dijalankan secara lokal dengan command `php artisan serve`.

* **URL Server Lokal:** `http://127.0.0.1:8000`

### 🔑 Akun Admin (Halaman Login: `http://127.0.0.1:8000/login`)
* **Email:** `admin@cafe.com`
* **Password:** `password`

### 📋 Data Awal yang Sudah Disediakan (Seeded):
* **Kategori:** Coffee, Non-Coffee, Main Course, Snacks
* **Menu:** Espresso Single, Iced Cappuccino, Matcha Latte, Nasi Goreng Special, French Fries
* **Meja:** Meja 1 hingga Meja 5 (lengkap dengan QR Token masing-masing).
