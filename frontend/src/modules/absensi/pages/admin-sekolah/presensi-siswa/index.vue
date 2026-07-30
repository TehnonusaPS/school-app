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
  AlertCircle,
  UserCheck,
  Clock,
  CalendarDays,
  Wifi,
  ShieldCheck,
  LogIn,
  LogOut,
  Maximize2,
  Minimize2,
  Lock,
} from 'lucide-vue-next'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { getStudents, getLogs, postScan } from '@/services/api/absensi'
import { glassSlide, glassFade } from '@/config/motion'
import { useAuthStore } from '@/stores/authStore'
import { toast } from 'vue-sonner'

const router = useRouter()
const auth = useAuthStore()

// ─── State ──────────────────────────────────────────────
const scanType = ref('Masuk') // 'Masuk' only (user request)
const absensiData = ref([])
const scanResults = ref([])
const cooldowns = ref({}) // Format: { [studentId]: timestamp }

// ─── Face Tracking State ───────────────────────────────
const videoWidth = ref(640)
const videoHeight = ref(480)
const isTrackerLoaded = ref(false)
const lastDetectedFace = ref(null)
const isCooldownActive = ref(false)
const activeScanError = ref(null)

// ─── Fullscreen State ────────────────────────────────────
const isFullscreen = ref(false)
const isPasswordGuardOpen = ref(false)
const passwordInput = ref('')
const passwordError = ref('')
const passwordShake = ref(false)

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
const overlayCanvasRef = ref(null)
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

// ─── Face Tracking Helper & Functions ───────────────────
const loadScript = (src) => {
  return new Promise((resolve, reject) => {
    if (document.querySelector(`script[src="${src}"]`)) {
      resolve()
      return
    }
    const script = document.createElement('script')
    script.src = src
    script.onload = () => resolve()
    script.onerror = () => reject(new Error(`Failed to load script: ${src}`))
    document.head.appendChild(script)
  })
}

let detectionInterval = null

function initFaceTracking() {
  if (!videoRef.value || !overlayCanvasRef.value || !window.faceapi) return
  
  stopFaceTracking()
  
  const videoEl = videoRef.value
  const canvasEl = overlayCanvasRef.value
  const faceapi = window.faceapi

  const displaySize = {
    width: videoEl.clientWidth || videoEl.videoWidth || 640,
    height: videoEl.clientHeight || videoEl.videoHeight || 480
  }
  
  faceapi.matchDimensions(canvasEl, displaySize)

  detectionInterval = setInterval(async () => {
    if (cameraStatus.value !== 'active') return
    
    // Auto-adjust size on browser/kiosk resize
    const currentSize = {
      width: videoEl.clientWidth || videoEl.videoWidth || 640,
      height: videoEl.clientHeight || videoEl.videoHeight || 480
    }
    if (canvasEl.width !== currentSize.width || canvasEl.height !== currentSize.height) {
      faceapi.matchDimensions(canvasEl, currentSize)
    }

    try {
      const detections = await faceapi.detectAllFaces(
        videoEl,
        new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
      )
      
      if (detections && detections.length > 0) {
        lastDetectedFace.value = detections[0]
      } else {
        lastDetectedFace.value = null
      }
      
      const resizedDetections = faceapi.resizeResults(detections, currentSize)
      const ctx = canvasEl.getContext('2d')
      ctx.clearRect(0, 0, canvasEl.width, canvasEl.height)

      resizedDetections.forEach((detection) => {
        const { x, y, width, height } = detection.box
        
        let labelText = 'Mendeteksi...'
        let boxColor = '#34d399' // Emerald green

        if (isProcessingScan.value) {
          labelText = 'Memproses...'
          boxColor = '#f59e0b' // Warning yellow
        } else if (activeScannedSiswa.value) {
          labelText = activeScannedSiswa.value.nama
          boxColor = '#10b981' // Success green
        }

        // Draw the glowing bounding box
        ctx.strokeStyle = boxColor
        ctx.lineWidth = 4
        
        // Glow effect
        ctx.shadowColor = boxColor
        ctx.shadowBlur = 10
        ctx.strokeRect(x, y, width, height)
        ctx.shadowBlur = 0 // reset shadow

        // Draw label text (un-mirrored so it's readable)
        ctx.save()
        ctx.translate(x + width / 2, y - 10)
        ctx.scale(-1, 1)

        ctx.font = 'bold 12px sans-serif'
        ctx.textAlign = 'center'
        
        const textWidth = ctx.measureText(labelText).width
        const paddingX = 8
        const paddingY = 4

        // Draw label background
        ctx.fillStyle = 'rgba(15, 23, 42, 0.85)'
        ctx.beginPath()
        const rectX = -textWidth / 2 - paddingX
        const rectY = -12 - paddingY
        const rectW = textWidth + paddingX * 2
        const rectH = 16 + paddingY
        const radius = 4
        
        if (ctx.roundRect) {
          ctx.roundRect(rectX, rectY, rectW, rectH, radius)
        } else {
          ctx.rect(rectX, rectY, rectW, rectH)
        }
        ctx.fill()

        // Border around text label
        ctx.strokeStyle = 'rgba(52, 211, 153, 0.2)'
        ctx.lineWidth = 1
        ctx.stroke()

        // Write text
        ctx.fillStyle = boxColor
        ctx.fillText(labelText, 0, 0)

        ctx.restore()
      })
    } catch (err) {
      console.error('Error running faceapi detection:', err)
    }
  }, 150)
}

