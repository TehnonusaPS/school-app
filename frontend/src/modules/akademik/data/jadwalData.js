// Mock data for Jadwal Pelajaran (Schedule), Ujian (Exams), and Tugas (Assignments)

// Helper to get relative dates for current month/year
const currentYear = new Date().getFullYear();
const currentMonth = String(new Date().getMonth() + 1).padStart(2, '0'); // e.g. "06" for June

export const mockScheduleSiswa = {
  // 1: Senin, 2: Selasa, 3: Rabu, 4: Kamis, 5: Jumat, 6: Sabtu
  1: [
    { id: 101, mulai: '07:30', selesai: '09:00', mapel: 'Matematika', kelas: 'XII MIPA 1', ruang: 'Lab B104', guru: 'Drs. Budi Santoso' },
    { id: 102, mulai: '09:15', selesai: '10:45', mapel: 'Fisika', kelas: 'XII MIPA 1', ruang: 'Lab Fisika', guru: 'Siti Aminah, M.Pd' },
    { id: 103, mulai: '11:00', selesai: '12:30', mapel: 'Bahasa Indonesia', kelas: 'XII MIPA 1', ruang: 'Ruang 12', guru: 'Sri Wahyuni, S.Pd' }
  ],
  2: [
    { id: 201, mulai: '07:30', selesai: '09:00', mapel: 'Kimia', kelas: 'XII MIPA 1', ruang: 'Lab Kimia A', guru: 'Dr. Agus Wijaya' },
    { id: 202, mulai: '09:15', selesai: '10:45', mapel: 'Biologi', kelas: 'XII MIPA 1', ruang: 'Lab Biologi', guru: 'Hj. Endang Lestari' }
  ],
  3: [
    { id: 301, mulai: '07:30', selesai: '09:00', mapel: 'Bahasa Inggris', kelas: 'XII MIPA 1', ruang: 'Ruang 12', guru: 'John Doe, MA' },
    { id: 302, mulai: '09:15', selesai: '10:45', mapel: 'Sejarah Indonesia', kelas: 'XII MIPA 1', ruang: 'Ruang 12', guru: 'Dedi Kurniawan, S.Pd' },
    { id: 303, mulai: '11:00', selesai: '12:30', mapel: 'Pendidikan Agama', kelas: 'XII MIPA 1', ruang: 'Masjid Al-Ikhlas', guru: 'Ust. Muhammad Zaki' }
  ],
  4: [
    { id: 401, mulai: '07:30', selesai: '09:00', mapel: 'Matematika', kelas: 'XII MIPA 1', ruang: 'Lab B104', guru: 'Drs. Budi Santoso' },
    { id: 402, mulai: '09:15', selesai: '10:45', mapel: 'PPKn', kelas: 'XII MIPA 1', ruang: 'Ruang 12', guru: 'Hendro Wibowo, SH' }
  ],
  5: [
    { id: 501, mulai: '07:30', selesai: '09:00', mapel: 'Penjasorkes', kelas: 'XII MIPA 1', ruang: 'Lapangan Utama', guru: 'Rian Hidayat, S.Pd' },
    { id: 502, mulai: '09:15', selesai: '10:45', mapel: 'Seni Budaya', kelas: 'XII MIPA 1', ruang: 'Aula Seni', guru: 'Rina Herawati, S.Sn' }
  ],
  6: [
    { id: 601, mulai: '08:00', selesai: '10:00', mapel: 'Prakarya & Kewirausahaan', kelas: 'XII MIPA 1', ruang: 'Ruang 12', guru: 'Yanti Sugiarti, SE' }
  ]
};

export const mockScheduleGuru = {
  1: [
    { id: 111, mulai: '07:30', selesai: '09:00', mapel: 'Matematika', kelas: 'XII MIPA 1', ruang: 'Lab B104', guru: 'Drs. Budi Santoso' },
    { id: 112, mulai: '09:15', selesai: '10:45', mapel: 'Matematika', kelas: 'XII MIPA 2', ruang: 'Ruang 10', guru: 'Drs. Budi Santoso' }
  ],
  2: [
    { id: 211, mulai: '11:00', selesai: '12:30', mapel: 'Matematika', kelas: 'XI MIPA 3', ruang: 'Ruang 05', guru: 'Drs. Budi Santoso' }
  ],
  3: [
    { id: 311, mulai: '07:30', selesai: '09:00', mapel: 'Matematika', kelas: 'XII MIPA 2', ruang: 'Ruang 10', guru: 'Drs. Budi Santoso' }
  ],
  4: [
    { id: 411, mulai: '07:30', selesai: '09:00', mapel: 'Matematika', kelas: 'XII MIPA 1', ruang: 'Lab B104', guru: 'Drs. Budi Santoso' },
    { id: 412, mulai: '11:00', selesai: '12:30', mapel: 'Matematika', kelas: 'XI MIPA 3', ruang: 'Ruang 05', guru: 'Drs. Budi Santoso' }
  ],
  5: [],
  6: []
};

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
