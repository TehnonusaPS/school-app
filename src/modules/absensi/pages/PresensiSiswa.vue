<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import {
  ArrowLeft,
  Camera,
  CameraOff,
  Fingerprint,
  Radio,
  CheckCircle,
  UserCheck,
  Clock,
  CalendarDays,
  Wifi,
  ShieldCheck,
} from 'lucide-vue-next'

const router = useRouter()

// ─── State ──────────────────────────────────────────────
const activeMode = ref('fingerprint') // 'kamera' | 'fingerprint' | 'rfid'
const absensiData = ref([])
const scanResults = ref([])

// ─── Clock State ─────────────────────────────────────────
const currentTime = ref('')
const currentDate = ref('')
let clockInterval = null

function updateClock() {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
  currentDate.value = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}

// ─── Scan State ─────────────────────────────────────────
const activeScannedSiswa = ref(null)
const scanSuccessMsg = ref('')
const isScanningSensor = ref(false)

// ─── Camera State ───────────────────────────────────────
const videoRef = ref(null)
const mediaStream = ref(null)
const cameraStatus = ref('idle') // idle | loading | active | error
const cameraError = ref('')

// ─── LocalStorage Persistence ───────────────────────────
function loadData() {
  const data = localStorage.getItem('absensiData')
  const logs = localStorage.getItem('scanResults')
  if (data) absensiData.value = JSON.parse(data)
  if (logs) scanResults.value = JSON.parse(logs)
}

function saveData() {
  localStorage.setItem('absensiData', JSON.stringify(absensiData.value))
  localStorage.setItem('scanResults', JSON.stringify(scanResults.value))
}

// ─── Initials Helper ────────────────────────────────────
function getInitials(nama) {
  if (!nama) return ''
  const parts = nama.split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return parts[0].substring(0, 2).toUpperCase()
}

// ─── Face Scan Simulation ───────────────────────────────
let faceScanInterval = null

function startFaceScanSimulation() {
  if (faceScanInterval) clearInterval(faceScanInterval)
  faceScanInterval = setInterval(() => {
    if (activeMode.value !== 'kamera' || cameraStatus.value !== 'active') return
    const unscanned = absensiData.value.filter(s => s.status === 'belum_absen')
    if (unscanned.length === 0) return
    const randomSiswa = unscanned[Math.floor(Math.random() * unscanned.length)]
    executeSuccessfulScan(randomSiswa.id, 'wajah')
  }, 8000)
}

function stopFaceScanSimulation() {
  if (faceScanInterval) { clearInterval(faceScanInterval); faceScanInterval = null }
}

// ─── Camera Handler ──────────────────────────────────────
async function startCamera() {
  if (activeMode.value !== 'kamera') return
  cameraStatus.value = 'loading'
  cameraError.value = ''
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
      audio: false,
    })
    mediaStream.value = stream
    cameraStatus.value = 'active'
    await nextTick()
    if (videoRef.value) {
      videoRef.value.srcObject = stream
      videoRef.value.play()
    }
    startFaceScanSimulation()
  } catch (err) {
    mediaStream.value = null
    cameraStatus.value = 'error'
    if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
      cameraError.value = 'Akses kamera ditolak. Mohon aktifkan izin kamera.'
    } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
      cameraError.value = 'Kamera tidak ditemukan pada perangkat Anda.'
    } else {
      cameraError.value = `Gagal memuat kamera: ${err.message}`
    }
  }
}

function stopCamera() {
  stopFaceScanSimulation()
  if (mediaStream.value) {
    mediaStream.value.getTracks().forEach(track => track.stop())
    mediaStream.value = null
  }
  if (videoRef.value) videoRef.value.srcObject = null
  cameraStatus.value = 'idle'
}

// ─── Scan Logic ──────────────────────────────────────────
async function triggerSensorScan(siswaId, type) {
  if (isScanningSensor.value) return
  isScanningSensor.value = true
  activeScannedSiswa.value = null
  scanSuccessMsg.value = ''
  await new Promise(resolve => setTimeout(resolve, 1200))
  isScanningSensor.value = false
  executeSuccessfulScan(siswaId, type)
}

