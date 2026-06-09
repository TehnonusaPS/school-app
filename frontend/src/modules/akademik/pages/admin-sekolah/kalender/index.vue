<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import {
  Table,
  TableHeader,
  TableBody,
  TableRow,
  TableHead,
  TableCell
} from '@/components/ui/table'
import {
  Plus,
  Send,
  Eye,
  Edit2,
  Trash2,
  Lock,
  Unlock,
  AlertCircle,
  CalendarDays,
  FileDown
} from 'lucide-vue-next'
import PageHeader from '@/components/page-header/PageHeader.vue'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import {
  getYearStatuses,
  saveYearStatuses,
  getEvents,
  saveEvents,
  tahunList,
  academicMonths
} from '../../../data/mockKalender'
import { glassFade } from '@/config/motion'

const router = useRouter()

// --- State ---
const yearStatuses = ref({})
const events = ref([])

const isRequestConfirmOpen = ref(false)
const selectedYearToRequest = ref('')

const isDeleteConfirmOpen = ref(false)
const selectedYearToDelete = ref('')

onMounted(() => {
  yearStatuses.value = getYearStatuses()
  events.value = getEvents()
})

// --- Helper Functions ---
function getYearStatusLabel(status) {
  if (status === 'approved') return 'Disetujui'
  if (status === 'pending') return 'Menunggu Persetujuan'
  if (status === 'rejected') return 'Ditolak'
  return 'Draft'
}

function getYearStatusBadgeClass(status) {
  if (status === 'approved') return 'bg-emerald-500 text-white border-none'
  if (status === 'pending') return 'bg-orange-500 text-white border-none animate-pulse'
  if (status === 'rejected') return 'bg-rose-500 text-white border-none'
  return 'bg-secondary text-secondary-foreground'
}

function isLocked(status) {
  return status === 'pending' || status === 'approved'
}

// Count events in a year (used for filtering only, not displayed)
function countEvents(year) {
  const startYear = parseInt(year.split('/')[0])
  const minDate = `${startYear}-07-01`
  const maxDate = `${startYear + 1}-06-30`
  return events.value.filter(e => e.startDate >= minDate && e.startDate <= maxDate).length
}

// --- Computed Displayed Years ---
const displayedTahunList = computed(() => {
  return tahunList.filter(year => {
    const status = yearStatuses.value[year]?.status || 'draft'
    const hasEvents = countEvents(year) > 0
    // Show if not draft OR if it's a draft that already has events (created via Create page)
    return status !== 'draft' || hasEvents
  })
})

// --- Actions ---
const handleCreate = () => {
  router.push('/akademik/admin-sekolah/kalender/create')
}

const handleShow = (year) => {
  router.push(`/akademik/admin-sekolah/kalender/show/${year.replace('/', '-')}`)
}

const handleEdit = (year) => {
  router.push(`/akademik/admin-sekolah/kalender/edit/${year.replace('/', '-')}`)
}

const openRequestConfirm = (year) => {
  const statusInfo = yearStatuses.value[year] || { status: 'draft' }
  if (isLocked(statusInfo.status)) return
  selectedYearToRequest.value = year
  isRequestConfirmOpen.value = true
}

const confirmRequest = () => {
  const year = selectedYearToRequest.value
  const updated = { ...yearStatuses.value }
  updated[year] = { status: 'pending', rejectedReason: '' }
  yearStatuses.value = updated
  saveYearStatuses(updated)
  isRequestConfirmOpen.value = false
  
  toast.success('Pengajuan Dikirim', {
    description: `Kalender akademik untuk Tahun Pelajaran ${year} telah berhasil diajukan.`
  })
}

const openDeleteConfirm = (year) => {
  const statusInfo = yearStatuses.value[year] || { status: 'draft' }
  if (isLocked(statusInfo.status)) {
    toast.error('Gagal', { description: 'Kalender sedang dikunci dan tidak bisa dihapus.' })
    return
  }
  selectedYearToDelete.value = year
  isDeleteConfirmOpen.value = true
}

