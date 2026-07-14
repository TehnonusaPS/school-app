## Deskripsi

Menyelesaikan implementasi fitur Jadwal Pelajaran (Schedule) dari backend hingga frontend, mengintegrasikan CRUD Tahun Ajaran & Mata Pelajaran dengan API riil, serta memperbaiki kendala sinkronisasi data kelas & tipe data TypeScript.

- **Integrasi API**: Menghubungkan CRUD Tahun Ajaran (Semester Ganjil/Genap) dan Mata Pelajaran di frontend dengan API backend riil.
- **Fitur Jadwal Pelajaran (Backend)**: Membuat skema tabel `time_slots` dan `schedules`, mengimplementasikan controller dengan validasi konflik (tabrakan kelas, tabrakan guru, dan konsistensi hari mapel dalam 1 semester), serta membuat API lookup jadwal untuk Guru dan Siswa.
- **Manajemen Jadwal Admin (Frontend)**: Membuat area kerja interaktif *Weekly Grid*, konfigurasi jam pelajaran (menggunakan komponen Switch premium), serta panel real-time berisi mata pelajaran yang belum dijadwalkan (*Unassigned Subjects*).
- **Dashboard Akademik (Guru & Siswa)**: Menghubungkan halaman Jadwal Pelajaran Akademik dengan API riil serta menambahkan penanda badge visual **KBM** pada sel tanggal kalender.
- **Bug Fix & Type Safety**: Mengatasi bug *dropdown* kelas kosong pada admin dengan seeding kelas baru `2025/2026`, memindahkan profil siswa ke kelas aktif `2025/2026`, serta menyelesaikan type warning typescript (`isLesson` & `v-model:placeholder`) di `ScheduleCalendar.vue`.

## Jenis Perubahan

- [x] **Feature** - Menambah fitur baru
- [x] **Bug Fix** - Memperbaiki bug/error
- [x] **UI/UX** - Perubahan tampilan/desain

## 🧪 Bagaimana Cara Testing?

### Backend

1. Pastikan Anda berada di direktori `backend/`.
2. Jalankan database migration untuk menambahkan tabel baru:
   ```bash
   php artisan migrate
   ```
3. Jalankan data seeder untuk mengisi data tahun ajaran `2025/2026`, kelas baru, jam pelajaran, dan jadwal mengajar guru:
   ```bash
   php artisan db:seed --class=TimeSlotSeeder
   php artisan db:seed --class=AcademicYear2025Seeder
   ```
4. Pastikan dev server backend berjalan (`php artisan serve`).

### Frontend

1. Pastikan Anda berada di direktori `frontend/`.
2. Jalankan development server (`npm run dev`).
3. **Uji Peran Admin Sekolah** (`adminsekolah@mail.com`):
   - Masuk ke menu **Manajemen Data** $\rightarrow$ **Jadwal Pelajaran**.
   - Pilih Tahun Ajaran **2025/2026 (Ganjil)** dan Kelas **2-D**.
   - Coba ubah atau tambah jadwal baru dan pastikan validasi konflik (tabrakan kelas/guru) bekerja.
   - Klik tombol **Atur Jam Pelajaran** di kanan atas untuk memverifikasi fungsionalitas panel jam pelajaran yang minimalis.
4. **Uji Peran Guru** (`guru@mail.com`):
   - Masuk ke menu **Akademik** $\rightarrow$ **Jadwal Pelajaran**.
   - Pastikan sel tanggal Senin & Selasa memiliki badge **KBM** biru, dan daftar jadwal pelajaran mengajar terisi lengkap di panel detail kanan saat tanggal tersebut diklik.
5. **Uji Peran Siswa** (`siswa@mail.com`):
   - Masuk ke menu **Akademik** $\rightarrow$ **Jadwal Pelajaran**.
   - Verifikasi jadwal belajar kelas aktif sudah terisi dan terintegrasi secara otomatis.

## 📷 Screenshot/Video (Jika ada)

- *Desain Filter & Grid Baru*: Minimalis & Space-saving
- *Visualisasi Kalender*: Ditambahkan Badge KBM

## 📌 Catatan Tambahan

Semua perubahan tipe data dan sintaks di Vue telah divalidasi dengan sukses via bundler dan kompilasi static build (`npm run build`) berjalan normal 100% tanpa error.