function stopFaceTracking() {
  if (detectionInterval) {
    clearInterval(detectionInterval)
    detectionInterval = null
  }
  lastDetectedFace.value = null
  if (overlayCanvasRef.value) {
    const ctx = overlayCanvasRef.value.getContext('2d')
    ctx.clearRect(0, 0, overlayCanvasRef.value.width, overlayCanvasRef.value.height)
  }
}

const onVideoPlay = () => {
  if (videoRef.value) {
    videoWidth.value = videoRef.value.videoWidth || 640
    videoHeight.value = videoRef.value.videoHeight || 480
    initFaceTracking()
  }
}

// ─── Camera Handlers ─────────────────────────────────────
const isProcessingScan = ref(false)
let realFaceScanInterval = null

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

    startRealFaceScanner()

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
  stopRealFaceScanner()
  stopFaceTracking()
  if (mediaStream.value) {
    mediaStream.value.getTracks().forEach(track => track.stop())
    mediaStream.value = null
  }
  if (videoRef.value) videoRef.value.srcObject = null
  cameraStatus.value = 'idle'
}

// ─── Real Face Scan Capture Loop ────────────────────────
function startRealFaceScanner() {
  if (realFaceScanInterval) clearInterval(realFaceScanInterval)
  realFaceScanInterval = setInterval(async () => {
    if (cameraStatus.value !== 'active' || isProcessingScan.value || activeScannedSiswa.value || isCooldownActive.value || !lastDetectedFace.value) return
    
    await performRealFaceScan()
  }, 1000)
}

function stopRealFaceScanner() {
  if (realFaceScanInterval) {
    clearInterval(realFaceScanInterval)
    realFaceScanInterval = null
  }
}

async function performRealFaceScan() {
  if (!videoRef.value || !lastDetectedFace.value) return
  isProcessingScan.value = true

  try {
    const video = videoRef.value
    const face = lastDetectedFace.value.box

    // Add 35% padding to match the reference student photo format
    const paddingX = face.width * 0.35
    const paddingY = face.height * 0.35

    let cropX = face.x - paddingX
    let cropY = face.y - paddingY
    let cropW = face.width + paddingX * 2
    let cropH = face.height + paddingY * 2

    // Bound coordinates to original video resolution
    cropX = Math.max(0, cropX)
    cropY = Math.max(0, cropY)
    cropW = Math.min(video.videoWidth - cropX, cropW)
    cropH = Math.min(video.videoHeight - cropY, cropH)

    const canvas = document.createElement('canvas')
    canvas.width = 300
    canvas.height = 300
    const context = canvas.getContext('2d')

    // Perform exact face-only crop
    context.drawImage(video, cropX, cropY, cropW, cropH, 0, 0, 300, 300)

    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.85))
    const formData = new FormData()
    formData.append('image', blob, 'kiosk_scan.jpg')

    const newLog = await postScan(formData)
    handleScanSuccess(newLog, 'kamera')

  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal memproses absensi.'
    handleScanError(msg, 'kamera')
    if (err.response?.status !== 400) {
      console.warn("Scan error:", err)
    }
  } finally {
    isProcessingScan.value = false
  }
}

