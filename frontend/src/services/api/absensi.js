const SIMULATE_NETWORK_DELAY = 400; // ms
const ERROR_PROBABILITY = 0.05; // 5% chance of error

const initialStudents = [
  { id: 1, nisn: '0051234567', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: '07:12:45', jamKeluar: '10:15:20', status: 'hadir' },
  { id: 2, nisn: '0069876543', nama: 'Bunga Citra', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: '07:25:10', jamKeluar: null, status: 'terlambat' },
  { id: 3, nisn: '0054321987', nama: 'Cakra Khan', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: null, jamKeluar: null, status: 'belum_absen' },
  { id: 4, nisn: '0061122334', nama: 'Dian Sastro', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: '07:10:05', jamKeluar: '10:15:33', status: 'hadir' },
  { id: 5, nisn: '0055566778', nama: 'Elsa Novita', kelas: 'XI IPA 2', mapel: 'Biologi', jamMasuk: '07:05:22', jamKeluar: '10:14:10', status: 'hadir' },
  { id: 6, nisn: '0068899001', nama: 'Farhan Ramdan', kelas: 'XI IPA 2', mapel: 'Biologi', jamMasuk: '07:30:00', jamKeluar: null, status: 'terlambat' },
  { id: 7, nisn: '0052233445', nama: 'Gita Nirmala', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: null, jamKeluar: null, status: 'belum_absen' },
  { id: 8, nisn: '0067788990', nama: 'Hendra Saputra', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: '07:08:11', jamKeluar: '10:15:00', status: 'hadir' },
];

const initialLogs = [
  { id: 1, nama: 'Ahmad Fadil', kelas: 'XI IPA 1', inisial: 'AF', waktu: '07:12:45', tipe: 'Masuk' },
  { id: 2, nama: 'Dian Sastro', kelas: 'XI IPA 1', inisial: 'DS', waktu: '07:10:05', tipe: 'Masuk' },
];

function initDB() {
  if (!localStorage.getItem('api_students')) {
    localStorage.setItem('api_students', JSON.stringify(initialStudents));
  }
  if (!localStorage.getItem('api_logs')) {
    localStorage.setItem('api_logs', JSON.stringify(initialLogs));
  }
}