function executeSuccessfulScan(siswaId, type) {
  const student = absensiData.value.find(s => s.id === siswaId)
  if (!student) return
  const now = new Date()
  const timeStr = now.toTimeString().split(' ')[0]
  let scanType = 'Masuk'
  if (!student.jamMasuk) {
    student.jamMasuk = timeStr
    const [h, m] = timeStr.split(':').map(Number)
    student.status = (h > 7 || (h === 7 && m > 20)) ? 'terlambat' : 'hadir'
    scanType = 'Masuk'
  } else if (!student.jamKeluar) {
    student.jamKeluar = timeStr
    scanType = 'Keluar'
  } else {
    student.jamKeluar = timeStr
    scanType = 'Keluar'
  }
  activeScannedSiswa.value = student
  scanSuccessMsg.value = `Absen ${scanType} Berhasil!`
  scanResults.value.unshift({
    id: Date.now(),
    nama: student.nama,
    inisial: getInitials(student.nama),
    kelas: student.kelas,
    waktu: timeStr,
    tipe: scanType,
    metode: type
  })
  saveData()
  setTimeout(() => {
    if (activeScannedSiswa.value?.id === student.id) activeScannedSiswa.value = null
  }, 3500)
}

// ─── Lifecycle & Watchers ──────────────────────────────
watch(activeMode, () => {
  activeScannedSiswa.value = null
  isScanningSensor.value = false
  // Kamera tidak otomatis menyala — user klik "Nyalakan Kamera" secara manual
  stopCamera()
})

onMounted(() => {
  loadData()
  updateClock()
  clockInterval = setInterval(updateClock, 1000)

  // ─── Restore tema & dark mode dari localStorage ─────────────────
  // Mengikuti logika yang sama dengan NavUser.vue
  const savedTheme = localStorage.getItem('theme')
  if (
    savedTheme === 'dark' ||
    (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)
  ) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }

  const savedThemeStyle = localStorage.getItem('themeStyle') || 'tahoe'
  // Hapus semua class theme-* lama dari body
  document.body.classList.forEach(cls => {
    if (cls.startsWith('theme-')) document.body.classList.remove(cls)
  })
  if (savedThemeStyle !== 'tahoe') {
    document.body.classList.add(`theme-${savedThemeStyle}`)
  }
})

onUnmounted(() => {
  stopCamera()
  if (clockInterval) clearInterval(clockInterval)
})

const filteredStudents = computed(() => absensiData.value)
</script>