// ─── Scan Logic ──────────────────────────────────────────
async function triggerSimulatedScan(siswaId, source) {
  const studentObj = absensiData.value.find(s => s.id === siswaId)
  try {
    const newLog = await postScan({
      student_id: siswaId,
      verification_method: source
    })
    handleScanSuccess(newLog, source)
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal memproses absensi simulasi.'
    handleScanError(msg, source, studentObj)
  }
}

async function triggerQuickScan(source) {
  let studentObj = null
  let studentId = null

  if (absensiData.value && absensiData.value.length > 0) {
    studentObj = absensiData.value[0]
    studentId = studentObj.id
  } else if (scanResults.value && scanResults.value.length > 0) {
    const firstLog = scanResults.value[0]
    studentObj = {
      nama: firstLog.nama,
      kelas: firstLog.kelas,
      nisn: firstLog.nisn || '1234567890',
      id: firstLog.student_profile_id || firstLog.id
    }
    studentId = studentObj.id
  }

  if (studentId) {
    try {
      const newLog = await postScan({
        student_id: studentId,
        verification_method: source
      })
      handleScanSuccess(newLog, source)
    } catch (err) {
      const msg = err.response?.data?.message || 'Gagal memproses absensi.'
      handleScanError(msg, source, studentObj)
    }
  } else {
    const mockLog = {
      id: Date.now(),
      nama: 'Ilham Saputra',
      kelas: '2-D',
      nisn: '1234567890',
      tipe: 'Masuk',
      waktu: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
      inisial: 'IS'
    }
    handleScanSuccess(mockLog, source)
  }
}

function getInitials(nama) {
  if (!nama) return ''
  const words = nama.trim().split(/\s+/)
  if (words.length === 1) return words[0].substring(0, 2).toUpperCase()
  return (words[0][0] + words[1][0]).toUpperCase()
}

function handleScanError(msg, source, studentObj = null) {
  if (scanTimeout) {
    clearTimeout(scanTimeout)
    scanTimeout = null
  }

  const sourceLabels = {
    kamera: 'SCAN WAJAH (AI)',
    rfid: 'KARTU RFID',
    fingerprint: 'SIDIK JARI'
  }
  const sourceLabel = sourceLabels[source] || 'SISTEM'

  // Check if the student has already checked in/out for today
  if (msg.toLowerCase().includes('sudah') || msg.toLowerCase().includes('absen')) {
    activeScannedSiswa.value = {
      nama: studentObj ? studentObj.nama : 'Siswa Terverifikasi',
      isAlreadyScanned: true,
      customMessage: msg,
      kelas: studentObj ? studentObj.kelas : '-',
      nisn: studentObj ? studentObj.nisn : '-',
      jamMasuk: '-',
      verificationSource: `VERIFIKASI: ${sourceLabel}`
    }
    
    scanSuccessMsg.value = 'SUDAH ABSENSI'
    
    // Auto-clear success card after 3 seconds
    scanTimeout = setTimeout(() => {
      activeScannedSiswa.value = null
    }, 3000)
  } else {
    // Display error overlay card (unrecognized, etc.)
    activeScanError.value = {
      message: source === 'kamera' ? 'GAGAL ABSENSI' : `GAGAL ABSENSI (${sourceLabel})`,
      detail: msg
    }

    // Enable cooldown so we don't spam requests while showing the error card
    isCooldownActive.value = true
    setTimeout(() => {
      activeScanError.value = null
      isCooldownActive.value = false
    }, 3000)
  }
}

function handleScanSuccess(newLog, source) {
  if (scanTimeout) {
    clearTimeout(scanTimeout)
    scanTimeout = null
  }

  const sourceLabels = {
    kamera: 'VERIFIKASI: SCAN WAJAH (AI)',
    rfid: 'VERIFIKASI: KARTU RFID',
    fingerprint: 'VERIFIKASI: SIDIK JARI'
  }

  activeScannedSiswa.value = {
    nama: newLog.nama,
    kelas: newLog.kelas,
    nisn: newLog.nisn,
    jamMasuk: newLog.tipe === 'Masuk' ? newLog.waktu : '-',
    jamKeluar: newLog.tipe === 'Keluar' ? newLog.waktu : null,
    verificationSource: sourceLabels[source] || 'VERIFIKASI SISTEM'
  }

  scanSuccessMsg.value = `ABSENSI ${newLog.tipe.toUpperCase()} BERHASIL!`
  
  // Show a beautiful success pop-up toast notification
  toast.success('Absensi Berhasil', {
    description: `${newLog.nama} (${newLog.kelas}) - Berhasil Absen ${newLog.tipe} pada ${newLog.waktu}`,
    position: 'top-center',
    duration: 2000
  })
  
  // Add to sidebar scan logs
  scanResults.value.unshift(newLog)
  
  // Sync local lists
  getStudents().then(data => absensiData.value = data)
  
  // Timer 2 seconds to hide overlay card (user request)
  scanTimeout = setTimeout(() => {
    if (activeScannedSiswa.value?.nama === newLog.nama) {
      activeScannedSiswa.value = null
    }
  }, 2000)
}

