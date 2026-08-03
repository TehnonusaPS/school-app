import api from '../api'

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

export const getStudents = async (params) => {
  const response = await api.get('/absensi/siswa', { params });
  return response.data;
};

export const getSummary = async () => {
  const students = await getStudents();
  return {
    totalSiswa: students.length,
    sudahHadir: students.filter(s => s.status === 'hadir').length,
    belumScan: students.filter(s => s.status === 'belum_absen').length,
    terlambat: students.filter(s => s.status === 'terlambat').length,
  };
};

export const getLogs = async () => {
  const response = await api.get('/absensi/siswa/logs');
  return response.data;
};

export const postScan = async (payload) => {
  // payload can be FormData (for real webcam upload) or standard object (for simulation)
  let response;
  if (payload instanceof FormData) {
    response = await api.post('/absensi/siswa/scan', payload, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  } else {
    response = await api.post('/absensi/siswa/scan', payload);
  }
  return response.data;
};

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

export const updateStudentStatus = async (studentId, status) => {
  const response = await api.post(`/absensi/siswa/${studentId}/status`, { status });
  return response.data;
};

export const getPersonalHistory = async (userId) => {
  await delay(SIMULATE_NETWORK_DELAY);
  simulateError();
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

  return [
    { id: 101, tanggal: '20 Mei 2026', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', jamMasuk: '07:12:45', jamKeluar: '10:15:20', status: 'hadir' },
    { id: 102, tanggal: '20 Mei 2026', nama: 'Bunga Citra', kelas: 'XI IPA 1', jamMasuk: '07:25:10', jamKeluar: '-', status: 'terlambat' },
    { id: 103, tanggal: '19 Mei 2026', nama: 'Bunga Citra', kelas: 'XI IPA 1', jamMasuk: '-', jamKeluar: '-', status: 'sakit' },
    { id: 104, tanggal: '18 Mei 2026', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', jamMasuk: '-', jamKeluar: '-', status: 'izin' },
    { id: 105, tanggal: '18 Mei 2026', nama: 'Elsa Novita', kelas: 'XI IPA 2', jamMasuk: '07:05:22', jamKeluar: '14:14:10', status: 'hadir' },
    { id: 106, tanggal: '17 Mei 2026', nama: 'Farhan Ramdan', kelas: 'XI IPA 2', jamMasuk: '-', jamKeluar: '-', status: 'alpa' },
  ];
};

export const getMonthlyGrid = async (params) => {
  const response = await api.get('/absensi/siswa/monthly-grid', { params });
  return response.data;
};

export const updateMonthlyCell = async (payload) => {
  const response = await api.post('/absensi/siswa/monthly-grid/update', payload);
  return response.data;
};

export const registerStudentFace = async (studentId, formData) => {
  const response = await api.post(`/absensi/siswa/${studentId}/register-face`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  });
  return response.data;
};

// ─── Real Staff Attendance & Leave APIs ──────────────────────────────────────

export const clockInStaff = async (formData) => {
  const response = await api.post('/absensi/clock-in', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  });
  return response.data;
};

export const getStaffAttendanceHistory = async () => {
  const response = await api.get('/absensi/history');
  return response.data;
};

export const submitLeaveRequest = async (payload) => {
  const response = await api.post('/absensi/leaves', payload);
  return response.data;
};

export const getStaffLeaveRequests = async () => {
  const response = await api.get('/absensi/leaves');
  return response.data;
};

export const getAdminLeaveRequests = async () => {
  const response = await api.get('/admin/absensi/leaves');
  return response.data;
};

export const actionLeaveRequest = async (id, payload) => {
  const response = await api.post(`/admin/absensi/leaves/${id}/action`, payload);
  return response.data;
};

export const getAbsensiSettings = async () => {
  const response = await api.get('/admin/absensi/settings');
  return response.data;
};

export const updateAbsensiSettings = async (payload) => {
  const response = await api.put('/admin/absensi/settings', payload);
  return response.data;
};