<template>
  <div class="kiosk-root">

    <!-- ══ TOP HEADER BAR ══ -->
    <header class="kiosk-header">
      <button class="back-btn" @click="router.push('/absensi/siswa')">
        <ArrowLeft class="size-4" />
        Kembali
      </button>
      <div class="header-center">
        <h1 class="header-title">Absensi Web School</h1>
        <p class="header-sub">Sistem Presensi Biometrik Digital</p>
      </div>
      <div class="header-status">
        <span class="status-pulse" />
        <span class="status-label">Sistem Aktif</span>
        <ShieldCheck class="size-4" style="color: var(--primary)" />
      </div>
    </header>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="kiosk-main">

      <!-- LEFT: Scanner Viewport -->
      <section class="right-panel">
        <div class="scanner-card">

          <!-- Scanner Header -->
          <div class="scanner-topbar">
            <div class="scanner-topbar-left">
              <span class="scanner-dot" />
              <span class="scanner-mode-text">
                {{ activeMode === 'fingerprint' ? 'FINGERPRINT MODULE' : activeMode === 'rfid' ? 'RFID CARD READER' : 'CAMERA MODULE' }}
              </span>
            </div>
            <div class="scanner-topbar-right">
              <Wifi class="size-3.5" style="color: var(--muted-foreground)" />
              <span class="scanner-online">ONLINE</span>
            </div>
          </div>

          <!-- ─ MODE: FINGERPRINT ─ -->
          <template v-if="activeMode === 'fingerprint'">
            <div class="scanner-viewport">
              <div v-if="isScanningSensor" class="scan-anim">
                <div class="scan-ring-fp">
                  <div class="scan-ping-fp" />
                  <div class="scan-inner-fp" />
                  <Fingerprint class="size-14 scan-icon-fp" />
                  <div class="scan-laser-fp" />
                </div>
                <p class="scan-label-fp">Membaca Sidik Jari...</p>
              </div>
              <div v-else-if="activeScannedSiswa" class="success-box">
                <div class="success-icon">
                  <CheckCircle class="size-8" style="color: var(--primary)" />
                </div>
                <p class="success-msg">{{ scanSuccessMsg }}</p>
                <p class="success-name">{{ activeScannedSiswa.nama }}</p>
                <p class="success-meta">{{ activeScannedSiswa.kelas }}</p>
                <div class="success-time-row">
                  <Clock class="size-3" />
                  {{ activeScannedSiswa.jamKeluar ? 'Keluar' : 'Masuk' }}: {{ activeScannedSiswa.jamKeluar || activeScannedSiswa.jamMasuk }}
                </div>
                <span class="success-chip">FINGERPRINT VERIFIED</span>
              </div>
              <div v-else class="idle-state">
                <div class="idle-ring">
                  <Fingerprint class="size-14 idle-icon" />
                </div>
                <p class="idle-title">Mode Scan Aktif</p>
                <p class="idle-sub">Tempelkan jari pada sensor fingerprint</p>
              </div>
            </div>
          </template>

          <!-- ─ MODE: RFID ─ -->
          <template v-else-if="activeMode === 'rfid'">
            <div class="scanner-viewport">
              <div v-if="isScanningSensor" class="scan-anim">
                <div class="scan-ring-rfid">
                  <div class="scan-ping-rfid" />
                  <div class="scan-inner-rfid" />
                  <Radio class="size-14 scan-icon-rfid" />
                </div>
                <p class="scan-label-rfid">Membaca RFID Card...</p>
              </div>
              <div v-else-if="activeScannedSiswa" class="success-box">
                <div class="success-icon">
                  <CheckCircle class="size-8" style="color: var(--primary)" />
                </div>
                <p class="success-msg">{{ scanSuccessMsg }}</p>
                <p class="success-name">{{ activeScannedSiswa.nama }}</p>
                <p class="success-meta">{{ activeScannedSiswa.kelas }}</p>
                <div class="success-time-row">
                  <Clock class="size-3" />
                  {{ activeScannedSiswa.jamKeluar ? 'Keluar' : 'Masuk' }}: {{ activeScannedSiswa.jamKeluar || activeScannedSiswa.jamMasuk }}
                </div>
                <span class="success-chip">RFID CARD VERIFIED</span>
              </div>
              <div v-else class="idle-state">
                <div class="idle-ring idle-ring--rfid">
                  <Radio class="size-14 idle-icon" />
                </div>
                <p class="idle-title">Mode Scan Aktif</p>
                <p class="idle-sub">Dekatkan kartu RFID ke reader</p>
                <div class="rfid-input-row">
                  <Radio class="size-3.5" style="color: var(--muted-foreground)" />
                  <span class="rfid-scan-text">Scanning...</span>
                </div>
                <p class="rfid-hint">Jangan klik di luar kotak ini</p>
              </div>
            </div>
          </template>

          <!-- ─ MODE: KAMERA ─ -->
          <template v-else-if="activeMode === 'kamera'">
            <div class="scanner-viewport scanner-viewport--cam">
              <video v-if="cameraStatus === 'active'" ref="videoRef" autoplay playsinline class="cam-feed" />
              <div v-if="cameraStatus === 'active' && !activeScannedSiswa" class="cam-overlay">
                <span class="corner corner-tl" />
                <span class="corner corner-tr" />
                <span class="corner corner-bl" />
                <span class="corner corner-br" />
                <div class="cam-scan-line" />
              </div>
              <div v-if="cameraStatus === 'loading'" class="cam-state-wrap">
                <div class="cam-spinner" />
                <span class="cam-state-text">Menghubungkan Kamera...</span>
              </div>
              <div v-if="cameraStatus === 'error'" class="cam-state-wrap">
                <div class="cam-error-icon"><CameraOff class="size-7" /></div>
                <p class="cam-error-text">{{ cameraError }}</p>
                <button class="cam-retry-btn" @click="startCamera">Coba Ulang</button>
              </div>
              <div v-if="cameraStatus === 'idle'" class="cam-state-wrap">
                <div class="cam-idle-icon"><Camera class="size-10" style="color: var(--muted-foreground)" /></div>
                <p class="cam-idle-text">Kamera siap dinyalakan</p>
                <button class="cam-start-btn" @click="startCamera" disabled>
                  <Camera class="size-4" />
                  Nyalakan Kamera
                </button>
              </div>
              <div v-if="activeScannedSiswa && cameraStatus === 'active'" class="cam-success-overlay">
                <div class="success-box">
                  <div class="success-icon"><CheckCircle class="size-8" style="color: var(--primary)" /></div>
                  <p class="success-msg">{{ scanSuccessMsg }}</p>
                  <p class="success-name">{{ activeScannedSiswa.nama }}</p>
                  <p class="success-meta">{{ activeScannedSiswa.kelas }}</p>
                  <span class="success-chip">FACE SCAN VERIFIED</span>
                </div>
              </div>
            </div>
          </template>

          <!-- Scanner Footer -->
          <div class="scanner-footer">
            <div class="scanner-footer-left">
              <span class="footer-dot" />
              <span class="footer-text">DEVICE COMPLIANT · ONLINE</span>
            </div>
            <span class="footer-ver">
              {{ activeMode === 'fingerprint' ? 'FINGER_V2.1' : activeMode === 'rfid' ? 'RFID_RF4.0' : 'CAM_HD_1.0' }}
            </span>
          </div>

        </div>
      </section>

      <!-- RIGHT: Clock + Student List -->
      <section class="left-panel">

        <!-- Clock Card -->
        <div class="clock-card">
          <div class="clock-time">{{ currentTime }}</div>
          <div class="clock-date">
            <CalendarDays class="size-4" style="color: var(--muted-foreground)" />
            <span>{{ currentDate }}</span>
          </div>
          <div class="divider" />
          <!-- Mode Switcher -->
          <div class="mode-tabs">
            <button
              @click="activeMode = 'fingerprint'"
              :class="['mode-tab', activeMode === 'fingerprint' ? 'mode-tab--active' : '']"
            >
              <Fingerprint class="size-3.5" />
              Fingerprint
            </button>
            <button
              @click="activeMode = 'rfid'"
              :class="['mode-tab', activeMode === 'rfid' ? 'mode-tab--active' : '']"
            >
              <Radio class="size-3.5" />
              RFID Card
            </button>
            <button
              @click="activeMode = 'kamera'"
              :class="['mode-tab', activeMode === 'kamera' ? 'mode-tab--active' : '']"
            >
              <Camera class="size-3.5" />
              Kamera
            </button>
          </div>
        </div>

        <!-- Scan Log List -->
        <div class="log-card">
          <div class="log-header">
            <UserCheck class="size-4" style="color: var(--primary)" />
            <span class="log-title">Riwayat Scan</span>
            <span class="log-badge">{{ scanResults.length }}</span>
          </div>
          <div class="log-list">
            <div v-if="scanResults.length === 0" class="log-empty">
              <Clock class="size-5" style="color: var(--muted-foreground)" />
              <span>Menunggu pemindaian pertama...</span>
            </div>
            <div
              v-for="log in scanResults.slice(0, 8)"
              :key="'log-' + log.id"
              class="log-item"
            >
              <div class="log-avatar">{{ log.inisial }}</div>
              <div class="log-info">
                <p class="log-name">{{ log.nama }}</p>
                <p class="log-meta">{{ log.kelas }} · {{ log.tipe }} · {{ log.waktu }}</p>
              </div>
              <span :class="['log-badge-type', log.tipe === 'Masuk' ? 'badge-masuk' : 'badge-keluar']">
                {{ log.tipe }}
              </span>
            </div>
          </div>
        </div>

      </section>
    </main>
  </div>