// ─── Fullscreen Functions ────────────────────────────────
function enterFullscreen() {
  document.documentElement.requestFullscreen().catch(() => {})
}

function onFullscreenChange() {
  const isNowFullscreen = !!document.fullscreenElement
  if (!isNowFullscreen && isFullscreen.value) {
    // User baru saja keluar dari fullscreen (ESC / F11 / browser control)
    isPasswordGuardOpen.value = true
    passwordInput.value = ''
    passwordError.value = ''
  }
  isFullscreen.value = isNowFullscreen
}

function submitPasswordGuard() {
  // Password sama dengan yang didefinisikan di authStore
  const correctPassword = '123456'
  if (passwordInput.value === correctPassword) {
    isPasswordGuardOpen.value = false
    passwordInput.value = ''
    passwordError.value = ''
    // Jika halaman masih dalam fullscreen (misal trigger dari F12),
    // keluar dari fullscreen setelah password terverifikasi
    if (isFullscreen.value && document.fullscreenElement) {
      document.exitFullscreen().catch(() => {})
      // Reset flag supaya onFullscreenChange tidak membuka modal lagi
      isFullscreen.value = false
    }
  } else {
    passwordError.value = 'Password salah. Coba lagi.'
    passwordShake.value = true
    passwordInput.value = ''
    setTimeout(() => (passwordShake.value = false), 600)
  }
}

function goBackToFullscreen() {
  isPasswordGuardOpen.value = false
  passwordInput.value = ''
  passwordError.value = ''
  enterFullscreen()
}

// Tangkap F12 — tampilkan password guard
function onKeyDown(e) {
  if (e.key === 'F12') {
    e.preventDefault()
    isPasswordGuardOpen.value = true
    passwordInput.value = ''
    passwordError.value = ''
  }
}

// ─── Lifecycle ──────────────────────────────────────────
onMounted(async () => {
  loadData()
  updateClock()
  clockInterval = setInterval(updateClock, 1000)

  // Load face-api.js and model weights dynamically from CDN
  try {
    await loadScript('https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js')
    await window.faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/')
    isTrackerLoaded.value = true
  } catch (err) {
    console.error('Failed to load face-api.js neural network models:', err)
  }

  // Aktifkan Kamera Otomatis di Kiosk (Disabled by user request)
  // startCamera()

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

  // Daftarkan listener fullscreen & keyboard guard
  document.addEventListener('fullscreenchange', onFullscreenChange)
  document.addEventListener('keydown', onKeyDown)
})

onUnmounted(() => {
  stopCamera()
  if (clockInterval) clearInterval(clockInterval)
  if (logPollInterval) clearInterval(logPollInterval)
  if (scanTimeout) clearTimeout(scanTimeout)
  document.removeEventListener('fullscreenchange', onFullscreenChange)
  document.removeEventListener('keydown', onKeyDown)
})

const filteredStudents = computed(() => absensiData.value)
</script>

