<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
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
  LogIn,
  LogOut,
} from 'lucide-vue-next'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { getStudents, getLogs, postScan } from '@/services/api/absensi'
import { glassSlide, glassFade } from '@/config/motion'

const router = useRouter()

// ─── State ──────────────────────────────────────────────
const scanType = ref('Otomatis') // 'Otomatis' | 'Masuk' | 'Keluar'
const absensiData = ref([])
const scanResults = ref([])
const cooldowns = ref({}) // Format: { [studentId]: timestamp }

// ─── Clock State ─────────────────────────────────────────
const currentTime = ref('')
const currentDate = ref('')
let clockInterval = null

function updateClock() {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
  currentDate.value = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}

// ─── Scan Success State ─────────────────────────────────
const activeScannedSiswa = ref(null)
const scanSuccessMsg = ref('')
let scanTimeout = null

// ─── Camera State ───────────────────────────────────────
const videoRef = ref(null)
const mediaStream = ref(null)
const cameraStatus = ref('idle') // idle | loading | active | error
const cameraError = ref('')

// ─── LocalStorage Persistence & API Polling ──────────────
let logPollInterval = null

async function loadData() {
  try {
    const [students, logs] = await Promise.all([
      getStudents(),
      getLogs()
    ])
    absensiData.value = students
    scanResults.value = logs
  } catch (error) {
    console.error('Failed to load data:', error)
  }
}

async function fetchLogsOnly() {
  try {
    scanResults.value = await getLogs()
  } catch (error) {
    // Silently fail for background polling
  }
}

// ─── Initials Helper ────────────────────────────────────
function getInitials(nama) {
  if (!nama) return ''
  const parts = nama.split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return parts[0].substring(0, 2).toUpperCase()
}

