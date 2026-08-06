/**
 * useDefaultImages.js
 * Kumpulan gambar default lokal bertema Sekolah Dasar (SD)
 * tanpa identitas/tulisan tertentu agar netral dan profesional.
 * Disimpan secara lokal di /public/defaults/sd/ sehingga dijamin selalu
 * tampil cepat, stabil, dan tidak bergantung pada server luar/internet.
 */

// ─── Galeri Kegiatan Sekolah ──────────────────────────────────────────────────
export const GALLERY_DEFAULT_IMAGES = [
  '/defaults/sd/classroom.png',  // Suasana kelas & belajar aktif
  '/defaults/sd/outdoor.png',    // Membaca & belajar di taman sekolah
  '/defaults/sd/science.png',    // Eksperimen sains & tanaman
  '/defaults/sd/art.png',        // Kelas seni lukis & kreativitas
  '/defaults/sd/building.png',   // Gedung sekolah & area bermain
]

// ─── Program Unggulan / Kurikulum ─────────────────────────────────────────────
export const PROGRAM_DEFAULT_IMAGES = [
  '/defaults/sd/science.png',    // Program Sains & Eksplorasi
  '/defaults/sd/art.png',        // Program Seni & Kreativitas
  '/defaults/sd/classroom.png',  // Program Akademik & Literasi
  '/defaults/sd/outdoor.png',    // Program Karakter & Lingkungan
]

// ─── Tentang Sekolah / About ──────────────────────────────────────────────────
export const ABOUT_DEFAULT_IMAGES = [
  '/defaults/sd/building.png',   // Gedung utama sekolah
  '/defaults/sd/classroom.png',  // Fasilitas ruang kelas
  '/defaults/sd/outdoor.png',    // Halaman dan lingkungan sekolah
]

// ─── Tenaga Pendidik / Guru ───────────────────────────────────────────────────
export const TEACHER_DEFAULT_IMAGES = [
  '/defaults/sd/teacher_1.png',  // Guru perempuan 1
  '/defaults/sd/teacher_2.png',  // Guru pria 1
  '/defaults/sd/teacher_3.png',  // Guru perempuan 2
  '/defaults/sd/teacher_4.png',  // Guru pria 2
]

// ─── Logo / Brand Sekolah ─────────────────────────────────────────────────────
export const DEFAULT_SCHOOL_LOGO = '/defaults/sd/logo.png'

/**
 * Mengambil URL gambar default berdasarkan kategori dan index item.
 * Index dipakai supaya setiap kartu mendapat gambar berbeda (round-robin).
 * @param {'gallery'|'program'|'about'|'teacher'|'logo'} type
 * @param {number} index - Index item dalam loop (0-based)
 * @returns {string} URL gambar lokal
 */
export function getDefaultImage(type, index = 0) {
  if (type === 'logo') return DEFAULT_SCHOOL_LOGO

  const maps = {
    gallery: GALLERY_DEFAULT_IMAGES,
    program: PROGRAM_DEFAULT_IMAGES,
    about: ABOUT_DEFAULT_IMAGES,
    teacher: TEACHER_DEFAULT_IMAGES,
  }
  const list = maps[type] || GALLERY_DEFAULT_IMAGES
  return list[index % list.length]
}