const confirmDelete = () => {
  const year = selectedYearToDelete.value
  // Remove events of this year
  const startYear = parseInt(year.split('/')[0])
  const minDate = `${startYear}-07-01`
  const maxDate = `${startYear + 1}-06-30`
  
  const remainingEvents = events.value.filter(
    e => !(e.startDate >= minDate && e.startDate <= maxDate)
  )
  events.value = remainingEvents
  saveEvents(remainingEvents)
  
  // Reset status to draft
  const updatedStatuses = { ...yearStatuses.value }
  updatedStatuses[year] = { status: 'draft', rejectedReason: '' }
  yearStatuses.value = updatedStatuses
  saveYearStatuses(updatedStatuses)
  isDeleteConfirmOpen.value = false
  
  toast.success('Tahun Pelajaran Dihapus', {
    description: `Seluruh kegiatan akademik Tahun Pelajaran ${year} telah dibersihkan.`
  })
}

const handleReset = () => {
  if (confirm('Apakah Anda yakin ingin me-reset seluruh status dan agenda Kalender Akademik ke data bawaan?')) {
    localStorage.removeItem('academic_calendar_events_db_v2')
    localStorage.removeItem('academic_calendar_statuses_db_v2')
    toast.success('Database Kalender Berhasil Direset')
    setTimeout(() => {
      window.location.reload()
    }, 800)
  }
}

// --- Helper Functions for PDF Export ---
function formatDateRange(startStr, endStr) {
  if (!startStr) return '-'
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
  
  const start = new Date(startStr)
  const startDay = start.getDate()
  const startMonth = months[start.getMonth()]
  const startYear = start.getFullYear()
  
  if (!endStr || startStr === endStr) {
    return `${startDay} ${startMonth} ${startYear}`
  }
  
  const end = new Date(endStr)
  const endDay = end.getDate()
  const endMonth = months[end.getMonth()]
  const endYear = end.getFullYear()
  
  if (startYear === endYear) {
    if (startMonth === endMonth) {
      return `${startDay} - ${endDay} ${startMonth} ${startYear}`
    } else {
      return `${startDay} ${startMonth} - ${endDay} ${endMonth} ${startYear}`
    }
  } else {
    return `${startDay} ${startMonth} ${startYear} - ${endDay} ${endMonth} ${endYear}`
  }
}

function getFriendlyTypeName(type) {
  if (type === 'libur_nasional') return 'Hari Libur Nasional'
  if (type === 'tanggal_merah') return 'Tanggal Merah'
  if (type === 'ujian') return 'Ujian'
  if (type === 'kegiatan') return 'Kegiatan Sekolah'
  return type
}