</template>

<style scoped>
/* ══════════════════════════════════════════
   ROOT — pakai var() dari tema aktif
══════════════════════════════════════════ */
.kiosk-root {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: var(--background);
  color: var(--foreground);
  font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
}

/* ══════════════════════════════════════════
   HEADER
══════════════════════════════════════════ */
.kiosk-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.75rem;
  height: 60px;
  background: var(--card);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.875rem;
  border-radius: var(--radius-md, 8px);
  border: 1px solid var(--border);
  background: var(--muted);
  color: var(--muted-foreground);
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.back-btn:hover {
  background: var(--accent);
  color: var(--accent-foreground);
  border-color: var(--primary);
}

.header-center { text-align: center; }
.header-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--foreground);
  letter-spacing: -0.02em;
  margin: 0;
}
.header-sub {
  font-size: 0.7rem;
  color: var(--muted-foreground);
  margin-top: 1px;
}

.header-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.7rem;
  color: var(--foreground);
  font-weight: 600;
}
.status-pulse {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--primary);
  animation: kpulse 2s infinite;
}

/* ══════════════════════════════════════════
   MAIN GRID
══════════════════════════════════════════ */
.kiosk-main {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
  padding: 1.25rem;
  flex: 1;
  min-height: 0;
}

/* ══════════════════════════════════════════
   LEFT PANEL
══════════════════════════════════════════ */
.left-panel {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Clock Card */
.clock-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg, 14px);
  padding: 1.5rem 1.75rem;
  text-align: center;
  box-shadow: var(--glass-shadow, 0 2px 8px rgba(0,0,0,0.06));
}