function delay(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function simulateError() {
  if (Math.random() < ERROR_PROBABILITY) {
    throw new Error('Gagal terhubung ke server. Silakan coba lagi.');
  }
}

// ─── API endpoints ─────────────────────────────────────────────────────────

export const getStudents = async () => {
  initDB();
  await delay(SIMULATE_NETWORK_DELAY);
  simulateError();
  return JSON.parse(localStorage.getItem('api_students'));
};

export const getSummary = async () => {
  initDB();
  await delay(SIMULATE_NETWORK_DELAY);
  simulateError();
  const students = JSON.parse(localStorage.getItem('api_students'));
  return {
    totalSiswa: students.length,
    sudahHadir: students.filter(s => s.status === 'hadir').length,
    belumScan: students.filter(s => s.status === 'belum_absen').length,
    terlambat: students.filter(s => s.status === 'terlambat').length,
  };
};

export const getLogs = async () => {
  initDB();
  await delay(SIMULATE_NETWORK_DELAY);
  simulateError();
  return JSON.parse(localStorage.getItem('api_logs'));
};

export const postScan = async (studentId, type = 'Masuk') => {
  initDB();
  await delay(SIMULATE_NETWORK_DELAY);
  simulateError();

  const students = JSON.parse(localStorage.getItem('api_students'));
  const logs = JSON.parse(localStorage.getItem('api_logs'));

  const student = students.find(s => s.id === studentId);
  if (!student) {
    throw new Error('Siswa tidak ditemukan');
  }

  const now = new Date();
  const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

  // Update student status
  if (type === 'Masuk') {
    student.jamMasuk = timeStr;
    // Simple logic for late check
    const isLate = now.getHours() >= 7 && now.getMinutes() > 15;
    student.status = isLate ? 'terlambat' : 'hadir';
  } else {
    student.jamKeluar = timeStr;
  }

  // Create log
  const initials = student.nama.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
  const newLog = {
    id: Date.now(),
    nama: student.nama,
    kelas: student.kelas,
    nisn: student.nisn,
    inisial: initials,
    waktu: timeStr,
    tipe: type,
  };

  logs.unshift(newLog); // prepend to log list

  // Save back
  localStorage.setItem('api_students', JSON.stringify(students));
  localStorage.setItem('api_logs', JSON.stringify(logs));

  return newLog;
};

// Khusus untuk Dashboard Guru (dummy clock in / out tanpa mengaitkan ke array siswa)
export const postGuruScan = async (type = 'in') => {
  await delay(SIMULATE_NETWORK_DELAY);
  simulateError();

  const now = new Date();
  const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
  const dateStr = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

  return {
    success: true,
    type,
    time: timeStr,
    date: dateStr
  };
};

// ─── Fitur Tambahan ────────────────────────────────────────────────────────

export const updateStudentStatus = async (studentId, status) => {
  initDB();
  await delay(SIMULATE_NETWORK_DELAY);
  simulateError();

  const students = JSON.parse(localStorage.getItem('api_students'));
  const student = students.find(s => s.id === studentId);
  if (!student) throw new Error('Siswa tidak ditemukan');

  student.status = status;
  // If setting to izin/sakit/alpa, reset their check-in times for simplicity
  if (['izin', 'sakit', 'alpa'].includes(status)) {
    student.jamMasuk = null;
    student.jamKeluar = null;
  }

  localStorage.setItem('api_students', JSON.stringify(students));
  return student;
};

export const getPersonalHistory = async (userId) => {
  await delay(SIMULATE_NETWORK_DELAY);
  simulateError();
  // Mock response for student personal history
  return [
    { id: 1, tanggal: '20 Mei 2026', mapel: 'Matematika', jamMasuk: '07:15', jamKeluar: '14:00', status: 'hadir' },
    { id: 2, tanggal: '19 Mei 2026', mapel: 'Fisika', jamMasuk: '07:20', jamKeluar: '13:50', status: 'hadir' },
    { id: 3, tanggal: '18 Mei 2026', mapel: 'Kimia', jamMasuk: '07:45', jamKeluar: '14:15', status: 'terlambat' },
    { id: 4, tanggal: '17 Mei 2026', mapel: 'Biologi', jamMasuk: '-', jamKeluar: '-', status: 'izin' },
    { id: 5, tanggal: '16 Mei 2026', mapel: 'Bahasa Inggris', jamMasuk: '07:10', jamKeluar: '14:00', status: 'hadir' },
  ];
};

export const getRecapData = async (startDate, endDate) => {
  initDB();
  await delay(SIMULATE_NETWORK_DELAY);
  simulateError();

  // Create mock recap based on api_students but randomized dates within the range
  // For simplicity, we just return a static mock list
  return [
    { id: 101, tanggal: '20 Mei 2026', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', jamMasuk: '07:12:45', jamKeluar: '10:15:20', status: 'hadir' },
    { id: 102, tanggal: '20 Mei 2026', nama: 'Bunga Citra', kelas: 'XI IPA 1', jamMasuk: '07:25:10', jamKeluar: '-', status: 'terlambat' },
    { id: 103, tanggal: '19 Mei 2026', nama: 'Bunga Citra', kelas: 'XI IPA 1', jamMasuk: '-', jamKeluar: '-', status: 'sakit' },
    { id: 104, tanggal: '18 Mei 2026', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', jamMasuk: '-', jamKeluar: '-', status: 'izin' },
    { id: 105, tanggal: '18 Mei 2026', nama: 'Elsa Novita', kelas: 'XI IPA 2', jamMasuk: '07:05:22', jamKeluar: '14:14:10', status: 'hadir' },
    { id: 106, tanggal: '17 Mei 2026', nama: 'Farhan Ramdan', kelas: 'XI IPA 2', jamMasuk: '-', jamKeluar: '-', status: 'alpa' },
  ];
};