// ─── Camera Handlers ─────────────────────────────────────
async function startCamera() {
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

// ─── Face Scan Simulation ───────────────────────────────
let faceScanInterval = null

function startFaceScanSimulation() {
  if (faceScanInterval) clearInterval(faceScanInterval)
  faceScanInterval = setInterval(() => {
    if (cameraStatus.value !== 'active') return
    
    // Cari siswa yang belum absen atau belum checkout
    let pool = []
    if (scanType.value === 'Otomatis') {
      pool = absensiData.value.filter(s => s.status === 'belum_absen' || (s.status !== 'belum_absen' && !s.jamKeluar))
    } else if (scanType.value === 'Masuk') {
      pool = absensiData.value.filter(s => s.status === 'belum_absen')
    } else if (scanType.value === 'Keluar') {
      pool = absensiData.value.filter(s => s.status !== 'belum_absen' && !s.jamKeluar)
    }
    
    if (pool.length === 0) return
    
    // Ambil acak siswa dari pool yang tidak dalam cooldown aktif
    const availablePool = pool.filter(s => {
      const lastScan = cooldowns.value[s.id] || 0
      return Date.now() - lastScan > 5000
    })
    
    if (availablePool.length === 0) return
    const randomSiswa = availablePool[Math.floor(Math.random() * availablePool.length)]
    
    executeSuccessfulScan(randomSiswa.id, 'kamera')
  }, 10000) // Simulasikan scan wajah otomatis setiap 10 detik
}

function stopFaceScanSimulation() {
  if (faceScanInterval) {
    clearInterval(faceScanInterval)
    faceScanInterval = null
  }
}

// ─── Scan Logic ──────────────────────────────────────────
function triggerSimulatedScan(siswaId, source) {
  executeSuccessfulScan(siswaId, source)
}

function executeSuccessfulScan(siswaId, source) {
  const student = absensiData.value.find(s => s.id === siswaId)
  if (!student) return

  // 1. Cooldown Check (5 detik untuk mencegah double-scan)
  const now = Date.now()
  if (cooldowns.value[siswaId] && now - cooldowns.value[siswaId] < 5000) {
    console.log(`Scan diabaikan untuk ${student.nama} karena batas cooldown 5s.`)
    return
  }
  cooldowns.value[siswaId] = now

  let actualScanType = scanType.value
  if (actualScanType === 'Otomatis') {
    actualScanType = student.status === 'belum_absen' ? 'Masuk' : 'Keluar'
  }

  // Panggil mock API absensi
  postScan(siswaId, actualScanType)
    .then(newLog => {
      // 2. Interrupt Logic: Hapus timeout lama jika ada scan masuk baru
      if (scanTimeout) {
        clearTimeout(scanTimeout)
        scanTimeout = null
      }

      // 3. Tentukan Source Label untuk UI Kiosk
      const sourceLabels = {
        kamera: 'VERIFIKASI: SCAN WAJAH (AI)',
        rfid: 'VERIFIKASI: KARTU RFID',
        fingerprint: 'VERIFIKASI: SIDIK JARI'
      }

      activeScannedSiswa.value = {
        nama: newLog.nama,
        kelas: newLog.kelas,
        nisn: newLog.nisn,
        jamMasuk: actualScanType === 'Masuk' ? newLog.waktu : (student.jamMasuk || '-'),
        jamKeluar: actualScanType === 'Keluar' ? newLog.waktu : null,
        verificationSource: sourceLabels[source] || 'VERIFIKASI SISTEM'
      }

      scanSuccessMsg.value = `ABSENSI ${actualScanType} BERHASIL!`
      
      // Tambahkan ke log list di samping
      scanResults.value.unshift(newLog)
      
      // Sinkronisasi data siswa
      getStudents().then(data => absensiData.value = data)
      
      // 4. Timer 3.5 detik untuk menutup popup overlay
      scanTimeout = setTimeout(() => {
        if (activeScannedSiswa.value?.nama === newLog.nama) {
          activeScannedSiswa.value = null
        }
      }, 3500)
    })
    .catch(err => {
      console.error(err)
    })
}

// ─── Lifecycle ──────────────────────────────────────────
onMounted(() => {
  loadData()
  updateClock()
  clockInterval = setInterval(updateClock, 1000)

  // Aktifkan Kamera Otomatis di Kiosk
  startCamera()

  // Theme Restoration
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
  document.body.classList.forEach(cls => {
    if (cls.startsWith('theme-')) document.body.classList.remove(cls)
  })
  if (savedThemeStyle !== 'tahoe') {
    document.body.classList.add(`theme-${savedThemeStyle}`)
  }

  logPollInterval = setInterval(fetchLogsOnly, 5000)
})

onUnmounted(() => {
  stopCamera()
  if (clockInterval) clearInterval(clockInterval)
  if (logPollInterval) clearInterval(logPollInterval)
  if (scanTimeout) clearTimeout(scanTimeout)
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
        <h1 class="header-title">Absensi & Presensi</h1>
        <p class="header-sub">Sistem Presensi</p>
      </div>
      <div class="header-status">
        <span class="status-pulse" />
        <span class="status-label">Sistem Aktif</span>
        <ShieldCheck class="size-4" style="color: var(--primary)" />
      </div>
    </header>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="kiosk-main">
      <!-- LEFT: Live Viewport (Kamera + Success Card Overlay) -->
      <section class="viewport-section" v-motion :initial="glassSlide.initial" :visible-once="glassSlide.visible">
        <Card class="scanner-card">
          <!-- Viewport Header -->
          <div class="scanner-topbar">
            <div class="scanner-topbar-left">
              <span class="scanner-dot" />
              <span class="scanner-mode-text">UNIFIED SCANNER TERMINAL</span>
            </div>
            <div class="scanner-topbar-right">
              <Wifi class="size-3.5" style="color: var(--primary)" />
              <span class="scanner-online">ONLINE</span>
            </div>
          </div>

          <!-- The Viewport Container -->
          <div class="scanner-viewport scanner-viewport--cam">
            <!-- Camera Feed -->
            <video v-if="cameraStatus === 'active'" ref="videoRef" autoplay playsinline class="cam-feed" />
            
            <!-- Scanning Sci-Fi Reticle Overlay (only when no success card is active) -->
            <div v-if="cameraStatus === 'active' && !activeScannedSiswa" class="cam-overlay">
              <span class="corner corner-tl" />
              <span class="corner corner-tr" />
              <span class="corner corner-bl" />
              <span class="corner corner-br" />
              <div class="cam-scan-line" />
              <div class="face-target-circle" />
            </div>

            <!-- Camera Loading State -->
            <div v-if="cameraStatus === 'loading'" class="cam-state-wrap">
              <div class="cam-spinner" />
              <span class="cam-state-text">Menghubungkan Kamera...</span>
            </div>

            <!-- Camera Error State -->
            <div v-if="cameraStatus === 'error'" class="cam-state-wrap">
              <div class="cam-error-icon"><CameraOff class="size-7" /></div>
              <p class="cam-error-text">{{ cameraError }}</p>
              <button class="cam-retry-btn" @click="startCamera">Coba Ulang</button>
            </div>

            <!-- Camera Idle (Off) State -->
            <div v-if="cameraStatus === 'idle'" class="cam-state-wrap">
              <div class="cam-idle-icon"><Camera class="size-10" style="color: var(--muted-foreground)" /></div>
              <p class="cam-idle-text">Kamera Kiosk Siap Dinyalakan</p>
              <button class="cam-start-btn" @click="startCamera">
                <Camera class="size-4" />
                Aktifkan Kamera
              </button>
            </div>

            <!-- Unified Success Overlay (displays on top of camera/idle state) -->
            <div v-if="activeScannedSiswa" class="cam-success-overlay">
              <div class="kiosk-success-card">
                <div class="success-icon-badge">
                  <CheckCircle class="size-8" style="color: var(--primary)" />
                </div>
                <p class="success-status-label">{{ scanSuccessMsg }}</p>
                
                <!-- Student Card details -->
                <div class="student-info-row">
                  <div class="student-avatar-large">
                    {{ getInitials(activeScannedSiswa.nama) }}
                  </div>
                  <div class="student-details-text">
                    <p class="student-name-large">{{ activeScannedSiswa.nama }}</p>
                    <p class="student-meta-large">{{ activeScannedSiswa.kelas }} · NISN: {{ activeScannedSiswa.nisn }}</p>
                  </div>
                </div>

                <div class="verification-source-chip">
                  <span>{{ activeScannedSiswa.verificationSource }}</span>
                </div>

                <div class="success-time-row">
                  <Clock class="size-3.5" />
                  <span>{{ activeScannedSiswa.jamKeluar ? 'Keluar' : 'Masuk' }}: {{ activeScannedSiswa.jamKeluar || activeScannedSiswa.jamMasuk }}</span>
                </div>

                <!-- Linear Countdown progress bar -->
                <div class="countdown-bar-container">
                  <div class="countdown-bar"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Viewport Footer (Hardware Indicators) -->
          <div class="scanner-footer">
            <div class="hardware-indicators-row">
              <span class="indicator-pill">
                <span class="indicator-dot active" />
                <span>RFID SENSOR</span>
              </span>
              <span class="indicator-pill">
                <span class="indicator-dot active" />
                <span>FINGERPRINT SENSOR</span>
              </span>
              <span class="indicator-pill">
                <span class="indicator-dot" :class="cameraStatus === 'active' ? 'active' : ''" />
                <span>FACE RECOGNITION</span>
              </span>
            </div>
            <span class="footer-ver">KIOSK_v3.0</span>
          </div>
        </Card>
      </section>

      <!-- RIGHT: Clock, Controls, Simulation list, and Scan Logs -->
      <section class="controls-section" v-motion :initial="glassSlide.initial" :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }">
        <!-- Clock Card -->
        <Card class="clock-card">
          <div class="clock-time">{{ currentTime }}</div>
          <div class="clock-date">
            <CalendarDays class="size-4" style="color: var(--muted-foreground)" />
            <span>{{ currentDate }}</span>
          </div>
          <div class="divider" />
          
          <!-- Scan Type Switcher -->
          <div class="type-tabs">
            <button
              @click="scanType = 'Otomatis'"
              :class="['type-tab', scanType === 'Otomatis' ? 'type-tab--active type-tab--otomatis' : '']"
            >
              <ShieldCheck class="size-4" />
              <span>Otomatis</span>
            </button>
            <button
              @click="scanType = 'Masuk'"
              :class="['type-tab', scanType === 'Masuk' ? 'type-tab--active type-tab--masuk' : '']"
            >
              <LogIn class="size-4" />
              <span>Masuk</span>
            </button>
            <button
              @click="scanType = 'Keluar'"
              :class="['type-tab', scanType === 'Keluar' ? 'type-tab--active type-tab--keluar' : '']"
            >
              <LogOut class="size-4" />
              <span>Pulang</span>
            </button>
          </div>
        </Card>

        <!-- Student Simulation Panel (For testing/demo) -->
        <Card class="simulation-card">
          <div class="log-header">
            <Fingerprint class="size-4" style="color: var(--primary)" />
            <span class="log-title">Simulasi Tap Siswa</span>
          </div>
          <div class="sim-list">
            <div v-for="siswa in filteredStudents.slice(0, 4)" :key="siswa.id" class="sim-item">
              <div class="sim-avatar">{{ getInitials(siswa.nama) }}</div>
              <div class="sim-info">
                <p class="sim-name">{{ siswa.nama }}</p>
                <p class="sim-meta">{{ siswa.kelas }}</p>
              </div>
              <div class="sim-actions">
                <button @click="triggerSimulatedScan(siswa.id, 'kamera')" class="sim-btn" title="Simulasikan Wajah">
                  <Camera class="size-3.5" />
                </button>
                <button @click="triggerSimulatedScan(siswa.id, 'rfid')" class="sim-btn" title="Simulasikan RFID">
                  <Radio class="size-3.5" />
                </button>
                <button @click="triggerSimulatedScan(siswa.id, 'fingerprint')" class="sim-btn" title="Simulasikan Sidik Jari">
                  <Fingerprint class="size-3.5" />
                </button>
              </div>
            </div>
          </div>
        </Card>

        <!-- Scan Log List -->
        <Card class="log-card">
          <div class="log-header">
            <UserCheck class="size-4" style="color: var(--primary)" />
            <span class="log-title">Riwayat Scan</span>
            <Badge variant="secondary" class="log-badge">{{ scanResults.length }}</Badge>
          </div>
          <div class="log-list">
            <div v-if="scanResults.length === 0" class="log-empty">
              <Clock class="size-5" style="color: var(--muted-foreground)" />
              <span>Menunggu pemindaian pertama...</span>
            </div>
            <div
              v-for="log in scanResults.slice(0, 5)"
              :key="'log-' + log.id"
              class="log-item"
            >
              <div class="log-avatar">{{ log.inisial }}</div>
              <div class="log-info">
                <p class="log-name">{{ log.nama }}</p>
                <p class="log-meta">{{ log.kelas }} · {{ log.tipe }} · {{ log.waktu }}</p>
              </div>
              <Badge variant="outline" :class="['log-badge-type', log.tipe === 'Masuk' ? 'badge-masuk' : 'badge-keluar']">
                {{ log.tipe }}
              </Badge>
            </div>
          </div>
        </Card>
      </section>
    </main>
  </div>
</template>

<style scoped>
/* ══════════════════════════════════════════
   ROOT & GENERAL STYLE
   ══════════════════════════════════════════ */
.kiosk-root {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: var(--background);
  color: var(--foreground);
  font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
  overflow: hidden;
}

/* Header */
.kiosk-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.75rem;
  height: 64px;
  background: var(--card);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  backdrop-filter: blur(12px);
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
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--foreground);
  letter-spacing: -0.02em;
  margin: 0;
}
.header-sub {
  font-size: 0.75rem;
  color: var(--muted-foreground);
  margin-top: 1px;
}
.header-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
}
.status-pulse {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--primary);
  animation: kpulse 2s infinite;
}