.clock-time {
  font-size: 3.25rem;
  font-weight: 800;
  letter-spacing: -0.04em;
  color: var(--primary);
  font-variant-numeric: tabular-nums;
  line-height: 1;
}
.clock-date {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.375rem;
  font-size: 0.75rem;
  color: var(--muted-foreground);
  margin-top: 0.5rem;
}
.divider {
  height: 1px;
  background: var(--border);
  margin: 1.125rem 0;
}

/* Mode Tabs */
.mode-tabs {
  display: flex;
  gap: 0.25rem;
  background: var(--muted);
  border: 1px solid var(--border);
  border-radius: var(--radius-md, 10px);
  padding: 0.25rem;
}
.mode-tab {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.375rem;
  padding: 0.45rem 0.25rem;
  border-radius: calc(var(--radius-md, 10px) - 2px);
  border: none;
  background: transparent;
  color: var(--muted-foreground);
  font-size: 0.7rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.mode-tab:hover {
  color: var(--foreground);
  background: var(--accent);
}
.mode-tab--active {
  background: var(--background);
  color: var(--primary);
  border: 1px solid var(--border);
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

/* Log Card */
.log-card {
  flex: 1;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg, 14px);
  padding: 1.125rem;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: var(--glass-shadow, 0 2px 8px rgba(0,0,0,0.06));
}
.log-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--border);
  margin-bottom: 0.75rem;
}
.log-title {
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--foreground);
  flex: 1;
}
.log-badge {
  background: var(--accent);
  color: var(--primary);
  font-size: 0.65rem;
  font-weight: 700;
  padding: 0.125rem 0.5rem;
  border-radius: 999px;
  border: 1px solid var(--border);
}
.log-list {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}
.log-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 2rem;
  color: var(--muted-foreground);
  font-size: 0.75rem;
  text-align: center;
}
.log-item {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  padding: 0.5rem 0.625rem;
  border-radius: calc(var(--radius-md, 10px) - 2px);
  background: var(--muted);
  border: 1px solid var(--border);
  animation: kslideIn 0.25s ease;
}
.log-avatar {
  width: 30px; height: 30px;
  border-radius: 50%;
  background: var(--accent);
  border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  font-size: 0.6rem; font-weight: 700; color: var(--primary);
  flex-shrink: 0;
}
.log-info { flex: 1; min-width: 0; }
.log-name { font-size: 0.72rem; font-weight: 600; color: var(--foreground); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.log-meta { font-size: 0.62rem; color: var(--muted-foreground); margin-top: 1px; }
.log-badge-type {
  font-size: 0.58rem; font-weight: 700;
  padding: 0.1rem 0.4rem;
  border-radius: 4px; flex-shrink: 0;
}
.badge-masuk {
  background: color-mix(in oklch, var(--primary) 12%, transparent);
  color: var(--primary);
  border: 1px solid color-mix(in oklch, var(--primary) 25%, transparent);
}
.badge-keluar {
  background: oklch(0.75 0.15 45 / 0.12);
  color: oklch(0.65 0.18 45);
  border: 1px solid oklch(0.75 0.15 45 / 0.25);
}

/* ══════════════════════════════════════════
   RIGHT PANEL: SCANNER CARD
══════════════════════════════════════════ */
.right-panel { display: flex; flex-direction: column; }

.scanner-card {
  flex: 1;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg, 14px);
  padding: 1.125rem;
  display: flex;
  flex-direction: column;
  gap: 0.875rem;
  overflow: hidden;
  box-shadow: var(--glass-shadow, 0 2px 8px rgba(0,0,0,0.06));
}

