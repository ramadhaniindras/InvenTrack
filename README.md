# 📦 InvenTrack v1.0

**InvenTrack** adalah sistem manajemen inventaris gudang modern yang dibangun dengan fokus pada kecepatan, keamanan, dan pengalaman pengguna yang "Josjis".

![Warehouse Illustration](/public/images/hero-warehouse.jpg)

## 🚀 Fitur Utama
* **Real-time Dashboard**: Pantau total produk, stok kritis, dan aktivitas transaksi secara instan.
* **Role-Based Access Control (RBAC)**: Keamanan berlapis antara **Admin** (Full Access) dan **Staff** (Inventory Only).
* **Smart Inventory**: Manajemen kategori produk dengan sistem validasi yang ketat.
* **Transaction Logs**: Riwayat keluar masuk barang yang tercatat secara otomatis.
* **Modern UI/UX**: Menggunakan Vuetify 3 dengan desain isometrik yang bersih dan responsif.

## 🛠 Tech Stack
Aplikasi ini dibangun menggunakan teknologi mutakhir:
* **Backend**: [Laravel 12](https://laravel.com) (The PHP Framework for Web Artisans)
* **Frontend**: [Vue 3](https://vuejs.org/) dengan [Inertia.js](https://inertiajs.com/) (Monolith feel, SPA power)
* **UI Component**: [Vuetify 3](https://vuetifyjs.com/) (Material Design Component Framework)
* **Database**: MySQL / SQLite

## 🔧 Instalasi
1. Clone repository ini
2. Jalankan `composer install` & `npm install`
3. Copy `.env.example` ke `.env` dan sesuaikan database
4. Jalankan `php artisan migrate --seed`
5. Jalankan `php artisan serve` & `npm run dev`

---
Built with 🔥 by [Ramadhani Indras](https://github.com/ramadhaniindras)