/* ══════════════════════════════════════════
   MAIN LAYOUT GRID
   ══════════════════════════════════════════ */
.kiosk-main {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 1.5rem;
  padding: 1.5rem;
  flex: 1;
  min-height: 0;
}

.viewport-section {
  display: flex;
  flex-direction: column;
}

.controls-section {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

/* ══════════════════════════════════════════
   LEFT VIEWPORT (SCANNER CARD)
   ══════════════════════════════════════════ */
.scanner-card {
  flex: 1;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg, 14px);
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  overflow: hidden;
  box-shadow: var(--glass-shadow, 0 2px 12px rgba(0,0,0,0.08));
}

.scanner-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--muted);
  border: 1px solid var(--border);
  border-radius: var(--radius-md, 10px);
  padding: 0.6rem 1rem;
}
.scanner-topbar-left { display: flex; align-items: center; gap: 0.5rem; }
.scanner-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--primary);
  animation: kpulse 1.5s infinite;
}
.scanner-mode-text {
  font-size: 0.7rem;
  font-weight: 700;
  color: var(--muted-foreground);
  letter-spacing: 0.08em;
}
.scanner-online {
  font-size: 0.65rem;
  font-weight: 600;
  color: var(--primary);
  font-family: monospace;
}

/* Viewport cam */
.scanner-viewport {
  flex: 1;
  min-height: 380px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  border-radius: var(--radius-md, 12px);
  background: #0b0f19;
  border: 1px solid var(--border);
  overflow: hidden;
}

