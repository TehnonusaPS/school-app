# 🏫 School App - Sistem Manajemen Sekolah Terintegrasi

Aplikasi manajemen sekolah modern dengan arsitektur modular yang dirancang untuk performa tinggi, kemudahan pemeliharaan, dan skalabilitas.

---

## 🛠️ Technology Stack

Aplikasi ini dibangun menggunakan teknologi mutakhir di ekosistem Frontend:

| Teknologi | Fungsi Utama |
| :--- | :--- |
| **Vue 3 (Composition API)** | Framework utama untuk membangun UI yang reaktif dan cepat. |
| **Vite** | Build tool generasi terbaru yang memberikan pengalaman development sangat cepat. |
| **Tailwind CSS v4** | Framework CSS utility-first untuk styling yang presisi dan modern. |
| **Shadcn UI (Vue)** | Koleksi komponen UI premium yang bisa di-custom sepenuhnya (Accessible & Clean). |
| **Pinia** | State Management resmi Vue untuk mengelola data global (seperti Sesi Login). |
| **Vue Router** | Navigasi antar halaman dengan dukungan nested routes dan metadata. |
| **Lucide Vue Next** | Set icon vektor yang ringan dan konsisten di seluruh aplikasi. |
| **Prettier** | Code formatter otomatis untuk menjaga standar kerapian kode tim. |

---

## 📂 Struktur Folder & Arsitektur

Project ini menggunakan **Arsitektur Modular** (Feature-based), di mana setiap fitur besar dipisahkan agar tidak saling tumpang tindih.

### 📁 `src/` (Source Code)
Diurutkan secara alfabetis:

*   **`assets/`**: File statis (logo, gambar) dan konfigurasi utama CSS (`main.css`).
*   **`components/`**: 
    *   **`ui/`**: Komponen dasar Shadcn yang di-install via CLI (Button, Card, Table).
    *   **Global**: Komponen lintas modul (Navbar, Sidebar, Breadcrumb).
*   **`composables/`**: Logika reaktif (stateful logic). Tempat menyimpan state `loading`, `error`, dan fungsi `execute` yang menghubungkan UI ke Service.
*   **`layouts/`**: Template pembungkus halaman. Saat ini menggunakan `SidebarLayout.vue` untuk area dashboard admin.
*   **`lib/`**: Utilitas konfigurasi library pihak ketiga (seperti helper `cn` untuk Tailwind).
*   **`modules/`**: **Pusat Fitur**. Dibagi berdasarkan modul sekolah:
    *   `absensi`, `akademik`, `auth` (Login), `dashboard`, `keuangan`, `manajemen-data`, dll.
    *   *Setiap modul memiliki:* `pages/` (View) dan `routes.js` (Definisi rute lokal).
*   **`router/`**: Konfigurasi routing pusat. Meng-import otomatis semua rute dari tiap modul.
*   **`services/`**: Logika networking murni. Berisi fungsi `async` untuk memanggil API (Stateless).
*   **`stores/`**: Global state menggunakan Pinia. Digunakan untuk data yang harus diakses di banyak tempat (misal: info user yang sedang login).
*   **`utils/`**: Fungsi pembantu (helper) murni. Contoh: `formatRupiah()`, `dateFormatter()`.

---

## 🚀 Panduan Memulai (Setup)

### 1. Persiapan Lingkungan
*   Install **Node.js** (Minimal versi 18.x).
*   Install **VS Code** dengan extension **Volar** dan **Prettier**.

### 2. Instalasi & Menjalankan
```bash
# 1. Clone repository
git clone <url-repo>

# 2. Install dependencies
npm install

# 3. Jalankan development server
npm run dev
```

### 3. Perintah Terminal
*   `npm run dev` : Server lokal (Hot Module Replacement).
*   `npm run format` : Merapikan seluruh kode project secara otomatis.
*   `npm run build` : Produksi (Hasilnya di folder `/dist`).

---

## 💡 Standar Alur Kerja (Workflow)

Untuk menjaga kualitas kode, developer diharapkan mengikuti pola berikut:

### **A. Menambah Fitur/Halaman Baru**
1.  Pilih modul di `src/modules/`.
2.  Buat file `.vue` di folder `pages/` modul tersebut.
3.  Daftarkan rute di file `routes.js` modul tersebut. 
    *   *Tips:* Gunakan `meta: { title: '...', parent: '...' }` untuk mendukung sistem Breadcrumb otomatis.

### **B. Komunikasi Data (API)**
Jangan memanggil `fetch` atau `axios` langsung di dalam komponen `.vue`. Gunakan pola:
1.  **Service**: Tulis fungsi ambil data mentah di `src/services/`.
2.  **Composable**: Buat hook di `src/composables/` untuk mengelola state `isLoading` dan `data`.
3.  **Page**: Panggil Composable tersebut di bagian `<script setup>`.

### **C. Styling & UI**
*   Prioritaskan penggunaan komponen yang sudah ada di `src/components/ui/`.
*   Gunakan class Tailwind untuk penyesuaian tata letak (Avoid writing manual CSS).

---

## 🧩 Sistem Pintar

*   **Breadcrumb Otomatis**: Sistem akan menelusuri `meta.parent` di router secara rekursif untuk membangun jalur navigasi.
*   **Sidebar Active State**: Sidebar akan tetap aktif (highlight) walaupun Anda berada di sub-halaman (misal: di halaman "Tambah Siswa", menu "Data Siswa" tetap aktif).
*   **Dark Mode Support**: Persistensi tema disimpan di `localStorage` dan dikelola melalui `AppSidebar`.

---
© 2024 School App Development Team.