.scanner-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--muted);
  border: 1px solid var(--border);
  border-radius: var(--radius-md, 10px);
  padding: 0.5rem 0.875rem;
}
.scanner-topbar-left { display: flex; align-items: center; gap: 0.5rem; }
.scanner-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--primary);
  animation: kpulse 1.5s infinite;
}
.scanner-mode-text {
  font-size: 0.65rem;
  font-weight: 700;
  color: var(--muted-foreground);
  letter-spacing: 0.07em;
}
.scanner-topbar-right { display: flex; align-items: center; gap: 0.375rem; }
.scanner-online {
  font-size: 0.62rem;
  font-weight: 600;
  color: var(--muted-foreground);
  font-family: monospace;
}

/* Scanner Viewport */
.scanner-viewport {
  flex: 1;
  min-height: 240px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  border-radius: var(--radius-md, 10px);
  background: var(--muted);
  border: 1px solid var(--border);
  overflow: hidden;
}
.scanner-viewport--cam { background: #000; }

/* Scan Animation: Fingerprint */
.scan-anim { display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }

.scan-ring-fp {
  position: relative;
  width: 130px; height: 130px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: color-mix(in oklch, var(--primary) 8%, transparent);
  border: 1px solid color-mix(in oklch, var(--primary) 25%, transparent);
}
.scan-ping-fp {
  position: absolute; inset: 0; border-radius: 50%;
  border: 1px solid color-mix(in oklch, var(--primary) 30%, transparent);
  animation: kping 1.2s cubic-bezier(0,0,0.2,1) infinite;
}
.scan-inner-fp {
  position: absolute; inset: 12px; border-radius: 50%;
  border: 1px solid color-mix(in oklch, var(--primary) 20%, transparent);
  animation: kpulse 1.5s ease-in-out infinite;
}
.scan-icon-fp { color: var(--primary); position: relative; z-index: 10; animation: kpulse 1.5s ease-in-out infinite; }
.scan-laser-fp {
  position: absolute; left: 18px; right: 18px;
  height: 2px; border-radius: 999px;
  background: var(--primary);
  box-shadow: 0 0 8px var(--primary);
  animation: kscanY 1.8s ease-in-out infinite;
  z-index: 20;
}
.scan-label-fp { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--primary); animation: kpulse 1.5s ease-in-out infinite; }