.cam-feed {
  position: absolute; inset: 0;
  width: 100%; height: 100%;
  object-fit: cover;
  transform: scaleX(-1); /* mirror effect */
}

/* Sci-fi Overlay */
.cam-overlay {
  position: absolute; inset: 0;
  display: flex; align-items: center; justify-content: center;
  z-index: 10;
  pointer-events: none;
}
.corner { position: absolute; width: 24px; height: 24px; border-color: var(--primary); border-style: solid; }
.corner-tl { top: 15%; left: 20%; border-width: 3px 0 0 3px; border-radius: 6px 0 0 0; }
.corner-tr { top: 15%; right: 20%; border-width: 3px 3px 0 0; border-radius: 0 6px 0 0; }
.corner-bl { bottom: 15%; left: 20%; border-width: 0 0 3px 3px; border-radius: 0 0 0 6px; }
.corner-br { bottom: 15%; right: 20%; border-width: 0 3px 3px 0; border-radius: 0 0 6px 0; }

.cam-scan-line {
  position: absolute; left: 20%; right: 20%; height: 3px;
  background: linear-gradient(90deg, transparent, var(--primary), transparent);
  animation: kscan 2.5s linear infinite;
  box-shadow: 0 0 10px var(--primary);
  border-radius: 999px;
}

