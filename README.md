# ⚙️ InvenTrack: Smart Inventory Management System

**InvenTrack** adalah solusi manajemen inventaris modern yang dirancang untuk efisiensi operasional bengkel maupun gudang suku cadang. Aplikasi ini mengintegrasikan pemantauan stok secara *real-time* dengan sistem pelaporan yang komprehensif.

## 🛠️ Arsitektur Teknologi
Project ini dibangun dengan *cutting-edge stack* untuk menjamin skalabilitas dan performa:
* **Core:** Laravel 12 & PHP 8.2
* **Frontend:** Vue.js 3 & Vuetify (Material Design)
* **Bridge:** Inertia.js (Membangun SPA tanpa kerumitan API tradisional)
* **Data Viz:** Chart.js untuk analisis tren pergerakan barang
* **Reporting:** DomPDF untuk dokumen laporan profesional

## 🚀 Fitur Unggulan
* **Intelligent Dashboard:** Pantau total aset, volume produk, dan peringatan stok kritis dalam satu layar.
* **Stock Analytics:** Visualisasi tren masuk dan keluar barang untuk membantu pengambilan keputusan restock.
* **Automated History:** Setiap pergerakan barang tercatat secara detail, termasuk log user yang bertanggung jawab.
* **Advanced Reporting:** Ekspor laporan periodik (Harian, Mingguan, Bulanan, Tahunan) serta filter custom periode tertentu.
* **Role Security:** Proteksi akses fitur laporan khusus untuk level Administrator.

## 📦 Instalasi
1.  **Clone Repository**
    ```bash
    git clone [https://github.com/ramadhaniindras/InvenTrack.git](https://github.com/ramadhaniindras/InvenTrack.git)
    ```
2.  **Environment Setup**
    * `composer install` & `npm install`
    * Konfigurasi `.env` (Database & App Key)
    * `php artisan migrate --seed`
3.  **Deployment**
    * `npm run dev` (Development)
    * `php artisan serve`

---
Developed by [Ramadhani Indra](https://github.com/ramadhaniindras)