/* Scan Animation: RFID */
.scan-ring-rfid {
  position: relative;
  width: 130px; height: 130px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: color-mix(in oklch, var(--primary) 8%, transparent);
  border: 1px solid color-mix(in oklch, var(--primary) 25%, transparent);
}
.scan-ping-rfid {
  position: absolute; inset: 0; border-radius: 50%;
  border: 1px solid color-mix(in oklch, var(--primary) 30%, transparent);
  animation: kping 1.2s cubic-bezier(0,0,0.2,1) infinite;
}
.scan-inner-rfid {
  position: absolute; inset: 12px; border-radius: 50%;
  border: 1px solid color-mix(in oklch, var(--primary) 20%, transparent);
  animation: kpulse 1.5s ease-in-out infinite;
}
.scan-icon-rfid { color: var(--primary); position: relative; z-index: 10; animation: kpulse 1.5s ease-in-out infinite; }
.scan-label-rfid { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--primary); animation: kpulse 1.5s ease-in-out infinite; }

/* Idle State */
.idle-state { display: flex; flex-direction: column; align-items: center; gap: 0.875rem; text-align: center; padding: 1rem; }
.idle-ring {
  width: 130px; height: 130px;
  border-radius: 50%;
  border: 2px dashed color-mix(in oklch, var(--primary) 35%, var(--border));
  display: flex; align-items: center; justify-content: center;
  background: color-mix(in oklch, var(--primary) 5%, transparent);
  transition: all 0.3s;
}
.idle-ring:hover {
  border-color: color-mix(in oklch, var(--primary) 60%, transparent);
  background: color-mix(in oklch, var(--primary) 8%, transparent);
}
.idle-icon { color: color-mix(in oklch, var(--primary) 50%, transparent); }
.idle-title { font-size: 0.95rem; font-weight: 700; color: var(--foreground); }
.idle-sub { font-size: 0.72rem; color: var(--muted-foreground); max-width: 200px; }

/* RFID input row */
.rfid-input-row {
  display: flex; align-items: center; gap: 0.5rem;
  background: color-mix(in oklch, var(--primary) 6%, var(--muted));
  border: 1px solid color-mix(in oklch, var(--primary) 20%, var(--border));
  border-radius: 8px; padding: 0.45rem 0.875rem;
  margin-top: 0.375rem;
}
.rfid-scan-text { font-size: 0.72rem; color: var(--primary); font-weight: 600; animation: kpulse 1.5s ease-in-out infinite; }
.rfid-hint { font-size: 0.62rem; color: var(--muted-foreground); }

/* Success Box */
.success-box {
  display: flex; flex-direction: column; align-items: center;
  gap: 0.4rem; text-align: center; padding: 1.5rem 1.25rem;
  background: color-mix(in oklch, var(--primary) 6%, var(--card));
  border: 1px solid color-mix(in oklch, var(--primary) 20%, var(--border));
  border-radius: var(--radius-lg, 14px);
  max-width: 260px; width: 100%;
  animation: kzoomIn 0.2s ease;
}
.success-icon {
  width: 54px; height: 54px;
  border-radius: 50%;
  background: color-mix(in oklch, var(--primary) 10%, transparent);
  border: 1px solid color-mix(in oklch, var(--primary) 20%, transparent);
  display: flex; align-items: center; justify-content: center;
  animation: kbounce 2s infinite;
}
.success-msg { font-size: 0.65rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.08em; }
.success-name { font-size: 1.05rem; font-weight: 700; color: var(--foreground); }
.success-meta { font-size: 0.72rem; color: var(--muted-foreground); }
.success-time-row {
  display: flex; align-items: center; gap: 0.25rem;
  font-size: 0.7rem; color: var(--muted-foreground);
}
.success-chip {
  font-size: 0.58rem; font-weight: 700;
  color: var(--primary);
  background: color-mix(in oklch, var(--primary) 10%, transparent);
  border: 1px solid color-mix(in oklch, var(--primary) 20%, transparent);
  padding: 0.125rem 0.625rem;
  border-radius: 4px; letter-spacing: 0.07em; font-family: monospace;
}