const handleDownloadPdf = (year) => {
  const startYear = parseInt(year.split('/')[0])
  const minDate = `${startYear}-07-01`
  const maxDate = `${startYear + 1}-06-30`
  const yearEvents = events.value.filter(e => e.startDate >= minDate && e.startDate <= maxDate)

  // Sort events by date
  yearEvents.sort((a, b) => a.startDate.localeCompare(b.startDate))

  let monthsHtml = ''

  academicMonths.forEach(month => {
    const valYear = startYear + month.yearOffset
    const monthVal = month.val // 0-based index (Juli=6, ..., Juni=5)

    // Get calendar first day & total days
    const firstDay = new Date(valYear, monthVal, 1).getDay() // 0 = Sunday, 1 = Monday...
    const totalDays = new Date(valYear, monthVal + 1, 0).getDate()

    // Filter events for this month (for listing under the grid)
    const monthEvents = yearEvents.filter(e => {
      const parts = e.startDate.split('-')
      const eventYear = parseInt(parts[0])
      const eventMonth = parseInt(parts[1]) - 1
      return eventYear === valYear && eventMonth === monthVal
    })

    // Build grid cells
    let gridHtml = ''
    let day = 1
    
    // Max 6 weeks/rows per month
    for (let r = 0; r < 6; r++) {
      let rowHtml = ''
      let hasDays = false
      for (let d = 0; d < 7; d++) {
        const cellIndex = r * 7 + d
        if (cellIndex < firstDay || day > totalDays) {
          rowHtml += '<td class="empty-cell"></td>'
        } else {
          hasDays = true
          const dateStr = `${valYear}-${String(monthVal + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`
          const dayEvent = yearEvents.find(e => dateStr >= e.startDate && dateStr <= e.endDate)

          // Determine cell class
          let cellClass = 'cell-white'
          if (dayEvent) {
            if (dayEvent.type === 'libur_nasional' || dayEvent.type === 'tanggal_merah') {
              cellClass = 'cell-red'
            } else if (dayEvent.type === 'ujian') {
              cellClass = 'cell-yellow'
            } else if (dayEvent.type === 'kegiatan') {
              cellClass = 'cell-white' // simplified kegiatan to school day (white)
            }
          } else if (d === 0 || d === 6) {
            cellClass = 'cell-red' // Saturday & Sunday default red
          }

          rowHtml += `<td class="${cellClass}">${String(day).padStart(2, '0')}</td>`
          day++
        }
      }
      if (hasDays) {
        gridHtml += `<tr>${rowHtml}</tr>`
      }
    }

    // List of events/holidays under the calendar
    let eventsListHtml = ''
    if (monthEvents.length > 0) {
      eventsListHtml = '<div class="month-events-list">'
      monthEvents.forEach(ev => {
        let textClass = 'event-text-default'
        if (ev.type === 'libur_nasional' || ev.type === 'tanggal_merah') {
          textClass = 'event-text-red'
        } else if (ev.type === 'ujian') {
          textClass = 'event-text-yellow'
        }
        
        // Format day single or range
        const startDay = new Date(ev.startDate).getDate()
        let dateRangeLabel = `${startDay}`
        if (ev.endDate && ev.startDate !== ev.endDate) {
          const endDay = new Date(ev.endDate).getDate()
          dateRangeLabel = `${startDay}-${endDay}`
        }
        
        eventsListHtml += `<p class="${textClass}"><strong>${dateRangeLabel} ${month.name}:</strong> ${ev.title}</p>`
      })
      eventsListHtml += '</div>'
    }

    monthsHtml += `
      <div class="month-card">
        <div class="month-card-header">${month.name.toUpperCase()} ${valYear}</div>
        <table class="calendar-table">
          <thead>
            <tr>
              <th class="day-header sunday">M</th>
              <th class="day-header">S</th>
              <th class="day-header">S</th>
              <th class="day-header">R</th>
              <th class="day-header">K</th>
              <th class="day-header">J</th>
              <th class="day-header saturday">S</th>
            </tr>
          </thead>
          <tbody>
            ${gridHtml}
          </tbody>
        </table>
        ${eventsListHtml}
      </div>
    `
  })

  const status = yearStatuses.value[year]?.status || 'draft'
  const statusLabel = getYearStatusLabel(status)
  const todayStr = new Date().toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })

  const htmlContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Kalender Akademik - Tahun Pelajaran ${year}</title>
      <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        @page {
          size: A4 portrait;
          margin: 10mm 10mm 15mm 10mm;
        }
        
        body {
          font-family: 'Plus Jakarta Sans', sans-serif;
          color: #1e293b;
          background: #fff;
          margin: 0;
          padding: 0;
          font-size: 8px;
          line-height: 1.3;
          -webkit-print-color-adjust: exact;
          print-color-adjust: exact;
        }

        .header-container {
          text-align: center;
          border-bottom: 2px double #000;
          padding-bottom: 6px;
          margin-bottom: 12px;
        }
        .header-dept {
          margin: 0;
          font-size: 9px;
          font-weight: 700;
          letter-spacing: 0.5px;
          text-transform: uppercase;
          color: #334155;
        }
        .header-school {
          margin: 2px 0 0 0;
          font-size: 14px;
          font-weight: 800;
          letter-spacing: 0.5px;
          color: #0f172a;
          text-transform: uppercase;
        }
        .header-meta {
          margin: 3px 0 0 0;
          font-size: 8px;
          color: #475569;
          font-weight: 500;
        }

        .title-container {
          text-align: center;
          margin-bottom: 12px;
        }
        .doc-title {
          margin: 0;
          font-size: 11px;
          font-weight: 800;
          text-transform: uppercase;
          letter-spacing: 0.5px;
          color: #0f172a;
        }
        .doc-subtitle {
          margin: 2px 0 0 0;
          font-size: 9px;
          font-weight: 700;
          color: #475569;
        }
        .status-badge {
          display: inline-block;
          margin-top: 4px;
          background-color: #f1f5f9;
          color: #334155;
          font-size: 7px;
          font-weight: 700;
          padding: 1px 6px;
          border-radius: 9999px;
          text-transform: uppercase;
          border: 1px solid #e2e8f0;
        }

        /* 3-Column Grid for Months */
        .calendar-grid {
          display: grid;
          grid-template-columns: repeat(3, 1fr);
          gap: 10px;
          margin-bottom: 15px;
        }

        .month-card {
          border: 1px solid #e2e8f0;
          border-radius: 4px;
          overflow: hidden;
          background-color: #fff;
          page-break-inside: avoid;
          display: flex;
          flex-direction: column;
        }
        .month-card-header {
          background-color: #4a154b; /* Solid dark purple header */
          color: #ffffff;
          font-size: 8px;
          font-weight: 800;
          text-align: center;
          padding: 4px 0;
          letter-spacing: 0.5px;
        }
        
        .calendar-table {
          width: 100%;
          border-collapse: collapse;
        }
        .calendar-table th, .calendar-table td {
          border: 1px solid #e2e8f0;
          width: 14.28%;
          text-align: center;
          font-size: 7.5px;
          font-weight: 700;
          height: 14px;
          vertical-align: middle;
          padding: 0;
        }
        .day-header {
          background-color: #f8fafc;
          color: #475569;
          font-weight: 800;
          height: 12px;
        }
        .day-header.sunday, .day-header.saturday {
          color: #ef4444; /* Sunday and Saturday header red */
        }

        /* 3 Color Classes */
        .cell-white {
          background-color: #ffffff !important;
          color: #1e293b !important;
        }
        .cell-red {
          background-color: #ef4444 !important;
          color: #ffffff !important;
        }
        .cell-yellow {
          background-color: #facc15 !important;
          color: #000000 !important;
        }
        .empty-cell {
          background-color: #ffffff !important;
          border: 1px solid #e2e8f0;
        }

        /* Monthly event details list below each grid */
        .month-events-list {
          padding: 4px;
          border-top: 1px solid #f1f5f9;
          font-size: 6.5px;
          line-height: 1.25;
        }
        .month-events-list p {
          margin: 1px 0;
        }
        .event-text-default {
          color: #475569;
        }
        .event-text-red {
          color: #ef4444; /* Holidays red text */
          font-weight: 600;
        }
        .event-text-yellow {
          color: #b45309; /* Amber/Dark Yellow for Ujian descriptions */
          font-weight: 600;
        }

        /* Bottom Section: Legend & Signature */
        .bottom-section {
          margin-top: 15px;
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          page-break-inside: avoid;
          border-top: 1px solid #e2e8f0;
          padding-top: 10px;
        }

        /* Legend Style matching reference */
        .legend-container {
          width: 50%;
        }
        .legend-title {
          font-size: 8px;
          font-weight: 800;
          text-transform: uppercase;
          color: #0f172a;
          margin-bottom: 6px;
          letter-spacing: 0.5px;
        }
        .legend-grid {
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          gap: 6px;
        }
        .legend-item {
          display: flex;
          align-items: center;
          gap: 6px;
          font-size: 7.5px;
          font-weight: 600;
        }
        .legend-color-box {
          width: 14px;
          height: 14px;
          border-radius: 2px;
          border: 1px solid #cbd5e1;
        }
        .legend-red {
          background-color: #ef4444;
        }
        .legend-yellow {
          background-color: #facc15;
        }
        .legend-white {
          background-color: #ffffff;
        }

        .signature-container {
          width: 40%;
          text-align: center;
          font-size: 8px;
        }
        .signature-title {
          margin-bottom: 35px;
          line-height: 1.3;
        }
        .signature-name {
          text-decoration: underline;
          font-weight: 700;
          margin: 0;
        }
        .signature-nip {
          margin: 1px 0 0 0;
          color: #475569;
          font-size: 7px;
        }
      </style>
    </head>
    <body>
      <!-- KOP SURAT -->
      <div class="header-container">
        <h2 class="header-dept">KEMENTRIAN PENDIDIKAN TINGGI, SAINS DAN TEKNOLOGI</h2>
        <h1 class="header-school">SDN TEHNONUSA PRIMA I</h1>
      </div>

      <!-- TITLE -->
      <div class="title-container">
        <h3 class="doc-title">KALENDER AKADEMIK</h3>
        <h4 class="doc-subtitle">TAHUN PELAJARAN ${year}</h4>
      </div>

      <!-- 12 MONTHS GRID -->
      <div class="calendar-grid">
        ${monthsHtml}
      </div>

      <!-- BOTTOM SECTION: LEGEND & SIGNATURE -->
      <div class="bottom-section">
        <div class="legend-container">
          <div class="legend-title">Keterangan Warna</div>
          <div class="legend-grid">
            <div class="legend-item">
              <div class="legend-color-box legend-white"></div>
              <span>Hari Sekolah Efektif</span>
            </div>
            <div class="legend-item">
              <div class="legend-color-box legend-red"></div>
              <span>Libur (Akhir Pekan & Hari Libur Nasional)</span>
            </div>
            <div class="legend-item">
              <div class="legend-color-box legend-yellow"></div>
              <span>Ujian (PAS/UAS/Ujian Lainnya)</span>
            </div>
          </div>
        </div>
        
        <div class="signature-container">
          <p class="signature-title">
            Jakarta, ${todayStr}<br>
            <strong>Mengetahui,</strong><br>
            Kepala Sekolah SDN Tehnonusa Prima I
          </p>
          <p class="signature-name">Dr. H. Ahmad Dahlan, M.Pd.</p>
          <p class="signature-nip">NIP. 19700101 199512 1 001</p>
        </div>
      </div>
    </body>
    </html>
  `

  const iframe = document.createElement('iframe')
  iframe.style.position = 'absolute'
  iframe.style.width = '0'
  iframe.style.height = '0'
  iframe.style.border = 'none'
  document.body.appendChild(iframe)

  const doc = iframe.contentWindow.document
  doc.open()
  doc.write(htmlContent)
  doc.close()

  setTimeout(() => {
    iframe.contentWindow.focus()
    iframe.contentWindow.print()
    setTimeout(() => {
      document.body.removeChild(iframe)
    }, 1000)
  }, 500)
}
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 text-left"
  >
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <PageHeader
        title="Kalender Akademik"
        description="Kelola jadwal kegiatan tahunan, libur nasional, ujian, dan persetujuan draf per Tahun Pelajaran."
      />
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <Button
          variant="outline"
          class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer flex items-center gap-2 h-10 shadow-xs text-rose-500 border-rose-500/25 hover:bg-rose-500/5 hover:text-rose-600"
          @click="handleReset"
        >
          Reset Kalender
        </Button>
        <Button
          variant="default"
          class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer bg-primary text-white hover:bg-primary/95 flex items-center gap-2 h-10 shadow-xs"
          @click="handleCreate"
        >
          <Plus class="h-4 w-4" />
          Buat Kalender Baru
        </Button>
      </div>
    </div>

    <!-- Table Card Container -->
    <Card class="rounded-2xl border bg-card shadow-xs overflow-hidden">
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <Table>
            <TableHeader class="bg-muted/30 border-b">
              <TableRow>
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider text-left w-[80px]">No</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider text-left">Tahun Akademik</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider text-left w-[200px]">Status Persetujuan</TableHead>
                <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider text-center w-[320px]">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            
            <TableBody>
              <TableRow
                v-for="(year, index) in displayedTahunList"
                :key="year"
                class="hover:bg-muted/10 transition-all border-b"
              >
                <!-- No -->
                <TableCell class="py-4 px-6 font-mono text-xs font-bold text-muted-foreground">
                  {{ index + 1 }}
                </TableCell>
                
                <!-- Tahun Akademik -->
                <TableCell class="py-4 px-4">
                  <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-lg bg-primary/5 text-primary flex items-center justify-center shrink-0 border border-primary/10">
                      <CalendarDays class="h-4 w-4" />
                    </div>
                    <div>
                      <span class="text-xs font-extrabold text-foreground">Tahun Pelajaran {{ year }}</span>
                      <p v-if="yearStatuses[year]?.status === 'rejected'" class="text-[9px] text-rose-500 font-bold mt-0.5 flex items-center gap-1">
                        <AlertCircle class="h-3 w-3" />
                        Ditolak: "{{ yearStatuses[year]?.rejectedReason }}"
                      </p>
                    </div>
                  </div>
                </TableCell>

                <!-- Status Persetujuan -->
                <TableCell class="py-4 px-4">
                  <Badge class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full uppercase" :class="getYearStatusBadgeClass(yearStatuses[year]?.status)">
                    {{ getYearStatusLabel(yearStatuses[year]?.status) }}
                  </Badge>
                </TableCell>

                <!-- Aksi -->
                <TableCell class="py-4 px-6 text-center">
                  <div class="flex items-center justify-center gap-3">
                    
                    <!-- Request Approval Button -->
                    <button
                      v-if="!isLocked(yearStatuses[year]?.status)"
                      class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
                      title="Request"
                      @click="openRequestConfirm(year)"
                    >
                      <Send class="size-4 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[9px] font-semibold leading-none">Request</span>
                    </button>
                    <div v-else class="flex flex-col items-center justify-center gap-0.5 text-muted-foreground/50">
                      <Lock class="size-4 text-muted-foreground/40" />
                      <span class="text-[9px] font-semibold leading-none">Terkunci</span>
                    </div>

                    <!-- Show Button -->
                    <button
                      class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
                      title="Show"
                      @click="handleShow(year)"
                    >
                      <Eye class="size-4 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[9px] font-semibold leading-none">Show</span>
                    </button>

                    <!-- Download Button (only if approved) -->
                    <button
                      v-if="yearStatuses[year]?.status === 'approved'"
                      class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
                      title="Download PDF"
                      @click="handleDownloadPdf(year)"
                    >
                      <FileDown class="size-4 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[9px] font-semibold leading-none">Download</span>
                    </button>

                    <!-- Edit Button -->
                    <button
                      v-if="!isLocked(yearStatuses[year]?.status)"
                      class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
                      title="Edit"
                      @click="handleEdit(year)"
                    >
                      <Edit2 class="size-4 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[9px] font-semibold leading-none">Edit</span>
                    </button>

                    <!-- Delete Button -->
                    <button
                      v-if="!isLocked(yearStatuses[year]?.status)"
                      class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
                      title="Hapus"
                      @click="openDeleteConfirm(year)"
                    >
                      <Trash2 class="size-4 transition-transform group-hover/btn:scale-110" />
                      <span class="text-[9px] font-semibold leading-none">Hapus</span>
                    </button>

                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </CardContent>
    </Card>

    <!-- REQUEST CONFIRM DIALOG -->
    <Dialog v-model:open="isRequestConfirmOpen">
      <DialogContent class="sm:max-w-[400px] rounded-2xl p-6 text-left">
        <DialogHeader>
          <DialogTitle class="text-sm font-bold text-foreground flex items-center gap-1.5">
            <Send class="h-5 w-5 text-emerald-500 animate-bounce" />
            Ajukan Kalender Akademik
          </DialogTitle>
          <DialogDescription class="text-[10px] text-muted-foreground leading-relaxed mt-2">
            Apakah Anda yakin ingin mengajukan Kalender Akademik Tahun Pelajaran {{ selectedYearToRequest }} ke Kepala Sekolah untuk ditinjau? Kalender ini tidak dapat diubah setelah diajukan.
          </DialogDescription>
        </DialogHeader>

        <DialogFooter class="flex flex-row items-center justify-end gap-2 pt-4 border-t mt-4">
          <Button
            type="button"
            variant="ghost"
            class="text-xs font-bold rounded-xl cursor-pointer"
            @click="isRequestConfirmOpen = false"
          >
            Batal
          </Button>
          <Button
            type="button"
            class="text-xs font-bold rounded-xl cursor-pointer bg-emerald-500 text-white hover:bg-emerald-600 border-none shadow-none"
            @click="confirmRequest"
          >
            Ya, Ajukan
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- DELETE CONFIRM DIALOG -->
    <Dialog v-model:open="isDeleteConfirmOpen">
      <DialogContent class="sm:max-w-[400px] rounded-2xl p-6 text-left">
        <DialogHeader>
          <DialogTitle class="text-sm font-bold text-foreground flex items-center gap-1.5">
            <Trash2 class="h-5 w-5 text-rose-500 animate-bounce" />
            Hapus Kalender Akademik
          </DialogTitle>
          <DialogDescription class="text-[10px] text-muted-foreground leading-relaxed mt-2">
            Apakah Anda yakin ingin menghapus seluruh agenda Kalender Akademik Tahun Pelajaran {{ selectedYearToDelete }}? Semua kegiatan yang telah terdaftar akan dibersihkan secara permanen dan tindakan ini tidak dapat dibatalkan.
          </DialogDescription>
        </DialogHeader>

        <DialogFooter class="flex flex-row items-center justify-end gap-2 pt-4 border-t mt-4">
          <Button
            type="button"
            variant="ghost"
            class="text-xs font-bold rounded-xl cursor-pointer"
            @click="isDeleteConfirmOpen = false"
          >
            Batal
          </Button>
          <Button
            type="button"
            class="text-xs font-bold rounded-xl cursor-pointer bg-rose-500 text-white hover:bg-rose-600 border-none shadow-none"
            @click="confirmDelete"
          >
            Ya, Hapus
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

  </div>
</template>
