<script setup>
import { ref, computed } from 'vue'
import { 
  BookOpen, 
  FileText, 
  Phone, 
  Send, 
  Smile, 
  Star, 
  MessageSquare, 
  Paperclip, 
  CheckCircle,
  MessageCircle,
  ClipboardList,
  Megaphone,
  Trophy,
  PenLine,
  Award
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

// Common Components
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import ActivityCard from '@/components/activity-card/ActivityCard.vue'

// UI Components
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { ProgressCircle } from '@/components/ui/progress-circle'
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel'

// Mock Profile
import { siswaProfile } from '../data/mockSiswaRaport'

// --- State ---
const selectedKelas = ref('11-A IPA')
const selectedSemester = ref('Semester Ganjil')
const messageText = ref('')

// --- Dynamic Parental Monitoring Data ---
const monitoringData = {
  '11-A IPA': {
    'Semester Ganjil': {
      avgKelas: 84.2,
      avgKelasTrend: '2.1% dari bulan lalu',
      avgKelasTrendDir: 'down',
      ip: 3.85,
      predikat: 'Sangat Baik',
      tugasPct: 98,
      tugasRatio: '42/43 Tugas',
      kehadiranPct: 97,
      hadirDays: 112,
      izinDays: 3,
      alpaDays: 0,
      waliKelas: 'Ibu Dra. Kartika Sari',
      catatanGuru: 'Aditya sangat aktif dalam diskusi kelompok hari ini. Pertahankan semangatnya!',
      catatanTime: '2 Hari Lalu',
      catatanGuruMapel: 'Guru Matematika',
      subjects: [
        { name: 'Matematika', teacher: 'Ibu Sri Wahyuni', score: 92, kkm: 75, status: 'Tuntas' },
        { name: 'Bahasa Inggris', teacher: 'Mr. James Wilson', score: 88, kkm: 75, status: 'Tuntas' },
        { name: 'Fisika', teacher: 'Bpk. Heru Susanto', score: 79, kkm: 70, status: 'Tuntas' }
      ]
    },
    'Semester Genap': {
      avgKelas: 85.6,
      avgKelasTrend: '1.4% dari bulan lalu',
      avgKelasTrendDir: 'up',
      ip: 3.92,
      predikat: 'Sangat Baik',
      tugasPct: 100,
      tugasRatio: '45/45 Tugas',
      kehadiranPct: 99,
      hadirDays: 115,
      izinDays: 1,
      alpaDays: 0,
      waliKelas: 'Ibu Dra. Kartika Sari',
      catatanGuru: 'Sikap belajar Aditya menunjukkan konsistensi yang sangat baik di kelas.',
      catatanTime: '1 Minggu Lalu',
      catatanGuruMapel: 'Guru Fisika',
      subjects: [
        { name: 'Matematika', teacher: 'Ibu Sri Wahyuni', score: 95, kkm: 75, status: 'Tuntas' },
        { name: 'Bahasa Inggris', teacher: 'Mr. James Wilson', score: 90, kkm: 75, status: 'Tuntas' },
        { name: 'Fisika', teacher: 'Bpk. Heru Susanto', score: 85, kkm: 70, status: 'Tuntas' }
      ]
    }
  },
  '10-A IPA': {
    'Semester Ganjil': {
      avgKelas: 82.1,
      avgKelasTrend: '1.2% dari bulan lalu',
      avgKelasTrendDir: 'down',
      ip: 3.55,
      predikat: 'Baik',
      tugasPct: 95,
      tugasRatio: '40/42 Tugas',
      kehadiranPct: 95,
      hadirDays: 108,
      izinDays: 5,
      alpaDays: 1,
      waliKelas: 'Ibu Dra. Kartika Sari',
      catatanGuru: 'Aditya perlu lebih fokus pada mata pelajaran eksak seperti Fisika.',
      catatanTime: '1 Bulan Lalu',
      catatanGuruMapel: 'Guru Fisika',
      subjects: [
        { name: 'Matematika', teacher: 'Ibu Sri Wahyuni', score: 85, kkm: 75, status: 'Tuntas' },
        { name: 'Bahasa Inggris', teacher: 'Mr. James Wilson', score: 80, kkm: 75, status: 'Tuntas' },
        { name: 'Fisika', teacher: 'Bpk. Heru Susanto', score: 70, kkm: 75, status: 'Remedial' }
      ]
    },
    'Semester Genap': {
      avgKelas: 83.8,
      avgKelasTrend: '1.7% dari bulan lalu',
      avgKelasTrendDir: 'up',
      ip: 3.70,
      predikat: 'Sangat Baik',
      tugasPct: 97,
      tugasRatio: '41/42 Tugas',
      kehadiranPct: 96,
      hadirDays: 110,
      izinDays: 4,
      alpaDays: 0,
      waliKelas: 'Ibu Dra. Kartika Sari',
      catatanGuru: 'Nilai Fisika Aditya mengalami kenaikan yang memuaskan.',
      catatanTime: '3 Minggu Lalu',
      catatanGuruMapel: 'Guru Wali Kelas',
      subjects: [
        { name: 'Matematika', teacher: 'Ibu Sri Wahyuni', score: 88, kkm: 75, status: 'Tuntas' },
        { name: 'Bahasa Inggris', teacher: 'Mr. James Wilson', score: 85, kkm: 75, status: 'Tuntas' },
        { name: 'Fisika', teacher: 'Bpk. Heru Susanto', score: 79, kkm: 75, status: 'Tuntas' }
      ]
    }
  }
}

const activeData = computed(() => {
  return monitoringData[selectedKelas.value]?.[selectedSemester.value] || monitoringData['11-A IPA']['Semester Ganjil']
})

// --- Carousel Recent Activities ---
const aktivitasTerkini = [
  { title: 'Analisis Puisi Modern', desc: 'B. Indonesia • Tenggat: Besok', icon: PenLine, variant: 'purple' },
  { title: 'Hukum Newton II', desc: 'Fisika • Nilai Keluar: 85', icon: ClipboardList, variant: 'green' },
  { title: 'Libur Semester Ganjil', desc: 'Admin • 18 Des - 2 Jan', icon: Megaphone, variant: 'amber' },
  { title: 'Latihan Basket Sore Ini', desc: 'Pembina • 15:30 - Selesai', icon: Trophy, variant: 'green' },
  { title: 'Latihan Soal Matriks', desc: 'Matematika • Tenggat: 3 hari lagi', icon: PenLine, variant: 'blue' },
  { title: 'Rapat Orang Tua Murid', desc: 'Kepsek • Sabtu, 09:00', icon: Megaphone, variant: 'purple' }
]

const getSubjectStyle = (subject) => {
  const styles = {
    'Matematika': { color: 'text-blue-500 dark:text-blue-400', bg: 'bg-blue-500/10 dark:bg-blue-500/20' },
    'Fisika': { color: 'text-emerald-500 dark:text-emerald-400', bg: 'bg-emerald-500/10 dark:bg-emerald-500/20' },
    'Bahasa Inggris': { color: 'text-indigo-500 dark:text-indigo-400', bg: 'bg-indigo-500/10 dark:bg-indigo-500/20' }
  }
  return styles[subject] || { color: 'text-primary', bg: 'bg-primary/10' }
}

// --- Action Handlers ---
const handleDownloadReport = () => {
  const fileName = `Raport_Monitoring_${siswaProfile.name.replace(/\s+/g, '_')}_${selectedKelas.value.replace(/\s+/g, '_')}_${selectedSemester.value.replace(/\s+/g, '_')}.pdf`
  
  // Dynamic mock PDF content
  const mockContent = `%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R >>
endobj
4 0 obj
<< /Length 380 >>
stream
BT
/F1 14 Tf
50 800 Td
(LAPORAN MONITORING PERKEMBANGAN AKADEMIK) Tj
0 -25 Td
(Siswa: ${siswaProfile.name}) Tj
0 -20 Td
(NISN: ${siswaProfile.nisn}) Tj
0 -20 Td
(Kelas: ${selectedKelas.value} - ${selectedSemester.value}) Tj
0 -40 Td
(Ringkasan Performa:) Tj
0 -20 Td
(- Rata-rata Kelas: ${activeData.value.avgKelas}) Tj
0 -20 Td
(- Indeks Prestasi (IP): ${activeData.value.ip} [${activeData.value.predikat}]) Tj
0 -20 Td
(- Tugas Terselesaikan: ${activeData.value.tugasRatio} (${activeData.value.tugasPct}%)) Tj
0 -20 Td
(- Kehadiran: ${activeData.value.kehadiranPct}% [Hadir: ${activeData.value.hadirDays}, Sakit: ${activeData.value.izinDays}, Alpa: ${activeData.value.alpaDays}]) Tj
0 -20 Td
(- Wali Kelas: ${activeData.value.waliKelas}) Tj
ET
endstream
endobj
xref
0 5
0000000000 65535 f
0000000009 00000 n
0000000062 00000 n
0000000121 00000 n
0000000213 00000 n
trailer
<< /Size 5 /Root 1 0 R >>
startxref
550
%%EOF`

  const blob = new Blob([mockContent], { type: 'application/pdf' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = fileName
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)

  toast.success(`Laporan monitoring perkembangan akademik berhasil diunduh!`, {
    description: `File saved as ${fileName}`
  })
}

const handleSendMessage = () => {
  if (!messageText.value.trim()) {
    toast.error('Gagal mengirim pesan', {
      description: 'Pesan tidak boleh kosong.'
    })
    return
  }
  toast.success('Pesan berhasil dikirim!', {
    description: 'Catatan Anda telah diteruskan ke pihak sekolah.'
  })
  messageText.value = ''
}

const handleContactWhatsApp = () => {
  toast.success('Membuka WhatsApp...', {
    description: `Menghubungkan ke chat ${activeData.value.waliKelas} (Wali Kelas).`
  })
}

const pageHeaderActions = computed(() => [
  {
    label: 'Unduh Raport Orang Tua',
    icon: FileText,
    variant: 'default',
    click: handleDownloadReport
  }
])
</script>

<template>
  <div class="space-y-6 max-w-[1400px] mx-auto pb-8 text-left">
    <!-- Header -->
    <PageHeader
      title="Monitoring Akademik Siswa"
      :description="`Laporan perkembangan belajar untuk ${siswaProfile.name} (${selectedKelas === '11-A IPA' ? 'Kelas 11-A IPA' : 'Kelas 10-A IPA'})`"
      :actions="pageHeaderActions"
    />

    <!-- Filter Selection Bar -->
    <div class="flex flex-wrap items-center gap-4 glass-ui p-4 rounded-2xl border border-white/10 dark:border-white/5 shadow-sm">
      <div class="flex flex-wrap items-center gap-3">
        <span class="text-sm font-semibold text-foreground">Filter</span>
        <Select v-model="selectedKelas">
          <SelectTrigger class="w-[180px] h-9 bg-background/50 backdrop-blur-sm">
            <SelectValue placeholder="Pilih Kelas" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="11-A IPA">Kelas 11-A IPA</SelectItem>
            <SelectItem value="10-A IPA">Kelas 10-A IPA</SelectItem>
          </SelectContent>
        </Select>

        <Select v-model="selectedSemester">
          <SelectTrigger class="w-[180px] h-9 bg-background/50 backdrop-blur-sm">
            <SelectValue placeholder="Pilih Semester" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="Semester Ganjil">Semester Ganjil</SelectItem>
            <SelectItem value="Semester Genap">Semester Genap</SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid gap-6 grid-cols-1 lg:grid-cols-3">
      <!-- Left Column (Stats & Subjects List & Messages) -->
      <div class="lg:col-span-2 space-y-6">
        
        <!-- Score Card Summary -->
        <!-- Stats Cards -->
        <StatCardGrid cols="3">
          <StatCard
            label="Rata-rata Kelas"
            :value="activeData.avgKelas"
            :sub="activeData.avgKelasTrend"
            :trend="activeData.avgKelasTrendDir === 'up' ? 'Naik' : 'Turun'"
            :trendDirection="activeData.avgKelasTrendDir"
            :icon="BookOpen"
            variant="violet"
          />
          <StatCard
            label="Indeks Prestasi"
            :value="activeData.ip"
            :sub="activeData.predikat"
            :trend="activeData.predikat"
            trendDirection="neutral"
            :icon="Star"
            variant="emerald"
          />
          <StatCard
            label="Tugas Selesai"
            :value="`${activeData.tugasPct}%`"
            :sub="activeData.tugasRatio"
            :icon="Award"
            variant="amber"
          />
        </StatCardGrid>

        <!-- Score Card Summary -->
        <Card class="glass-ui p-6 rounded-2xl border border-white/10 dark:border-white/5 shadow-sm">
          <CardHeader class="p-0 pb-4 mb-4 border-b border-white/5 flex flex-row items-center justify-between">
            <div class="flex items-center gap-2">
              <BookOpen class="size-5 text-muted-foreground" />
              <CardTitle class="text-base font-bold text-foreground">Ringkasan Nilai {{ selectedSemester }}</CardTitle>
            </div>
            <Badge variant="blue" class="font-bold text-[10px] tracking-wide uppercase px-2 py-0.5 rounded-full">
              Ranking: {{ selectedKelas === '11-A IPA' ? '3 / 32' : '5 / 30' }}
            </Badge>
          </CardHeader>
          
          <CardContent class="p-0 space-y-6">

            <!-- Subject Detail Rows -->
            <div class="space-y-3">
              <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider block">Detail Nilai Mata Pelajaran Utama</span>
              
              <div 
                v-for="sub in activeData.subjects" 
                :key="sub.name"
                class="flex items-center justify-between p-3.5 rounded-xl border border-white/5 bg-background/20 backdrop-blur-sm hover:bg-background/40 transition-colors"
              >
                <div class="flex items-center gap-3 text-left">
                  <div 
                    :class="[
                      'rounded-xl p-2.5 shrink-0 border border-white/5 shadow-sm',
                      getSubjectStyle(sub.name).bg,
                      getSubjectStyle(sub.name).color
                    ]"
                  >
                    <BookOpen class="size-4.5" />
                  </div>
                  <div class="space-y-0.5">
                    <h5 class="font-bold text-sm text-foreground">{{ sub.name }}</h5>
                    <p class="text-[10px] text-muted-foreground">Guru: {{ sub.teacher }}</p>
                  </div>
                </div>

                <div class="text-right space-y-0.5 shrink-0">
                  <div class="font-black text-base text-foreground tabular-nums">{{ sub.score }}</div>
                  <Badge 
                    variant="outline" 
                    :class="[
                      'text-[9px] font-bold px-1.5 py-0 border-none rounded-full h-4 leading-none',
                      sub.status === 'Tuntas' 
                        ? 'bg-emerald-500/10 text-emerald-500' 
                        : 'bg-red-500/10 text-red-500'
                    ]"
                  >
                    {{ sub.status }} (KKM {{ sub.kkm }})
                  </Badge>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Messages / Notes Card -->
        <Card class="glass-ui p-6 rounded-2xl border border-white/10 dark:border-white/5 shadow-sm">
          <CardHeader class="p-0 pb-4 mb-4 border-b border-white/5 flex flex-row items-center gap-2">
            <MessageSquare class="size-5 text-muted-foreground" />
            <CardTitle class="text-base font-bold text-foreground">Catatan Orang Tua & Guru</CardTitle>
          </CardHeader>
          <CardContent class="p-0 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Message History -->
            <div class="space-y-3">
              <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider block">Saluran komunikasi dua arah</span>
              <div class="bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/10 p-4 rounded-xl space-y-2 relative overflow-hidden">
                <div class="flex items-center justify-between text-[10px] font-bold">
                  <span class="text-blue-500 dark:text-blue-400">{{ activeData.catatanGuruMapel }}</span>
                  <span class="text-muted-foreground">{{ activeData.catatanTime }}</span>
                </div>
                <p class="text-xs text-foreground italic leading-relaxed">
                  "{{ activeData.catatanGuru }}"
                </p>
              </div>
            </div>

            <!-- Right Message Form -->
            <div class="space-y-3">
              <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider block">Kirim Catatan atau Pertanyaan ke Sekolah</span>
              <div class="space-y-2">
                <Textarea 
                  v-model="messageText"
                  placeholder="Tuliskan pesan Anda di sini... (Misal: Mohon info jadwal remedial, Aditya sedang kurang sehat)" 
                  class="min-h-[90px] text-xs bg-background/50 dark:bg-background/20 border-white/10 dark:border-white/5 rounded-xl resize-none placeholder-muted-foreground/50"
                />
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2 text-muted-foreground">
                    <Button variant="ghost" size="icon" class="h-7 w-7" title="Lampiran">
                      <Paperclip class="size-3.5" />
                    </Button>
                    <Button variant="ghost" size="icon" class="h-7 w-7" title="Emoji">
                      <Smile class="size-3.5" />
                    </Button>
                  </div>
                  <Button size="sm" class="px-4 py-1.5 h-8 font-semibold flex items-center gap-1.5 rounded-lg" @click="handleSendMessage">
                    <Send class="size-3" />
                    Kirim Pesan
                  </Button>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Right Column (Attendance & Wali Kelas) -->
      <div class="space-y-6">
        <!-- Attendance Ring Card -->
        <Card class="glass-ui p-6 rounded-2xl border border-white/10 dark:border-white/5 shadow-sm flex flex-col items-center justify-center">
          <CardHeader class="p-0 pb-4 mb-4 border-b border-white/5 w-full flex flex-row items-center gap-2">
            <CheckCircle class="size-5 text-muted-foreground" />
            <CardTitle class="text-base font-bold text-foreground">Presensi Kehadiran</CardTitle>
          </CardHeader>
          <CardContent class="p-0 flex flex-col items-center w-full">
            <div class="my-3">
              <ProgressCircle 
                :percentage="activeData.kehadiranPct" 
                :size="150" 
                :stroke-width="12" 
                variant="blue"
              >
                <span class="text-3xl font-black text-foreground tabular-nums">{{ activeData.kehadiranPct }}%</span>
                <span class="text-[10px] font-semibold text-muted-foreground uppercase tracking-wider">Kehadiran</span>
              </ProgressCircle>
            </div>
            
            <div class="w-full space-y-2 mt-4 text-xs font-semibold">
              <div class="flex items-center justify-between pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                  <span class="size-2 bg-blue-500 rounded-full"></span>
                  <span class="text-muted-foreground">Hadir</span>
                </div>
                <span class="text-foreground tabular-nums">{{ activeData.hadirDays }} Hari</span>
              </div>
              <div class="flex items-center justify-between pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                  <span class="size-2 bg-amber-500 rounded-full"></span>
                  <span class="text-muted-foreground">Izin/Sakit</span>
                </div>
                <span class="text-foreground tabular-nums">{{ activeData.izinDays }} Hari</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="size-2 bg-red-500 rounded-full"></span>
                  <span class="text-muted-foreground">Alpa</span>
                </div>
                <span class="text-foreground tabular-nums">{{ activeData.alpaDays }} Hari</span>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Wali Kelas Card -->
        <Card class="bg-gradient-to-br from-[#0c1a30] to-[#122849] dark:from-slate-900 dark:to-slate-800 text-white p-6 rounded-2xl border border-white/10 shadow-lg relative overflow-hidden group">
          <!-- Watermark icon background -->
          <MessageCircle class="absolute -right-6 -bottom-6 size-24 opacity-10 rotate-12 transition-transform duration-300 group-hover:scale-110" />
          
          <CardContent class="p-0 space-y-4 relative z-10 flex flex-col justify-between h-full">
            <div class="space-y-1 text-left">
              <span class="text-[10px] font-bold text-white/60 uppercase tracking-wider block">Wali Kelas</span>
              <h4 class="text-lg font-black tracking-tight text-white">{{ activeData.waliKelas }}</h4>
            </div>
            <Button 
              variant="outline" 
              class="w-full font-semibold border-white/20 bg-white text-slate-900 hover:bg-white/95 rounded-xl h-10 mt-2 shadow-sm transition-all flex items-center justify-center gap-2"
              @click="handleContactWhatsApp"
            >
              <Phone class="size-4" />
              Hubungi via WhatsApp
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Carousel: Aktivitas Terkini Section -->
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-foreground">Aktivitas Terkini</h3>
      </div>

      <!-- Embla Carousel wrapper -->
      <Carousel class="w-full relative" :opts="{ align: 'start' }">
        <CarouselContent class="-ml-4">
          <CarouselItem 
            v-for="(act, idx) in aktivitasTerkini" 
            :key="idx" 
            class="pl-4 basis-full sm:basis-1/2 md:basis-1/3 lg:basis-1/4"
          >
            <ActivityCard
              :leading-icon="act.icon"
              :title="act.title"
              :description="act.desc"
              :variant="act.variant"
            />
          </CarouselItem>
        </CarouselContent>
        <!-- Custom absolute navigation arrows -->
        <div class="absolute -top-10 right-0 flex gap-2">
          <CarouselPrevious class="static translate-y-0 translate-x-0 h-8 w-8 rounded-lg bg-background/50 dark:bg-background/20 border border-white/10 hover:bg-background transition-colors" />
          <CarouselNext class="static translate-y-0 translate-x-0 h-8 w-8 rounded-lg bg-background/50 dark:bg-background/20 border border-white/10 hover:bg-background transition-colors" />
        </div>
      </Carousel>
    </div>
  </div>
</template>