<template>
  <div class="kiosk-root">
    <!-- ══ TOP HEADER BAR ══ -->
    <header class="kiosk-header">
      <button v-if="!isFullscreen" class="back-btn" @click="router.push('/absensi/siswa')">
        <ArrowLeft class="size-4" />
        Kembali
      </button>
      <div v-else class="back-btn-placeholder" />
      <div class="header-center">
        <h1 class="header-title">Absensi & Presensi</h1>
        <p class="header-sub">Sistem Presensi</p>
      </div>
      <div class="header-right">
        <!-- Fullscreen Toggle Button (hidden when in fullscreen) -->
        <button
          v-if="!isFullscreen"
          class="fullscreen-btn"
          title="Masuk Mode Fullscreen"
          @click="enterFullscreen"
        >
          <Maximize2 class="size-4" />
          <span>Fullscreen</span>
        </button>
      </div>
    </header>

    <!-- ══ PASSWORD GUARD MODAL ══ -->
    <Teleport to="body">
      <div v-if="isPasswordGuardOpen" class="pg-overlay">
        <div class="pg-card" :class="{ 'pg-shake': passwordShake }">
          <!-- Icon -->
          <div class="pg-icon">
            <Lock class="size-8" />
          </div>

          <!-- Title -->
          <h2 class="pg-title">Verifikasi Admin</h2>
          <p class="pg-desc">
            Anda keluar dari mode fullscreen. Masukkan password Admin Sekolah
            untuk melanjutkan, atau kembali ke mode fullscreen.
          </p>

          <!-- Password Input -->
          <input
            v-model="passwordInput"
            type="password"
            class="pg-input"
            :class="{ 'pg-input--error': passwordError }"
            placeholder="Masukkan password..."
            autofocus
            @keyup.enter="submitPasswordGuard"
          />
          <p v-if="passwordError" class="pg-error">{{ passwordError }}</p>

          <!-- Actions -->
          <div class="pg-actions">
            <button class="pg-btn-fullscreen" @click="goBackToFullscreen">
              <Maximize2 class="size-4" />
              Kembali Fullscreen
            </button>
            <button class="pg-btn-confirm" @click="submitPasswordGuard">
              Konfirmasi
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="kiosk-main">
      <!-- LEFT: Live Viewport (Kamera + Success Card Overlay) -->
      <section class="viewport-section" v-motion :initial="glassSlide.initial" :visible-once="glassSlide.visible">
        <Card class="scanner-card">
          <!-- Viewport Header -->
          <div class="scanner-topbar">
            <div class="scanner-topbar-left">
              <span class="scanner-dot" />
              <span class="scanner-mode-text">Absensi Kamera</span>
            </div>
            <div class="scanner-topbar-right flex items-center gap-3">
              <button
                v-if="cameraStatus === 'active'"
                type="button"
                class="cam-toggle-btn cam-toggle-btn--stop"
                @click="stopCamera"
              >
                <CameraOff class="size-3" />
                <span>Nonaktifkan Kamera</span>
              </button>
              <button
                v-else-if="cameraStatus === 'idle'"
                type="button"
                class="cam-toggle-btn cam-toggle-btn--start"
                @click="startCamera"
              >
                <Camera class="size-3" />
                <span>Aktifkan Kamera</span>
              </button>

              <div class="flex items-center gap-1.5">
                <Wifi class="size-3.5" style="color: var(--primary)" />
                <span class="scanner-online">ONLINE</span>
              </div>
            </div>
          </div>

          <!-- The Viewport Container -->
          <div class="scanner-viewport scanner-viewport--cam">
            <!-- Camera Feed -->
            <video v-if="cameraStatus === 'active'" ref="videoRef" autoplay playsinline class="cam-feed" @play="onVideoPlay" />
            
            <!-- Real-time Face Bounding Box Canvas Overlay (Aspect-Aligned & Mirrored) -->
            <canvas v-show="cameraStatus === 'active' && !activeScannedSiswa" ref="overlayCanvasRef" class="overlay-canvas" />

            <!-- Scanning Sci-Fi Reticle Overlay (only when no success card is active) -->
            <div v-if="cameraStatus === 'active' && !activeScannedSiswa" class="cam-overlay">
              <span class="corner corner-tl" />
              <span class="corner corner-tr" />
              <span class="corner corner-bl" />
              <span class="corner corner-br" />
              <div class="cam-scan-line" />
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
                
                <!-- Friendly message for already scanned status -->
                <div v-if="activeScannedSiswa.isAlreadyScanned" class="my-4 px-4 text-center">
                  <p class="text-lg font-bold text-slate-700 dark:text-slate-200">{{ activeScannedSiswa.customMessage }}</p>
                </div>

                <!-- Regular Student Card details -->
                <template v-else>
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
                </template>

                <!-- Linear Countdown progress bar -->
                <div class="countdown-bar-container">
                  <div class="countdown-bar" :class="{ 'countdown-bar--info': activeScannedSiswa.isAlreadyScanned }"></div>
                </div>
              </div>
            </div>

            <!-- Unified Error Overlay (displays on top of camera/idle state) -->
            <div v-if="activeScanError" class="cam-success-overlay cam-success-overlay--error">
              <div class="kiosk-success-card kiosk-success-card--error">
                <div class="success-icon-badge success-icon-badge--error">
                  <AlertCircle class="size-8" style="color: var(--destructive)" />
                </div>
                <p class="success-status-label success-status-label--error">{{ activeScanError.message }}</p>
                
                <div class="my-4 px-4 text-center">
                  <p class="text-lg font-bold text-slate-700 dark:text-slate-200">{{ activeScanError.detail }}</p>
                </div>

                <div class="success-time-row">
                  <Clock class="size-3.5" />
                  <span>Silakan coba sesaat lagi</span>
                </div>

                <!-- Linear Countdown progress bar -->
                <div class="countdown-bar-container">
                  <div class="countdown-bar countdown-bar--error"></div>
                </div>
              </div>
            </div>
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
        </Card>

        <!-- Student Simulation Panel (For testing/demo) -->
        <Card class="simulation-card">
          <div class="log-header">
            <Fingerprint class="size-4" style="color: var(--primary)" />
            <span class="log-title">Simulasi Tap Siswa</span>
          </div>

          <!-- Quick Simulation Action Buttons -->
          <div class="px-4 pb-4 flex gap-2">
            <button
              type="button"
              class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer transition-colors shadow-sm"
              @click="triggerQuickScan('rfid')"
            >
              <Radio class="size-3.5" />
              <span>Tap RFID</span>
            </button>
            <button
              type="button"
              class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer transition-colors shadow-sm"
              @click="triggerQuickScan('fingerprint')"
            >
              <Fingerprint class="size-3.5" />
              <span>Tap Fingerprint</span>
            </button>
          </div>
        </Card>

        <!-- Scan Log List -->
        <Card class="log-card">
          <div class="log-header">
            <UserCheck class="size-4" style="color: var(--primary)" />
            <span class="log-title">Riwayat Absensi hari ini</span>
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
                <p class="log-meta">{{ log.kelas }} · {{ log.waktu }}</p>
              </div>
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
.back-btn-placeholder {
  /* Invisible spacer with same approximate width as back-btn to keep title centered */
  width: 90px;
  flex-shrink: 0;
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

.overlay-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 10;
  transform: scaleX(-1); /* match video mirror */
  pointer-events: none;
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

.face-target-box {
  width: 220px;
  height: 220px;
  border: 1px dashed rgba(52, 211, 153, 0.25);
  position: absolute;
  box-shadow: 0 0 0 999px rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
}

.face-target-box::before {
  content: '';
  position: absolute;
  inset: -2px;
  border: 2px solid transparent;
  background: 
    linear-gradient(to right, #34d399 20px, transparent 20px) 0 0,
    linear-gradient(to bottom, #34d399 20px, transparent 20px) 0 0,
    linear-gradient(to left, #34d399 20px, transparent 20px) 100% 0,
    linear-gradient(to bottom, #34d399 20px, transparent 20px) 100% 0,
    linear-gradient(to right, #34d399 20px, transparent 20px) 0 100%,
    linear-gradient(to top, #34d399 20px, transparent 20px) 0 100%,
    linear-gradient(to left, #34d399 20px, transparent 20px) 100% 100%,
    linear-gradient(to top, #34d399 20px, transparent 20px) 100% 100%;
  background-repeat: no-repeat;
  pointer-events: none;
  animation: target-glow 2s infinite ease-in-out;
}

@keyframes target-glow {
  0%, 100% { opacity: 0.6; filter: drop-shadow(0 0 2px #34d399); }
  50% { opacity: 1.0; filter: drop-shadow(0 0 8px #34d399); }
}

.cam-toggle-btn {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 0.35rem 0.75rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

.cam-toggle-btn--stop {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.cam-toggle-btn--stop:hover {
  background: #ef4444;
  color: #ffffff;
}

.cam-toggle-btn--start {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
  border: 1px solid rgba(59, 130, 246, 0.2);
}

.cam-toggle-btn--start:hover {
  background: #3b82f6;
  color: #ffffff;
}

.locked-mode-banner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border-radius: 8px;
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.05em;
  border: 1px solid rgba(16, 185, 129, 0.15);
  margin-top: 1rem;
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
  animation: shrinkWidth 2s linear forwards; /* match success card timeout */
}

.countdown-bar--error {
  background: var(--destructive) !important;
  animation: shrinkWidth 3s linear forwards !important; /* match error card timeout */
}

.countdown-bar--info {
  animation: shrinkWidth 3s linear forwards !important; /* match info card timeout */
}

.cam-success-overlay--error {
  background: rgba(15, 10, 15, 0.85) !important;
}

.kiosk-success-card--error {
  border-color: var(--destructive) !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6), 0 0 20px rgba(239, 68, 68, 0.25) !important;
}

.success-icon-badge--error {
  background: color-mix(in oklch, var(--destructive) 15%, transparent) !important;
  border-color: color-mix(in oklch, var(--destructive) 40%, transparent) !important;
}

.success-status-label--error {
  color: var(--destructive) !important;
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
@keyframes pg-enter {
  from { opacity: 0; transform: scale(0.9) translateY(20px); }
  to   { opacity: 1; transform: scale(1)   translateY(0); }
}
@keyframes pg-shake {
  0%, 100% { transform: translateX(0); }
  20%      { transform: translateX(-10px); }
  40%      { transform: translateX(10px); }
  60%      { transform: translateX(-7px); }
  80%      { transform: translateX(7px); }
}

/* ══════════════════════════════════════════
   FULLSCREEN BUTTON
   ══════════════════════════════════════════ */
.header-right {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.fullscreen-btn {
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
.fullscreen-btn:hover {
  background: var(--accent);
  color: var(--primary);
  border-color: var(--primary);
}
.fullscreen-btn--active {
  background: color-mix(in oklch, var(--primary) 10%, transparent);
  border-color: color-mix(in oklch, var(--primary) 40%, transparent);
  color: var(--primary);
}

/* ══════════════════════════════════════════
   PASSWORD GUARD OVERLAY
   ══════════════════════════════════════════ */
.pg-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: rgba(0, 0, 0, 0.88);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}

.pg-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 2.5rem 2rem;
  width: 100%;
  max-width: 420px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.04);
  animation: pg-enter 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.pg-shake {
  animation: pg-shake 0.55s ease !important;
}

.pg-icon {
  width: 76px;
  height: 76px;
  border-radius: 50%;
  background: color-mix(in oklch, var(--destructive) 12%, transparent);
  border: 1px solid color-mix(in oklch, var(--destructive) 30%, transparent);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--destructive);
  margin-bottom: 0.25rem;
}

.pg-title {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--foreground);
  margin: 0;
  letter-spacing: -0.02em;
}

.pg-desc {
  font-size: 0.8rem;
  color: var(--muted-foreground);
  text-align: center;
  line-height: 1.65;
  margin: 0;
  max-width: 320px;
}

.pg-input {
  width: 100%;
  padding: 0.8rem 1rem;
  border-radius: 12px;
  border: 1px solid var(--border);
  background: var(--muted);
  color: var(--foreground);
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-family: inherit;
}
.pg-input:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px color-mix(in oklch, var(--primary) 15%, transparent);
}
.pg-input--error {
  border-color: var(--destructive);
  box-shadow: 0 0 0 3px color-mix(in oklch, var(--destructive) 12%, transparent);
}

.pg-error {
  font-size: 0.75rem;
  color: var(--destructive);
  margin: -0.25rem 0 0;
  align-self: flex-start;
  font-weight: 600;
}

.pg-actions {
  display: flex;
  gap: 0.75rem;
  width: 100%;
  margin-top: 0.25rem;
}

.pg-btn-fullscreen {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.375rem;
  padding: 0.7rem 1rem;
  border-radius: 12px;
  border: 1px solid var(--border);
  background: var(--muted);
  color: var(--muted-foreground);
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
}
.pg-btn-fullscreen:hover {
  border-color: var(--primary);
  color: var(--primary);
  background: color-mix(in oklch, var(--primary) 8%, transparent);
}

.pg-btn-confirm {
  flex: 1;
  padding: 0.7rem 1rem;
  border-radius: 12px;
  border: none;
  background: var(--primary);
  color: var(--primary-foreground);
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  transition: opacity 0.2s, transform 0.15s;
  font-family: inherit;
}
.pg-btn-confirm:hover {
  opacity: 0.92;
  transform: translateY(-1px);
}
.pg-btn-confirm:active {
  transform: translateY(0);
}

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