.face-target-circle {
  width: 180px; height: 180px;
  border: 1px dashed rgba(255, 255, 255, 0.25);
  border-radius: 50%;
  position: absolute;
  box-shadow: 0 0 0 999px rgba(0, 0, 0, 0.4);
  animation: kpulse 3s infinite;
}

/* Success Overlay */
.cam-success-overlay {
  position: absolute; inset: 0; z-index: 30;
  background: rgba(11, 15, 25, 0.75);
  backdrop-filter: blur(8px);
  display: flex; align-items: center; justify-content: center;
  padding: 1.5rem;
}

.kiosk-success-card {
  background: var(--card);
  border: 2px solid var(--primary);
  border-radius: var(--radius-lg, 16px);
  padding: 2rem;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 15px rgba(59, 130, 246, 0.15);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  animation: kzoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.success-icon-badge {
  width: 64px; height: 64px;
  border-radius: 50%;
  background: color-mix(in oklch, var(--primary) 12%, transparent);
  border: 1px solid color-mix(in oklch, var(--primary) 30%, transparent);
  display: flex; align-items: center; justify-content: center;
  animation: kbounce 2s infinite;
}

.success-status-label {
  font-size: 0.85rem;
  font-weight: 800;
  color: var(--primary);
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.student-info-row {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  width: 100%;
  padding: 1rem;
  background: var(--muted);
  border-radius: 12px;
  border: 1px solid var(--border);
}

.student-avatar-large {
  width: 50px; height: 50px;
  border-radius: 50%;
  background: var(--primary);
  color: var(--primary-foreground);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.15rem; font-weight: 800;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.student-details-text {
  flex: 1;
  text-align: left;
}

.student-name-large {
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--foreground);
  margin: 0;
  line-height: 1.2;
}

.student-meta-large {
  font-size: 0.8rem;
  color: var(--muted-foreground);
  margin-top: 2px;
}

.verification-source-chip {
  font-size: 0.65rem;
  font-weight: 800;
  color: #10b981;
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.2);
  padding: 0.25rem 0.85rem;
  border-radius: 999px;
  letter-spacing: 0.08em;
}

.success-time-row {
  display: flex; align-items: center; gap: 0.375rem;
  font-size: 0.8rem; color: var(--muted-foreground);
}

/* Countdown bar */
.countdown-bar-container {
  width: 100%;
  height: 4px;
  background: var(--border);
  border-radius: 2px;
  overflow: hidden;
  margin-top: 0.5rem;
}
.countdown-bar {
  height: 100%;
  background: var(--primary);
  animation: shrinkWidth 3.5s linear forwards;
}

@keyframes shrinkWidth {
  from { width: 100%; }
  to { width: 0%; }
}

/* Hardware indicators */
.scanner-footer {
  display: flex; align-items: center; justify-content: space-between;
  background: var(--muted); border: 1px solid var(--border);
  border-radius: var(--radius-md, 10px); padding: 0.6rem 1rem;
}

.hardware-indicators-row {
  display: flex;
  gap: 1rem;
}

.indicator-pill {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.62rem;
  font-weight: 700;
  color: var(--muted-foreground);
  letter-spacing: 0.04em;
}

.indicator-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: #64748b;
}