/* Camera */
.cam-feed { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.cam-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 10; }
.corner { position: absolute; width: 20px; height: 20px; border-color: var(--primary); border-style: solid; }
.corner-tl { top: 20%; left: 22%; border-width: 2px 0 0 2px; border-radius: 4px 0 0 0; }
.corner-tr { top: 20%; right: 22%; border-width: 2px 2px 0 0; border-radius: 0 4px 0 0; }
.corner-bl { bottom: 20%; left: 22%; border-width: 0 0 2px 2px; border-radius: 0 0 0 4px; }
.corner-br { bottom: 20%; right: 22%; border-width: 0 2px 2px 0; border-radius: 0 0 4px 0; }
.cam-scan-line {
  position: absolute; left: 22%; right: 22%; height: 2px;
  background: linear-gradient(90deg, transparent, var(--primary), transparent);
  animation: kscan 2s linear infinite;
  box-shadow: 0 0 8px var(--ring);
  border-radius: 999px;
}
.cam-state-wrap {
  position: absolute; inset: 0; z-index: 20;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 0.75rem; background: rgba(0,0,0,0.65); text-align: center; padding: 1rem;
}
.cam-spinner {
  width: 30px; height: 30px;
  border: 3px solid color-mix(in oklch, var(--primary) 30%, transparent);
  border-top-color: var(--primary);
  border-radius: 50%; animation: kspin 0.8s linear infinite;
}
.cam-state-text { font-size: 0.72rem; color: #94a3b8; font-weight: 600; }
.cam-error-icon {
  padding: 1rem; border-radius: 50%;
  background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.2); color: #f87171;
}
.cam-error-text { font-size: 0.72rem; color: #f87171; max-width: 200px; }
.cam-retry-btn {
  padding: 0.375rem 1rem; border-radius: 8px;
  border: 1px solid rgba(255,255,255,0.15);
  background: rgba(255,255,255,0.08); color: #e2e8f0;
  font-size: 0.72rem; cursor: pointer;
}
.cam-idle-icon {
  padding: 1.25rem; border-radius: 50%;
  background: var(--muted); border: 1px solid var(--border);
}
.cam-idle-text { font-size: 0.72rem; color: var(--muted-foreground); }
.cam-start-btn {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 0.5rem 1.25rem; border-radius: var(--radius-md, 10px);
  background: var(--primary); color: var(--primary-foreground);
  border: none; font-size: 0.78rem; font-weight: 600; cursor: pointer;
  transition: opacity 0.2s;
}
.cam-start-btn:hover { opacity: 0.88; }
.cam-success-overlay {
  position: absolute; inset: 0; z-index: 30;
  background: rgba(0,0,0,0.65); backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  border-radius: var(--radius-md, 10px);
}

/* Scanner Footer */
.scanner-footer {
  display: flex; align-items: center; justify-content: space-between;
  background: var(--muted); border: 1px solid var(--border);
  border-radius: var(--radius-md, 10px); padding: 0.45rem 0.875rem;
}
.scanner-footer-left { display: flex; align-items: center; gap: 0.5rem; }
.footer-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--primary); animation: kpulse 2s infinite; }
.footer-text { font-size: 0.62rem; font-weight: 700; color: var(--muted-foreground); letter-spacing: 0.06em; font-family: monospace; }
.footer-ver { font-size: 0.6rem; color: var(--muted-foreground); font-family: monospace; opacity: 0.6; }

/* Simulation Panel removed */

/* ══════════════════════════════════════════
   KEYFRAMES
══════════════════════════════════════════ */
@keyframes kpulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.45; } }
@keyframes kping { 0% { transform: scale(0.95); opacity: 0.8; } 75%, 100% { transform: scale(1.35); opacity: 0; } }
@keyframes kscan { 0% { top: 15%; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { top: 85%; opacity: 0; } }
@keyframes kscanY { 0% { top: 12px; } 50% { top: calc(100% - 14px); } 100% { top: 12px; } }
@keyframes kspin { to { transform: rotate(360deg); } }
@keyframes kslideIn { from { transform: translateY(-5px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes kzoomIn { from { transform: scale(0.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }
@keyframes kbounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
</style>
