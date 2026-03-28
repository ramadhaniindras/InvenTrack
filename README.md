# ⚙️ InvenTrack: Smart Inventory Management System

**InvenTrack** adalah alat bantu manajemen inventaris modern yang dirancang untuk efisiensi pemantauan stok. Aplikasi ini mengintegrasikan pemantauan stok secara _real-time_ dengan sistem pelaporan yang komprehensif.

---

## 🛠️ Arsitektur Teknologi

Project ini dibangun dengan _cutting-edge stack_ untuk menjamin skalabilitas dan performa:

- **Core:** Laravel 12 & PHP 8.2
- **Frontend:** Vue.js 3 & Vuetify (Material Design)
- **Bridge:** Inertia.js (SPA Experience)
- **Data Viz:** Chart.js untuk analisis tren pergerakan barang
- **Reporting:** DomPDF untuk dokumen laporan profesional
- **Scanner:** Html5-Qrcode untuk integrasi kamera hardware

---

## 🚀 Fitur Unggulan

- **📷 Intelligent QR Scanner:** Input dan update stok instan menggunakan kamera.
- **🖼️ Product Photo Management:** Lampirkan foto produk asli ke setiap SKU. Memudahkan admin gudang identifikasi barang secara fisik agar tidak tertukar.
- **📊 Smart Dashboard & Analytics:** Pantau total aset, volume produk, dan peringatan stok kritis secara visual dengan grafik interaktif.
- **🛡️ Secure Audit Trail:** Setiap pergerakan barang tercatat detail (Siapa, Kapan, Kenapa), menjamin transparansi 100% pada mutasi stok.
- **📄 Advanced Reporting:** Ekspor laporan periodik (Harian, Mingguan, Bulanan, Tahunan) ke format PDF.
- **🔐 Role-Based Access:** Proteksi fitur sensitif dan laporan khusus untuk level Administrator guna menjaga keamanan data.
- **📱 Responsive & Smooth UI:** Desain Minimalist yang enteng dan nyaman digunakan.

---

## 📦 Instalasi

1.  **Clone Repository**

    ```bash
    git clone [https://github.com/ramadhaniindras/InvenTrack.git](https://github.com/ramadhaniindras/InvenTrack.git)
    cd InvenTrack
    ```

2.  **Environment Setup**
    - `composer install` & `npm install`
    - Copy `.env.example` ke `.env` dan atur database.
    - `php artisan key:generate`
    - `php artisan migrate --seed`
    - `php artisan storage:link`

3.  **Deployment**
    Buka dua terminal:
    - Terminal 1: `npm run dev`
    - Terminal 2: `php artisan serve`

---

**Developed by [Ramadhani Indra S](https://github.com/ramadhaniindras)** _InvenTrack - Manage with ease._ ⚡