.indicator-dot.active {
  background: #10b981;
  box-shadow: 0 0 6px #10b981;
  animation: kpulse 1.8s infinite;
}

.footer-ver { font-size: 0.65rem; color: var(--muted-foreground); font-family: monospace; opacity: 0.6; }

/* ══════════════════════════════════════════
   RIGHT PANEL CONTROLS
   ══════════════════════════════════════════ */
.clock-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg, 14px);
  padding: 1.25rem 1.5rem;
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
  font-size: 0.8rem;
  color: var(--muted-foreground);
  margin-top: 0.4rem;
}

.divider {
  height: 1px;
  background: var(--border);
  margin: 1rem 0;
}

.type-tabs {
  display: flex;
  gap: 0.375rem;
  padding: 0.375rem;
  background: var(--muted);
  border-radius: 12px;
  border: 1px solid var(--border);
}
.type-tab {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.375rem;
  padding: 0.6rem 0.25rem;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: var(--muted-foreground);
  font-size: 0.75rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.type-tab:hover:not(.type-tab--active) {
  color: var(--foreground);
  background: var(--accent);
}
.type-tab--active {
  background: var(--background);
}
.type-tab--otomatis {
  color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
  border: 1px solid rgba(59, 130, 246, 0.2);
}
.type-tab--masuk {
  color: #10b981;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
  border: 1px solid rgba(16, 185, 129, 0.2);
}
.type-tab--keluar {
  color: #f59e0b;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
  border: 1px solid rgba(245, 158, 11, 0.2);
}

/* Simulation panel */
.simulation-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg, 14px);
  padding: 1.125rem;
  display: flex;
  flex-direction: column;
  box-shadow: var(--glass-shadow, 0 2px 8px rgba(0,0,0,0.06));
}

