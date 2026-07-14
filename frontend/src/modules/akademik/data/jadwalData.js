// Mock data for Ujian (Exams) and Tugas (Assignments)

// Helper to get relative dates for current month/year
const currentYear = new Date().getFullYear();
const currentMonth = String(new Date().getMonth() + 1).padStart(2, '0'); // e.g. "06" for June

// Exam dates (format: YYYY-MM-DD)
export const mockExams = [
  { id: 1, tanggal: `${currentYear}-${currentMonth}-23`, nama: 'Ujian Tengah Semester Ganjil', mapel: 'Matematika', kelas: 'XII MIPA 1', waktu: '07:30 - 09:30', ruang: 'Aula Serbaguna' },
  { id: 2, tanggal: `${currentYear}-${currentMonth}-24`, nama: 'Ujian Tengah Semester Ganjil', mapel: 'Fisika', kelas: 'XII MIPA 1', waktu: '07:30 - 09:30', ruang: 'Aula Serbaguna' },
  { id: 3, tanggal: `${currentYear}-${currentMonth}-25`, nama: 'Ujian Tengah Semester Ganjil', mapel: 'Kimia', kelas: 'XII MIPA 1', waktu: '07:30 - 09:30', ruang: 'Aula Serbaguna' }
];

// Assignment deadlines (format: YYYY-MM-DD)
export const mockAssignments = [
  { id: 1, tanggal: `${currentYear}-${currentMonth}-10`, nama: 'Tugas Mandiri 1: Turunan Fungsi', mapel: 'Matematika', kelas: 'XII MIPA 1', deadline: '23:59 WIB', deskripsi: 'Kerjakan soal Latihan 2.1 halaman 45 buku paket.' },
  { id: 2, tanggal: `${currentYear}-${currentMonth}-15`, nama: 'Praktikum Mandiri: Vektor Gaya', mapel: 'Fisika', kelas: 'XII MIPA 1', deadline: '12:00 WIB', deskripsi: 'Kumpulkan laporan praktikum mandiri format PDF.' },
  { id: 3, tanggal: `${currentYear}-${currentMonth}-18`, nama: 'Tugas Analisis Cerpen', mapel: 'Bahasa Indonesia', kelas: 'XII MIPA 1', deadline: '23:59 WIB', deskripsi: 'Analisis unsur intrinsik cerpen "Robohnya Surau Kami".' }
];