.sim-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.sim-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border-radius: 8px;
  background: var(--muted);
  border: 1px solid var(--border);
}

.sim-avatar {
  width: 34px; height: 34px;
  border-radius: 50%;
  background: var(--accent);
  border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  font-size: 0.75rem; font-weight: 700; color: var(--primary);
}

.sim-info { flex: 1; text-align: left; min-width: 0; }
.sim-name { font-size: 0.78rem; font-weight: 700; color: var(--foreground); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0; }
.sim-meta { font-size: 0.65rem; color: var(--muted-foreground); margin: 0; }

.sim-actions {
  display: flex;
  gap: 0.25rem;
}

.sim-btn {
  padding: 0.4rem;
  border-radius: 6px;
  border: 1px solid var(--border);
  background: var(--background);
  color: var(--muted-foreground);
  cursor: pointer;
  transition: all 0.2s;
}
.sim-btn:hover {
  background: var(--primary);
  color: var(--primary-foreground);
  border-color: var(--primary);
}

/* Log card */
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
  text-align: left;
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
  border-radius: 8px;
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
  font-size: 0.65rem; font-weight: 700; color: var(--primary);
  flex-shrink: 0;
}
.log-info { flex: 1; min-width: 0; text-align: left; }
.log-name { font-size: 0.75rem; font-weight: 700; color: var(--foreground); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0; }
.log-meta { font-size: 0.65rem; color: var(--muted-foreground); margin: 0; }
.log-badge-type {
  font-size: 0.6rem; font-weight: 700;
  padding: 0.125rem 0.4rem;
  border-radius: 4px; flex-shrink: 0;
}
.badge-masuk {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
  border: 1px solid rgba(59, 130, 246, 0.2);
}
.badge-keluar {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
  border: 1px solid rgba(245, 158, 11, 0.2);
}

/* ══════════════════════════════════════════
   KEYFRAMES
   ══════════════════════════════════════════ */
@keyframes kpulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.45; } }
@keyframes kscan { 0% { top: 15%; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { top: 85%; opacity: 0; } }
@keyframes kspin { to { transform: rotate(360deg); } }
@keyframes kslideIn { from { transform: translateY(-5px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes kzoomIn { from { transform: scale(0.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }
@keyframes kbounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }

.cam-state-wrap {
  position: absolute; inset: 0; z-index: 20;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 0.75rem; background: rgba(0,0,0,0.75); text-align: center; padding: 1rem;
}
.cam-spinner {
  width: 32px; height: 32px;
  border: 3px solid rgba(59, 130, 246, 0.2);
  border-top-color: var(--primary);
  border-radius: 50%; animation: kspin 0.8s linear infinite;
}
.cam-state-text { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }
.cam-error-icon {
  padding: 1rem; border-radius: 50%;
  background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.2); color: #f87171;
}
.cam-error-text { font-size: 0.75rem; color: #f87171; max-width: 220px; }
.cam-retry-btn {
  padding: 0.45rem 1.25rem; border-radius: 8px;
  border: 1px solid rgba(255,255,255,0.15);
  background: rgba(255,255,255,0.08); color: #e2e8f0;
  font-size: 0.75rem; cursor: pointer; transition: all 0.2s;
}
.cam-retry-btn:hover { background: rgba(255,255,255,0.15); }
.cam-idle-icon {
  padding: 1.25rem; border-radius: 50%;
  background: var(--muted); border: 1px solid var(--border);
}
.cam-idle-text { font-size: 0.75rem; color: var(--muted-foreground); }
.cam-start-btn {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 0.55rem 1.5rem; border-radius: var(--radius-md, 10px);
  background: var(--primary); color: var(--primary-foreground);
  border: none; font-size: 0.8rem; font-weight: 600; cursor: pointer;
  transition: opacity 0.2s;
}
.cam-start-btn:hover { opacity: 0.9; }
</style>
