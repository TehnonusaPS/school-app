<script setup>
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRoute } from 'vue-router'
import { useRouter } from 'vue-router'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { NativeSelect } from '@/components/ui/native-select'
import { ClipboardList, CreditCard, CheckCircle2, ArrowRight, CalendarDays, Download, Printer, Search, Wallet, Banknote, Settings, FileText, Mail, RefreshCw, ShieldCheck, BookOpen, HelpCircle, AlertTriangle } from 'lucide-vue-next'
import CetakKwitansi from './CetakKwitansi.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const currentRole = computed(() => auth.user?.role || 'guest')
const sAdmin = computed(() => currentRole.value === 'superadmin')
const isAdminYayasan = computed(() => currentRole.value === 'admin_yayasan')
const isKepsek = computed(() => currentRole.value === 'kepala_sekolah')
const isAdminSekolah = computed(() => currentRole.value === 'admin_sekolah')
const isTataUsaha = computed(() => currentRole.value === 'tata_usaha')
const isGuru = computed(() => currentRole.value === 'guru')
const isWaliKelas = computed(() => currentRole.value === 'wali_kelas')
const isSiswa = computed(() => currentRole.value === 'siswa')
const isOrangTua = computed(() => currentRole.value === 'orang_tua')

const description = computed(() => route.meta.description || 'Selamat datang di halaman ini.')

const goToKwitansi = () => {
 router.push('/keuangan/cetak-kwitansi')
}

const gotoPengeluaranKecil = () => {
  router.push('/keuangan/input-pengeluaran-kecil')
}

const gotoPenerimaPembayaran = () => {
  router.push('/keuangan/penerimaan-pembayaran')
}

// State untuk Dashboard Orang Tua
const paySpp = ref(true)
const payLab = ref(true)
const payFieldTrip = ref(true)
const selectedPaymentMethod = ref('bca')

const totalPayment = computed(() => {
  let total = 0
  if (paySpp.value) total += 1200000
  if (payLab.value) total += 150000
  if (payFieldTrip.value) total += 100000
  return total
})

const formatRupiah = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value)
}
</script>

<template>
  <div class="space-y-6 w-full mx-auto px-0">
    <!-- Page Header -->
    <div class="border-b pb-4">
      <div class="flex flex-col gap-2 p-2">
        <div class="flex items-center gap-3">
          <h1 class="text-2xl font-bold tracking-tight">Manajemen Keuangan</h1>
          <span
            class="rounded-full bg-primary/10 text-primary px-3 py-1 text-xs font-semibold uppercase"
          >
            {{ currentRole }}
          </span>
        </div>
        <p class="text-muted-foreground">{{ description }}</p>
      </div>
    </div>

    <div v-if="sAdmin" class="space-y-6 w-full">
      <Card class="w-full">
        <CardContent class="space-y-6 p-6">
          <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div class="space-y-3">
              <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Keuangan</p>
              <h2 class="text-3xl font-semibold">Tinjau daftar sekolah dalam Pembayaran & langganan</h2>
              <p class="max-w-3xl text-sm text-muted-foreground">Lihat ringkasan yayasan, sekolah, total pemasukan, dan posisi tagihan untuk memonitor kegiatan keuangan secara cepat.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
              <Button variant="outline" class="rounded-lg">
                <CalendarDays class="size-4" />
                Last 30 Days
              </Button>
              <Button class="rounded-lg">
                <Download class="size-4" />
                Export Report
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <div class="grid gap-4 md:grid-cols-4 w-full">
        <Card class="p-5">
          <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Total Yayasan</p>
          <div class="mt-4 flex items-center justify-between gap-4">
            <div>
              <p class="text-3xl font-bold">3</p>
            </div>
            <div class="rounded-xl bg-muted p-3 text-foreground">🏢</div>
          </div>
        </Card>
        <Card class="p-5">
          <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Total Sekolah</p>
          <div class="mt-4 flex items-center justify-between gap-4">
            <div>
              <p class="text-3xl font-bold">15</p>
            </div>
            <div class="rounded-xl bg-muted p-3 text-foreground">🎓</div>
          </div>
        </Card>
        <Card class="p-5">
          <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Total uang masuk</p>
          <div class="mt-4 flex items-center justify-between gap-4">
            <div>
              <p class="text-3xl font-bold">Rp. 5.000.000</p>
            </div>
            <div class="rounded-xl bg-muted p-3 text-foreground">💰</div>
          </div>
        </Card>
        <Card class="p-5">
          <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Total Tagihan</p>
          <div class="mt-4 flex items-center justify-between gap-4">
            <div>
              <p class="text-3xl font-bold">Rp. 34.000.000</p>
            </div>
            <div class="rounded-xl bg-muted p-3 text-foreground">📄</div>
          </div>
        </Card>
      </div>

      <Card class="w-full p-4">
        <CardContent class="p-0">
          <div class="grid gap-4 xl:grid-cols-[1.5fr_1.5fr_2fr]">
            <div>
              <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.3em] text-muted-foreground">Semua Yayasan</label>
              <NativeSelect class="mt-2 w-full" size="default">
                <option value="">Semua Yayasan</option>
                <option value="yayasan-a">Yayasan A</option>
                <option value="yayasan-b">Yayasan B</option>
              </NativeSelect>
            </div>
            <div>
              <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.3em] text-muted-foreground">Semua Sekolah</label>
              <NativeSelect class="mt-2 w-full" size="default">
                <option value="">Semua Sekolah</option>
                <option value="sd-1">SD Negeri 1</option>
                <option value="smp-2">SMP Negeri 2</option>
              </NativeSelect>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-end">
              <div class="relative flex-1">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input class="mt-2 pl-10" placeholder="Cari Data ..." />
              </div>
              <Button variant="outline" class="mt-2 h-11 min-w-[4rem]">
                <Printer class="size-4" />
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card class="w-full">
        <CardContent class="p-4">
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="border-b bg-muted text-left text-xs uppercase tracking-[0.2em] text-muted-foreground">
                  <th class="px-4 py-3">Nama Yayasan</th>
                  <th class="px-4 py-3">Tanggal</th>
                  <th class="px-4 py-3">Diajukan Oleh</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b hover:bg-accent">
                  <td class="px-4 py-4 font-medium">Masa Pengenalan Lingkungan Sekolah (MPLS)</td>
                  <td class="px-4 py-4">14 - 16 Juli 2025</td>
                  <td class="px-4 py-4">Admin Sekolah</td>
                  <td class="px-4 py-4"><span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">Approve</span></td>
                  <td class="px-4 py-4 flex gap-2">
                    <Button variant="outline" size="sm" class="h-9 px-3">✔️</Button>
                    <Button variant="outline" size="sm" class="h-9 px-3">✖️</Button>
                  </td>
                </tr>
                <tr class="border-b hover:bg-accent">
                  <td class="px-4 py-4 font-medium">Libur HUT RI</td>
                  <td class="px-4 py-4">14 - 16 Juli 2025</td>
                  <td class="px-4 py-4">Admin Sekolah</td>
                  <td class="px-4 py-4"><span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">Approve</span></td>
                  <td class="px-4 py-4 flex gap-2">
                    <Button variant="outline" size="sm" class="h-9 px-3">✔️</Button>
                    <Button variant="outline" size="sm" class="h-9 px-3">✖️</Button>
                  </td>
                </tr>
                <tr class="border-b hover:bg-accent">
                  <td class="px-4 py-4 font-medium">Ujian Semester Akhir</td>
                  <td class="px-4 py-4">14 - 16 Juli 2025</td>
                  <td class="px-4 py-4">Admin Sekolah</td>
                  <td class="px-4 py-4"><span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">Approve</span></td>
                  <td class="px-4 py-4 flex gap-2">
                    <Button variant="outline" size="sm" class="h-9 px-3">✔️</Button>
                    <Button variant="outline" size="sm" class="h-9 px-3">✖️</Button>
                  </td>
                </tr>
                <tr class="border-b hover:bg-accent">
                  <td class="px-4 py-4 font-medium">Ujian Semester Akhir</td>
                  <td class="px-4 py-4">14 - 16 Juli 2025</td>
                  <td class="px-4 py-4">Admin Sekolah</td>
                  <td class="px-4 py-4"><span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">Approve</span></td>
                  <td class="px-4 py-4 flex gap-2">
                    <Button variant="outline" size="sm" class="h-9 px-3">✔️</Button>
                    <Button variant="outline" size="sm" class="h-9 px-3">✖️</Button>
                  </td>
                </tr>
                <tr class="border-b hover:bg-accent">
                  <td class="px-4 py-4 font-medium">Ujian Semester Akhir</td>
                  <td class="px-4 py-4">14 - 16 Juli 2025</td>
                  <td class="px-4 py-4">Admin Sekolah</td>
                  <td class="px-4 py-4"><span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">Approve</span></td>
                  <td class="px-4 py-4 flex gap-2">
                    <Button variant="outline" size="sm" class="h-9 px-3">✔️</Button>
                    <Button variant="outline" size="sm" class="h-9 px-3">✖️</Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>

    <Card v-if="isAdminYayasan" class=" p-6">
      <div class="flex items-center gap-3 mb-4">
        <CheckCircle2 class="size-5 text-primary" />
        <h2 class="text-lg font-semibold">Selamat Datang, Admin Yayasan!</h2>
      </div>
      <p class="text-sm text-muted-foreground mb-4">
        Berikut adalah ringkasan aktivitas keuangan terbaru dan tugas yang perlu Anda tangani hari ini.
      </p>
      <button class="bg-primary text-primary-foreground px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary/90 transition flex items-center gap-2">
        Lihat Tugas Hari Ini
        <ArrowRight class="size-4" />
      </button>
    </Card>

    <div v-if="isKepsek" class="space-y-6 w-full">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Keuangan Sekolah</p>
          <h2 class="text-3xl font-semibold">Ringkasan Keuangan Kepala Sekolah</h2>
          <p class="max-w-2xl text-sm text-muted-foreground mt-2">Pantau pendapatan, pengeluaran, anggaran, dan permintaan persetujuan dari tim Anda.</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <Button variant="outline" class="rounded-lg">Generate School Report</Button>
          <Button class="rounded-lg bg-primary text-primary-foreground hover:bg-primary/90">Approve All Requests</Button>
        </div>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <Card class="p-6">
          <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Total Pendapatan</p>
          <p class="mt-4 text-3xl font-semibold">Rp 4.2B</p>
          <p class="text-sm text-muted-foreground mt-2">84% dari target tahunan</p>
          <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-secondary">
            <div class="h-full w-[84%] rounded-full bg-primary"></div>
          </div>
        </Card>
        <Card class="p-6">
          <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Beban Operasional</p>
          <p class="mt-4 text-3xl font-semibold">Rp 1.8B</p>
          <p class="text-sm text-primary mt-2">↗ 4.2% bulan ini</p>
        </Card>
        <Card class="p-6">
          <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Sisa Anggaran (SMT 1)</p>
          <p class="mt-4 text-3xl font-semibold">Rp 650M</p>
          <p class="text-sm text-muted-foreground mt-2">Aman: Sisa 65%</p>
          <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-secondary">
            <div class="h-full w-[65%] rounded-full bg-primary"></div>
          </div>
        </Card>
        <Card class="p-6">
          <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground">Menunggu Persetujuan</p>
          <div class="mt-4 flex items-center gap-3">
            <p class="text-4xl font-semibold">12</p>
            <span class="rounded-full bg-destructive/10 px-3 py-1 text-xs font-semibold text-destructive">Prioritas Tinggi</span>
          </div>
          <p class="text-sm text-muted-foreground mt-2">Persetujuan Kepala Sekolah</p>
        </Card>
      </div>

      <div class="grid gap-4 xl:grid-cols-[1.3fr_1fr]">
        <Card class="p-6">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h3 class="text-lg font-semibold">Penagihan SPP Bulan Ini</h3>
              <p class="text-sm text-muted-foreground">Februari 2024</p>
            </div>
          </div>
          <div class="space-y-4 mt-6">
            <div>
              <div class="flex items-center justify-between text-sm font-medium text-foreground">
                <span>Kelas 10 (High School)</span>
                <span>92%</span>
              </div>
              <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-secondary">
                <div class="h-full w-[92%] rounded-full bg-primary"></div>
              </div>
            </div>
            <div>
              <div class="flex items-center justify-between text-sm font-medium text-foreground">
                <span>Kelas 11 (High School)</span>
                <span>78%</span>
              </div>
              <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-secondary">
                <div class="h-full w-[78%] rounded-full bg-primary"></div>
              </div>
            </div>
            <div>
              <div class="flex items-center justify-between text-sm font-medium text-foreground">
                <span>Kelas 12 (High School)</span>
                <span>64%</span>
              </div>
              <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-secondary">
                <div class="h-full w-[64%] rounded-full bg-destructive"></div>
              </div>
            </div>
          </div>
          <blockquote class="mt-6 rounded-lg border border-border bg-muted/50 p-4 text-sm italic text-muted-foreground">
            "Catatan: Kelas 12 memiliki tunggakan tertinggi karena persiapan ujian akhir."
          </blockquote>
        </Card>

        <Card class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold">Tren Pengeluaran vs Anggaran</h3>
              <p class="text-sm text-muted-foreground">Realisasi dan limit anggaran per bulan</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-muted-foreground">
              <span class="inline-flex h-2 w-2 rounded-full bg-primary"></span> Realisasi
              <span class="inline-flex h-2 w-2 rounded-full bg-secondary"></span> Limit Anggaran
            </div>
          </div>
          <div class="mt-8 h-[22rem] rounded-3xl border border-dashed border-border bg-muted/50 text-center text-sm text-muted-foreground grid place-items-center">
            Grafik placeholder
          </div>
          <div class="mt-4 grid grid-cols-3 text-xs uppercase tracking-[0.3em] text-muted-foreground">
            <span>Sep</span>
            <span>Okt</span>
            <span>Nov</span>
          </div>
          <div class="mt-2 grid grid-cols-3 text-xs uppercase tracking-[0.3em] text-muted-foreground">
            <span>Des</span>
            <span>Jan</span>
            <span>Feb</span>
          </div>
        </Card>
      </div>

      <Card class="w-full">
        <CardContent class="p-4">
          <div class="flex items-center justify-between gap-4 border-b border-border pb-4">
            <div>
              <h3 class="text-lg font-semibold">Permintaan Pengeluaran Menunggu</h3>
              <p class="text-sm text-muted-foreground">12 Total</p>
            </div>
            <Button variant="ghost" class="text-sm">Lihat Semua</Button>
          </div>
          <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="border-b bg-muted text-left text-xs uppercase tracking-[0.2em] text-muted-foreground">
                  <th class="px-4 py-3">Departemen</th>
                  <th class="px-4 py-3">Item Pengadaan</th>
                  <th class="px-4 py-3">Nominal</th>
                  <th class="px-4 py-3">Prioritas</th>
                  <th class="px-4 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b hover:bg-accent">
                  <td class="px-4 py-4">Laboratorium IPA</td>
                  <td class="px-4 py-4">Upgrade Mikroskop Digital (5 unit)</td>
                  <td class="px-4 py-4">Rp 45.000.000</td>
                  <td class="px-4 py-4"><span class="rounded-full bg-destructive/10 px-2 py-1 text-xs font-semibold text-destructive">Tinggi</span></td>
                  <td class="px-4 py-4 flex gap-2">
                    <Button variant="outline" size="sm" class="h-9 px-3">✖️</Button>
                    <Button class="h-9 px-3 bg-primary text-primary-foreground hover:bg-primary/90">Sign</Button>
                  </td>
                </tr>
                <tr class="border-b hover:bg-accent">
                  <td class="px-4 py-4">Kesiswaan</td>
                  <td class="px-4 py-4">Sewa Bus Lomba Basket Provinsi</td>
                  <td class="px-4 py-4">Rp 7.500.000</td>
                  <td class="px-4 py-4"><span class="rounded-full bg-muted px-2 py-1 text-xs font-semibold text-muted-foreground">Medium</span></td>
                  <td class="px-4 py-4 flex gap-2">
                    <Button variant="outline" size="sm" class="h-9 px-3">✖️</Button>
                    <Button class="h-9 px-3 bg-primary text-primary-foreground hover:bg-primary/90">Sign</Button>
                  </td>
                </tr>
                <tr class="hover:bg-accent">
                  <td class="px-4 py-4">Administrasi / TU</td>
                  <td class="px-4 py-4">Kertas Ujian & Toner Printer</td>
                  <td class="px-4 py-4">Rp 12.250.000</td>
                  <td class="px-4 py-4"><span class="rounded-full bg-destructive/10 px-2 py-1 text-xs font-semibold text-destructive">Tinggi</span></td>
                  <td class="px-4 py-4 flex gap-2">
                    <Button variant="outline" size="sm" class="h-9 px-3">✖️</Button>
                    <Button class="h-9 px-3 bg-primary text-primary-foreground hover:bg-primary/90">Sign</Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>

    <div v-if="isAdminSekolah" class="space-y-6">
      <!-- Top Section: Action Cards + Info Boxes -->
      <div class="grid gap-4 grid-cols-[3fr_2fr]">
        <!-- Left: Action Cards (3 columns) -->
        <div class="grid gap-4 grid-cols-3">
          <!-- Card 1: Terima Pembayaran SPP -->
          <Card @click="gotoPenerimaPembayaran" class=" hover:border-primary p-6 cursor-pointer transition-all duration-200 hover:shadow-md hover:-translate-y-1">
            <div class="bg-primary text-primary-foreground rounded-lg w-10 h-10 flex items-center justify-center mb-3">
              <CreditCard class="size-5" />
            </div>
            <h3 class="font-semibold text-sm">Terima Pembayaran SPP</h3>
            <p class="text-xs text-muted-foreground mt-2">Catat pembayaran SPP siswa secara instan.</p>
          </Card>

          <!-- Card 2: Input Pengeluaran Kecil -->
          <Card @click="gotoPengeluaranKecil" class=" hover:border-blue-500 p-6 cursor-pointer transition-all duration-200 hover:shadow-md hover:-translate-y-1">
            <div class="bg-blue-500 text-white rounded-lg w-10 h-10 flex items-center justify-center mb-3">
              <ClipboardList class="size-5" />
            </div>
            <h3 class="font-semibold text-sm">Input Pengeluaran Kecil</h3>
            <p class="text-xs text-muted-foreground mt-2">Entri pengeluaran operasional harian.</p>
          </Card>

          <!-- Card 3: Cetak Kuitansi -->
          <Card @click="goToKwitansi" class=" hover:border-emerald-500 p-6 cursor-pointer transition-all duration-200 hover:shadow-md hover:-translate-y-1">
            <div class="bg-emerald-500 text-white rounded-lg w-10 h-10 flex items-center justify-center mb-3">
              <ClipboardList class="size-5" />
            </div>
            <h3 class="font-semibold text-sm">Cetak Kuitansi</h3>
            <p class="text-xs text-muted-foreground mt-2">Generate kuitansi pembayaran.</p>
          </Card>
        </div>

        <!-- Right: Info Boxes -->
        <div class="space-y-3">
          <!-- Kas Kecil Tersedia -->
          <StatCard
            label="Kas Kecil Tersedia"
            value="Rp 4.500.000"
            variant="blue"
            :icon="Wallet"
          />

          <!-- Total SPP Terkumpul -->
          <StatCard
            label="Total SPP Terkumpul (Bulan Ini)"
            value="Rp 128.450.000"
            variant="emerald"
            :icon="Banknote"
          />

          <!-- Pending Verifikasi -->
          <StatCard
            label="Pending Verifikasi"
            value="12 Transaksi"
            variant="amber"
            :icon="ClipboardList"
          />
        </div>
      </div>

      <!-- Tabs and Table Section -->
      <Card class=" p-6">
        <!-- Tabs -->
        <div class="flex gap-6 border-b mb-6">
          <button class="pb-3 font-medium text-sm border-b-2 border-primary text-primary">
            Antrian Pembayaran SPP
          </button>
          <button class="pb-3 font-medium text-sm text-muted-foreground hover:text-primary">
            Log Kas Kecil
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b bg-muted/50">
                <th class="px-4 py-3 text-left font-semibold text-xs uppercase text-muted-foreground">NAMA SISWA</th>
                <th class="px-4 py-3 text-left font-semibold text-xs uppercase text-muted-foreground">KELAS</th>
                <th class="px-4 py-3 text-left font-semibold text-xs uppercase text-muted-foreground">BULAN</th>
                <th class="px-4 py-3 text-left font-semibold text-xs uppercase text-muted-foreground">JUMLAH</th>
                <th class="px-4 py-3 text-left font-semibold text-xs uppercase text-muted-foreground">STATUS</th>
                <th class="px-4 py-3 text-left font-semibold text-xs uppercase text-muted-foreground">AKSI</th>
              </tr>
            </thead>
            <tbody>
              <!-- Row 1 -->
              <tr class="border-b hover:bg-muted/50">
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <div class="bg-secondary text-secondary-foreground rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">AS</div>
                    <span class="font-medium">Aditya Saputra</span>
                  </div>
                </td>
                <td class="px-4 py-4">XII IPA 1</td>
                <td class="px-4 py-4">Oktober 2023</td>
                <td class="px-4 py-4">Rp 750.000</td>
                <td class="px-4 py-4"><span class="bg-secondary text-secondary-foreground px-2 py-1 rounded text-xs font-semibold">PENDING</span></td>
                <td class="px-4 py-4 flex gap-2">
                  <button class="bg-primary text-primary-foreground px-3 py-1 rounded text-xs font-semibold hover:bg-primary/90">Proses</button>
                  <button class="border border-border text-foreground px-3 py-1 rounded text-xs hover:bg-accent">📄</button>
                </td>
              </tr>
              <!-- Row 2 -->
              <tr class="border-b hover:bg-muted/50">
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <div class="bg-primary text-primary-foreground rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">BN</div>
                    <span class="font-medium">Bella Novita</span>
                  </div>
                </td>
                <td class="px-4 py-4">X IPS 2</td>
                <td class="px-4 py-4">Oktober 2023</td>
                <td class="px-4 py-4">Rp 650.000</td>
                <td class="px-4 py-4"><span class="bg-primary/10 text-primary px-2 py-1 rounded text-xs font-semibold">PAID</span></td>
                <td class="px-4 py-4 flex gap-2">
                  <button class="border border-border text-foreground px-3 py-1 rounded text-xs hover:bg-accent">Selesai</button>
                  <button class="border border-border text-foreground px-3 py-1 rounded text-xs hover:bg-accent">⚙️</button>
                </td>
              </tr>
              <!-- Row 3 -->
              <tr class="border-b hover:bg-muted/50">
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <div class="bg-muted-foreground text-primary-foreground rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">DW</div>
                    <span class="font-medium">Diki Wahyudi</span>
                  </div>
                </td>
                <td class="px-4 py-4">XI IPA 4</td>
                <td class="px-4 py-4">Oktober 2023</td>
                <td class="px-4 py-4">Rp 750.000</td>
                <td class="px-4 py-4"><span class="bg-destructive/10 text-destructive px-2 py-1 rounded text-xs font-semibold">OVERDUE</span></td>
                <td class="px-4 py-4 flex gap-2">
                  <button class="bg-primary text-primary-foreground px-3 py-1 rounded text-xs font-semibold hover:bg-primary/90">Proses</button>
                  <button class="border border-border text-foreground px-3 py-1 rounded text-xs hover:bg-accent">✉️</button>
                </td>
              </tr>
              <!-- Row 4 -->
              <tr class="border-b hover:bg-muted/50">
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <div class="bg-primary text-primary-foreground rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">FL</div>
                    <span class="font-medium">Farah Lestari</span>
                  </div>
                </td>
                <td class="px-4 py-4">XII IPS 1</td>
                <td class="px-4 py-4">Oktober 2023</td>
                <td class="px-4 py-4">Rp 750.000</td>
                <td class="px-4 py-4"><span class="bg-secondary text-secondary-foreground px-2 py-1 rounded text-xs font-semibold">PENDING</span></td>
                <td class="px-4 py-4 flex gap-2">
                  <button class="bg-primary text-primary-foreground px-3 py-1 rounded text-xs font-semibold hover:bg-primary/90">Proses</button>
                  <button class="border border-border text-foreground px-3 py-1 rounded text-xs hover:bg-accent">📄</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4">
          <p class="text-xs text-muted-foreground">Menampilkan 4 dari 48 antriaan pembayaran hari ini.</p>
          <div class="flex gap-1">
            <button class="px-2 py-1 hover:bg-muted rounded text-sm">←</button>
            <button class="px-2 py-1 bg-primary text-primary-foreground rounded text-sm">1</button>
            <button class="px-2 py-1 hover:bg-muted rounded text-sm">2</button>
            <button class="px-2 py-1 hover:bg-muted rounded text-sm">3</button>
            <button class="px-2 py-1 hover:bg-muted rounded text-sm">→</button>
          </div>
        </div>
      </Card>

      <!-- Bottom Section: Riwayat Audit + Bantuan Tata Usaha -->
      <div class="grid gap-4 grid-cols-[2fr_1.5fr]">
        <!-- Riwayat Audit Terbaru -->
        <Card class=" p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Riwayat Audit Terbaru</h2>
            <button class="text-sm text-muted-foreground hover:text-primary">Lihat Semua</button>
          </div>
          <div class="space-y-4">
            <!-- Audit Item 1 -->
            <div class="flex gap-3 pb-4 border-b">
              <div class="text-2xl">✓</div>
              <div class="flex-1">
                <p class="font-semibold text-sm">Kuitansi Dicetak #INV-202309</p>
                <p class="text-xs text-muted-foreground">Oleh: Budi Santoso • 10:45 AM</p>
              </div>
              <span class="text-xs font-semibold text-primary">Done</span>
            </div>
            <!-- Audit Item 2 -->
            <div class="flex gap-3 pb-4 border-b">
              <div class="text-2xl">⟲</div>
              <div class="flex-1">
                <p class="font-semibold text-sm">Revisi Input Kas Kecil (Pembelian ATK)</p>
                <p class="text-xs text-muted-foreground">Oleh: Budi Santoso • 09:12 AM</p>
                <p class="text-xs font-medium text-destructive mt-1">Rp 125.000</p>
              </div>
            </div>
            <!-- Audit Item 3 -->
            <div class="flex gap-3">
              <div class="text-2xl">🛡️</div>
              <div class="flex-1">
                <p class="font-semibold text-sm">Verifikasi Pembayaran Siswa: Citra</p>
                <p class="text-xs text-muted-foreground">Oleh: Sistem (Audit) • 08:30 AM</p>
              </div>
              <span class="text-xs font-semibold text-primary">Verified</span>
            </div>
          </div>
        </Card>

        <!-- Bantuan Tata Usaha -->
        <Card class="bg-primary text-primary-foreground p-6 border-none shadow-sm rounded-xl">
          <h2 class="text-lg font-semibold mb-2 text-primary-foreground">Bantuan Tata Usaha</h2>
          <p class="text-sm text-primary-foreground/80 mb-4">
            Butuh panduan pengelolaan dana BOS atau pelajaran SPP tahunan?
          </p>
          <div class="space-y-2 mb-6">
            <div class="flex items-start gap-2">
              <span class="text-lg">📋</span>
              <span class="text-sm">Panduan SOP Keuangan</span>
            </div>
            <div class="flex items-start gap-2">
              <span class="text-lg">🏫</span>
              <span class="text-sm">Hubungi Bendahara Sekolah</span>
            </div>
          </div>
          <button class="w-full bg-background text-foreground py-2 rounded-lg font-semibold hover:bg-accent transition">
            Buka Pusat Bantuan
          </button>
        </Card>
      </div>
    </div>

    <div v-if="isTataUsaha" class="space-y-6">
      <!-- Top Section: Action Cards + Info Boxes -->
      <div class="grid gap-4 grid-cols-[3fr_2fr]">
        <!-- Left: Action Cards (3 columns) -->
        <div class="grid gap-4 grid-cols-3">
          <!-- Card 1: Terima Pembayaran SPP -->
          <Card @click="gotoPenerimaPembayaran" class=" hover:border-primary p-6 cursor-pointer transition-all duration-200 hover:shadow-md hover:-translate-y-1">
            <div class="bg-primary text-primary-foreground rounded-lg w-10 h-10 flex items-center justify-center mb-3">
              <CreditCard class="size-5" />
            </div>
            <h3 class="font-semibold text-sm">Terima Pembayaran SPP</h3>
            <p class="text-xs text-muted-foreground mt-2">Catat pembayaran SPP siswa secara instan.</p>
          </Card>

          <!-- Card 2: Input Pengeluaran Kecil -->
          <Card @click="gotoPengeluaranKecil" class=" hover:border-blue-500 p-6 cursor-pointer transition-all duration-200 hover:shadow-md hover:-translate-y-1">
            <div class="bg-blue-500 text-white rounded-lg w-10 h-10 flex items-center justify-center mb-3">
              <ClipboardList class="size-5" />
            </div>
            <h3 class="font-semibold text-sm">Input Pengeluaran Kecil</h3>
            <p class="text-xs text-muted-foreground mt-2">Entri pengeluaran operasional harian.</p>
          </Card>

          <!-- Card 3: Cetak Kuitansi -->
          <Card @click="goToKwitansi" class=" hover:border-emerald-500 p-6 cursor-pointer transition-all duration-200 hover:shadow-md hover:-translate-y-1">
            <div class="bg-emerald-500 text-white rounded-lg w-10 h-10 flex items-center justify-center mb-3">
              <ClipboardList class="size-5" />
            </div>
            <h3 class="font-semibold text-sm">Cetak Kuitansi</h3>
            <p class="text-xs text-muted-foreground mt-2">Generate kuitansi pembayaran.</p>
          </Card>
        </div>

        <!-- Right: Info Boxes -->
        <div class="space-y-3">
          <!-- Kas Kecil Tersedia -->
          <StatCard
            label="Kas Kecil Tersedia"
            value="Rp 4.500.000"
            variant="blue"
            :icon="Wallet"
          />

          <!-- Total SPP Terkumpul -->
          <StatCard
            label="Total SPP Terkumpul (Bulan Ini)"
            value="Rp 128.450.000"
            variant="emerald"
            :icon="Banknote"
          />

          <!-- Pending Verifikasi -->
          <StatCard
            label="Pending Verifikasi"
            value="12 Transaksi"
            variant="amber"
            :icon="ClipboardList"
          />
        </div>
      </div>

      <!-- Tabs and Table Section -->
      <Card class=" p-6">
        <!-- Tabs -->
        <div class="flex gap-6 border-b mb-6">
          <button class="pb-3 font-medium text-sm border-b-2 border-primary text-primary">
            Antrian Pembayaran SPP
          </button>
          <button class="pb-3 font-medium text-sm text-muted-foreground hover:text-primary">
            Log Kas Kecil
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead class="w-[50px] text-center font-semibold text-xs uppercase text-muted-foreground">NO</TableHead>
                <TableHead class="font-semibold text-xs uppercase text-muted-foreground">NAMA SISWA</TableHead>
                <TableHead class="font-semibold text-xs uppercase text-muted-foreground">KELAS</TableHead>
                <TableHead class="font-semibold text-xs uppercase text-muted-foreground">BULAN</TableHead>
                <TableHead class="font-semibold text-xs uppercase text-muted-foreground">JUMLAH</TableHead>
                <TableHead class="font-semibold text-xs uppercase text-muted-foreground">STATUS</TableHead>
                <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-right">AKSI</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <!-- Row 1 -->
              <TableRow>
                <TableCell class="text-center font-medium">1</TableCell>
                <TableCell>
                  <div class="flex items-center gap-2">
                    <div class="bg-secondary text-secondary-foreground rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">AS</div>
                    <span class="font-medium">Aditya Saputra</span>
                  </div>
                </TableCell>
                <TableCell>XII IPA 1</TableCell>
                <TableCell>Oktober 2023</TableCell>
                <TableCell>Rp 750.000</TableCell>
                <TableCell>
                  <Badge variant="outline" class="bg-secondary text-secondary-foreground font-semibold">PENDING</Badge>
                </TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end gap-2">
                    <Button size="sm" class="h-8 px-2.5 bg-primary text-primary-foreground hover:bg-primary/90">Proses</Button>
                    <Button size="sm" variant="outline" class="h-8 px-2.5"><FileText class="size-3.5" /></Button>
                  </div>
                </TableCell>
              </TableRow>
              <!-- Row 2 -->
              <TableRow>
                <TableCell class="text-center font-medium">2</TableCell>
                <TableCell>
                  <div class="flex items-center gap-2">
                    <div class="bg-primary text-primary-foreground rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">BN</div>
                    <span class="font-medium">Bella Novita</span>
                  </div>
                </TableCell>
                <TableCell>X IPS 2</TableCell>
                <TableCell>Oktober 2023</TableCell>
                <TableCell>Rp 650.000</TableCell>
                <TableCell>
                  <Badge variant="outline" class="bg-primary/10 text-primary border-primary/20 hover:bg-primary/10 font-semibold">PAID</Badge>
                </TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end gap-2">
                    <Button size="sm" variant="outline" class="h-8 px-2.5">Selesai</Button>
                    <Button size="sm" variant="outline" class="h-8 px-2.5"><Settings class="size-3.5" /></Button>
                  </div>
                </TableCell>
              </TableRow>
              <!-- Row 3 -->
              <TableRow>
                <TableCell class="text-center font-medium">3</TableCell>
                <TableCell>
                  <div class="flex items-center gap-2">
                    <div class="bg-muted-foreground text-primary-foreground rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">DW</div>
                    <span class="font-medium">Diki Wahyudi</span>
                  </div>
                </TableCell>
                <TableCell>XI IPA 4</TableCell>
                <TableCell>Oktober 2023</TableCell>
                <TableCell>Rp 750.000</TableCell>
                <TableCell>
                  <Badge variant="destructive" class="bg-destructive/10 text-destructive border-destructive/20 hover:bg-destructive/10 font-semibold">OVERDUE</Badge>
                </TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end gap-2">
                    <Button size="sm" class="h-8 px-2.5 bg-primary text-primary-foreground hover:bg-primary/90">Proses</Button>
                    <Button size="sm" variant="outline" class="h-8 px-2.5"><Mail class="size-3.5" /></Button>
                  </div>
                </TableCell>
              </TableRow>
              <!-- Row 4 -->
              <TableRow>
                <TableCell class="text-center font-medium">4</TableCell>
                <TableCell>
                  <div class="flex items-center gap-2">
                    <div class="bg-primary text-primary-foreground rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">FL</div>
                    <span class="font-medium">Farah Lestari</span>
                  </div>
                </TableCell>
                <TableCell>XII IPS 1</TableCell>
                <TableCell>Oktober 2023</TableCell>
                <TableCell>Rp 750.000</TableCell>
                <TableCell>
                  <Badge variant="outline" class="bg-secondary text-secondary-foreground font-semibold">PENDING</Badge>
                </TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end gap-2">
                    <Button size="sm" class="h-8 px-2.5 bg-primary text-primary-foreground hover:bg-primary/90">Proses</Button>
                    <Button size="sm" variant="outline" class="h-8 px-2.5"><FileText class="size-3.5" /></Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4">
          <p class="text-xs text-muted-foreground">Menampilkan 4 dari 48 antrian pembayaran hari ini.</p>
          <div class="flex gap-1">
            <Button variant="outline" size="sm" class="h-8 w-8 p-0">←</Button>
            <Button size="sm" class="h-8 w-8 p-0 bg-primary text-primary-foreground">1</Button>
            <Button variant="outline" size="sm" class="h-8 w-8 p-0">2</Button>
            <Button variant="outline" size="sm" class="h-8 w-8 p-0">3</Button>
            <Button variant="outline" size="sm" class="h-8 w-8 p-0">→</Button>
          </div>
        </div>
      </Card>

      <!-- Bottom Section: Riwayat Audit + Bantuan Tata Usaha -->
      <div class="grid gap-4 grid-cols-[2fr_1.5fr]">
        <!-- Riwayat Audit Terbaru -->
        <Card class=" p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Riwayat Audit Terbaru</h2>
            <Button variant="link" class="text-sm text-muted-foreground hover:text-primary h-auto p-0">Lihat Semua</Button>
          </div>
          <div class="space-y-4">
            <!-- Audit Item 1 -->
            <div class="flex gap-3 pb-4 border-b items-start">
              <div class="bg-primary/10 text-primary rounded-full p-2 flex items-center justify-center shrink-0">
                <CheckCircle2 class="size-4" />
              </div>
              <div class="flex-1">
                <p class="font-semibold text-sm">Kuitansi Dicetak #INV-202309</p>
                <p class="text-xs text-muted-foreground">Oleh: Budi Santoso • 10:45 AM</p>
              </div>
              <span class="text-xs font-semibold text-primary">Done</span>
            </div>
            <!-- Audit Item 2 -->
            <div class="flex gap-3 pb-4 border-b items-start">
              <div class="bg-amber-500/10 text-amber-500 rounded-full p-2 flex items-center justify-center shrink-0">
                <RefreshCw class="size-4" />
              </div>
              <div class="flex-1">
                <p class="font-semibold text-sm">Revisi Input Kas Kecil (Pembelian ATK)</p>
                <p class="text-xs text-muted-foreground">Oleh: Budi Santoso • 09:12 AM</p>
                <p class="text-xs font-medium text-destructive mt-1">Rp 125.000</p>
              </div>
            </div>
            <!-- Audit Item 3 -->
            <div class="flex gap-3 items-start">
              <div class="bg-emerald-500/10 text-emerald-500 rounded-full p-2 flex items-center justify-center shrink-0">
                <ShieldCheck class="size-4" />
              </div>
              <div class="flex-1">
                <p class="font-semibold text-sm">Verifikasi Pembayaran Siswa: Citra</p>
                <p class="text-xs text-muted-foreground">Oleh: Sistem (Audit) • 08:30 AM</p>
              </div>
              <span class="text-xs font-semibold text-primary">Verified</span>
            </div>
          </div>
        </Card>

        <!-- Bantuan Tata Usaha -->
        <Card class="bg-primary text-primary-foreground p-6 border-none shadow-sm rounded-xl">
          <h2 class="text-lg font-semibold mb-2 text-primary-foreground">Bantuan Tata Usaha</h2>
          <p class="text-sm text-primary-foreground/80 mb-4">
            Butuh panduan pengelolaan dana BOS atau pelajaran SPP tahunan?
          </p>
          <div class="space-y-2 mb-6">
            <div class="flex items-start gap-2.5">
              <BookOpen class="size-4 text-primary-foreground/90 mt-0.5 shrink-0" />
              <span class="text-sm">Panduan SOP Keuangan</span>
            </div>
            <div class="flex items-start gap-2.5">
              <HelpCircle class="size-4 text-primary-foreground/90 mt-0.5 shrink-0" />
              <span class="text-sm">Hubungi Bendahara Sekolah</span>
            </div>
          </div>
          <Button class="w-full bg-background text-foreground hover:bg-background/90 hover:text-foreground font-semibold">
            Buka Pusat Bantuan
          </Button>
        </Card>
      </div>
    </div>
    
    <Card v-else-if="isGuru" class=" p-6">
      <div class="flex items-center gap-3 mb-4">
        <CheckCircle2 class="size-5 text-primary" />
        <h2 class="text-lg font-semibold">Selamat Datang, Guru!</h2>
      </div>
      <p class="text-sm text-muted-foreground mb-4">
        Berikut adalah ringkasan aktivitas keuangan terbaru dan tugas yang perlu Anda tangani hari ini.
      </p>
      <button class="bg-primary text-primary-foreground px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary/90 transition flex items-center gap-2">
        Lihat Tugas Hari Ini
        <ArrowRight class="size-4" />
      </button>
    </Card>  

    <Card v-else-if="isWaliKelas" class=" p-6">
      <div class="flex items-center gap-3 mb-4">
        <CheckCircle2 class="size-5 text-primary" />
        <h2 class="text-lg font-semibold">Selamat Datang, Wali Kelas!</h2>
      </div>
      <p class="text-sm text-muted-foreground mb-4">
        Berikut adalah ringkasan aktivitas keuangan terbaru dan tugas yang perlu Anda tangani hari ini.
      </p>
      <button class="bg-primary text-primary-foreground px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary/90 transition flex items-center gap-2">
        Lihat Tugas Hari Ini
        <ArrowRight class="size-4" />
      </button>
    </Card>  
  

    <div v-else-if="isSiswa" class="space-y-3">
      <!-- Top section with outstanding balance and next payment -->
      <div class="grid gap-4 grid-cols-1 md:grid-cols-[7fr_3fr]">
        <!-- Outstanding Balance Card -->
        <StatCard
          label="Total Outstanding Balance"
          value="Rp 4.250.000"
          sub="Sudah dibayar (65% Lunas)"
          :progress="65"
          variant="primary"
          :icon="Wallet"
        />

        <!-- Next Payment Due Card -->
        <Card class="p-6 flex flex-col justify-between shadow-sm border-border bg-card text-card-foreground rounded-xl">
          <div>
            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Next Payment Due</p>
            <h2 class="text-3xl font-bold text-foreground mt-3">15 Oktober 2023</h2>
          </div>
          <Button class="mt-6 w-full bg-primary text-primary-foreground hover:bg-primary/90 transition flex items-center justify-center gap-2 font-bold h-11">
            <CreditCard class="size-4" />
            Bayar Sekarang
          </Button>
        </Card>
      </div>

      <!-- Main content with Current Bills and Payment History -->
      <div class="grid gap-4 grid-cols-1 lg:grid-cols-[7fr_3fr]">
        <!-- Current Bills Section (70%) -->
        <Card class="p-6 shadow-sm border-border bg-card text-card-foreground rounded-xl">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">Current Bills</h2>
            <Button variant="ghost" size="sm" class="text-xs font-semibold text-muted-foreground hover:text-primary flex items-center gap-1.5">
              <Settings class="w-3.5 h-3.5" /> Filter
            </Button>
          </div>

          <div class="overflow-x-auto">
            <Table>
              <TableHeader class="bg-muted/50">
                <TableRow>
                  <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-center w-[50px]">No</TableHead>
                  <TableHead class="font-semibold text-xs uppercase text-muted-foreground">Deskripsi Tagihan</TableHead>
                  <TableHead class="font-semibold text-xs uppercase text-muted-foreground">Jatuh Tempo</TableHead>
                  <TableHead class="font-semibold text-xs uppercase text-muted-foreground">Jumlah</TableHead>
                  <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-right">Status</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow>
                  <TableCell class="text-center font-medium text-muted-foreground">1</TableCell>
                  <TableCell class="font-medium text-foreground">SPP Bulanan - Oktober</TableCell>
                  <TableCell class="text-muted-foreground">10 Okt 2023</TableCell>
                  <TableCell class="font-bold">Rp 1.500.000</TableCell>
                  <TableCell class="text-right">
                    <Badge variant="outline" class="text-red-500 bg-red-50 border-red-100 px-2 py-0.5 font-medium">Unpaid</Badge>
                  </TableCell>
                </TableRow>
                <TableRow>
                  <TableCell class="text-center font-medium text-muted-foreground">2</TableCell>
                  <TableCell class="font-medium text-foreground">Lab Fees (IPA & Komputer)</TableCell>
                  <TableCell class="text-muted-foreground">25 Sep 2023</TableCell>
                  <TableCell class="font-bold">Rp 750.000</TableCell>
                  <TableCell class="text-right">
                    <Badge variant="outline" class="text-amber-600 bg-amber-50 border-amber-100 px-2 py-0.5 font-medium">Partial</Badge>
                  </TableCell>
                </TableRow>
                <TableRow>
                  <TableCell class="text-center font-medium text-muted-foreground">3</TableCell>
                  <TableCell class="font-medium text-foreground">Field Trip ke Bandung</TableCell>
                  <TableCell class="text-muted-foreground">15 Sep 2023</TableCell>
                  <TableCell class="font-bold">Rp 2.000.000</TableCell>
                  <TableCell class="text-right">
                    <Badge variant="outline" class="text-emerald-600 bg-emerald-50 border-emerald-100 px-2 py-0.5 font-medium">Paid</Badge>
                  </TableCell>
                </TableRow>
                <TableRow>
                  <TableCell class="text-center font-medium text-muted-foreground">4</TableCell>
                  <TableCell class="font-medium text-foreground">Biaya Wisuda & Foto Album</TableCell>
                  <TableCell class="text-muted-foreground">01 Nov 2023</TableCell>
                  <TableCell class="font-bold">Rp 1.000.000</TableCell>
                  <TableCell class="text-right">
                    <Badge variant="outline" class="text-red-500 bg-red-50 border-red-100 px-2 py-0.5 font-medium">Unpaid</Badge>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </Card>

        <!-- Payment History Section (30%) -->
        <Card class="p-6 h-[30rem] flex flex-col shadow-sm border-border bg-card text-card-foreground rounded-xl">
          <div class="flex items-center justify-between gap-3 mb-4">
            <h2 class="text-lg font-semibold">Payment History</h2>
          </div>

          <div class="space-y-3 overflow-y-auto flex-1 pr-2">
            <div class="rounded-lg bg-muted/60 p-3 text-sm border border-border/50">
              <div class="flex items-center justify-between font-bold mb-1">
                <span>Pembayaran SPP Sept</span>
                <span class="text-primary font-bold">-Rp 1.500k</span>
              </div>
              <p class="text-xs text-muted-foreground font-medium">12 Sep 2023 • 14:30</p>
              <a href="#" class="text-xs text-primary hover:underline mt-2 flex items-center gap-1 font-semibold">
                <Download class="w-3.5 h-3.5" /> Receipt_120923.pdf
              </a>
            </div>

            <div class="rounded-lg bg-muted/60 p-3 text-sm border border-border/50">
              <div class="flex items-center justify-between font-bold mb-1">
                <span>Deposit Lab Fee</span>
                <span class="text-primary font-bold">-Rp 350k</span>
              </div>
              <p class="text-xs text-muted-foreground font-medium">05 Sep 2023 • 09:15</p>
              <a href="#" class="text-xs text-primary hover:underline mt-2 flex items-center gap-1 font-semibold">
                <Download class="w-3.5 h-3.5" /> Receipt_050923.pdf
              </a>
            </div>

            <div class="rounded-lg bg-muted/60 p-3 text-sm border border-border/50">
              <div class="flex items-center justify-between font-bold mb-1">
                <span>Seragam Olahraga</span>
                <span class="text-primary font-bold">-Rp 450k</span>
              </div>
              <p class="text-xs text-muted-foreground font-medium">28 Aug 2023 • 11:45</p>
              <a href="#" class="text-xs text-primary hover:underline mt-2 flex items-center gap-1 font-semibold">
                <Download class="w-3.5 h-3.5" /> Receipt_280823.pdf
              </a>
            </div>
          </div>

          <Button variant="outline" class="mt-auto w-full font-semibold">
            Lihat Semua Transaksi
          </Button>
        </Card>
      </div>

      <!-- FAQ Section -->
      <Card class="p-6 shadow-sm border-border bg-card text-card-foreground rounded-xl">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold">Butuh bantuan keuangan?</h2>
            <p class="text-sm text-muted-foreground mt-1">
              Hubungi tim bursar sekolah untuk rencana cicilan atau pertanyaan mengenai tagihan Anda.
            </p>
          </div>
          <div class="flex gap-3 w-full sm:w-auto">
            <Button variant="outline" class="flex-1 sm:flex-none font-semibold">
              Panduan Pembayaran
            </Button>
            <Button class="flex-1 sm:flex-none font-bold bg-primary text-primary-foreground hover:bg-primary/90">
              Hubungi CS
            </Button>
          </div>
        </div>
      </Card>
    </div>

    <!-- Full-width card below -->
    <Card v-if="isSiswa" class="p-6 mt-4 shadow-sm border-border bg-card text-card-foreground rounded-xl">
      <h2 class="text-lg font-semibold">Catatan Tambahan</h2>
      <p class="text-sm text-muted-foreground mt-2">
        Informasi tambahan ini tampil full-width di bawah kolom kiri dan kanan, cocok untuk ringkasan, pengumuman tagihan, atau catatan penting lainnya.
      </p>
    </Card>

    <div v-else-if="isOrangTua" class="space-y-6">
      <!-- Info Ringkas Siswa & Periode -->
      <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl bg-muted/40 border border-border/50">
        <div class="flex items-center gap-3">
          <div class="bg-primary/10 text-primary rounded-full p-2 flex items-center justify-center shrink-0">
            <CheckCircle2 class="size-5" />
          </div>
          <div>
            <p class="text-xs text-muted-foreground font-medium">Siswa</p>
            <p class="text-sm font-bold text-foreground">Lucas Henderson</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="bg-secondary text-secondary-foreground rounded-full p-2 flex items-center justify-center shrink-0">
            <CalendarDays class="size-5" />
          </div>
          <div>
            <p class="text-xs text-muted-foreground font-medium">Periode Tagihan</p>
            <p class="text-sm font-bold text-foreground">Oktober 2023</p>
          </div>
        </div>
      </div>

      <!-- Ringkasan Tagihan + Pembayaran -->
      <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
        <!-- Ringkasan Tagihan -->
        <Card class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-foreground">Ringkasan Tagihan</h2>
            <Button variant="ghost" size="icon" class="text-muted-foreground hover:text-primary">
              <ClipboardList class="size-5" />
            </Button>
          </div>

          <!-- Total Sisa Tagihan -->
          <div class="mb-6 pb-6 border-b">
            <p class="text-xs uppercase tracking-[0.2em] text-muted-foreground mb-2">Total Sisa Tagihan</p>
            <h3 class="text-4xl font-extrabold text-foreground">Rp 1.450.000</h3>
          </div>

          <!-- Tabel Ringkasan Tagihan -->
          <div class="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead class="w-[60px] text-center font-semibold text-xs uppercase text-muted-foreground">No</TableHead>
                  <TableHead class="font-semibold text-xs uppercase text-muted-foreground">Deskripsi Tagihan</TableHead>
                  <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-center">Keterangan</TableHead>
                  <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-right">Jumlah</TableHead>
                  <TableHead class="w-[80px] text-center font-semibold text-xs uppercase text-muted-foreground">Pilih</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <!-- SPP Bulanan -->
                <TableRow class="hover:bg-accent/30 cursor-pointer" @click="paySpp = !paySpp">
                  <TableCell class="text-center font-medium">1</TableCell>
                  <TableCell>
                    <div class="flex items-center gap-2">
                      <span class="text-lg">🎓</span>
                      <span class="font-medium text-foreground">SPP Bulanan</span>
                    </div>
                  </TableCell>
                  <TableCell class="text-center">
                    <Badge variant="destructive" class="bg-destructive/10 text-destructive border-destructive/20 font-medium">Wajib</Badge>
                  </TableCell>
                  <TableCell class="text-right font-semibold text-foreground">Rp 1.200.000</TableCell>
                  <TableCell>
                    <div class="flex items-center justify-center">
                      <Checkbox id="summary-spp" :checked="paySpp" @click.prevent />
                    </div>
                  </TableCell>
                </TableRow>

                <!-- Biaya Praktikum & Laboratorium -->
                <TableRow class="hover:bg-accent/30 cursor-pointer" @click="payLab = !payLab">
                  <TableCell class="text-center font-medium">2</TableCell>
                  <TableCell>
                    <div class="flex items-center gap-2">
                      <span class="text-lg">🔬</span>
                      <span class="font-medium text-foreground">Biaya Praktikum & Laboratorium</span>
                    </div>
                  </TableCell>
                  <TableCell class="text-center">
                    <Badge variant="destructive" class="bg-destructive/10 text-destructive border-destructive/20 font-medium">Wajib</Badge>
                  </TableCell>
                  <TableCell class="text-right font-semibold text-foreground">Rp 150.000</TableCell>
                  <TableCell>
                    <div class="flex items-center justify-center">
                      <Checkbox id="summary-lab" :checked="payLab" @click.prevent />
                    </div>
                  </TableCell>
                </TableRow>

                <!-- Kunjungan Lapangan (Field Trip) - Museum -->
                <TableRow class="hover:bg-accent/30 cursor-pointer" @click="payFieldTrip = !payFieldTrip">
                  <TableCell class="text-center font-medium">3</TableCell>
                  <TableCell>
                    <div class="flex items-center gap-2">
                      <span class="text-lg">🚌</span>
                      <span class="font-medium text-foreground">Kunjungan Lapangan (Field Trip) - Museum</span>
                    </div>
                  </TableCell>
                  <TableCell class="text-center">
                    <Badge variant="outline" class="bg-secondary text-secondary-foreground font-medium border-secondary/20">Add-on</Badge>
                  </TableCell>
                  <TableCell class="text-right font-semibold text-foreground">Rp 100.000</TableCell>
                  <TableCell>
                    <div class="flex items-center justify-center">
                      <Checkbox id="summary-trip" :checked="payFieldTrip" @click.prevent />
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <!-- Peringatan Pembayaran -->
          <div class="mt-6 rounded-lg bg-destructive/10 border border-destructive/20 p-4 flex gap-3 items-center">
            <AlertTriangle class="size-5 text-destructive shrink-0" />
            <p class="text-sm font-medium text-destructive">Batas pembayaran dalam 4 hari lagi (15 Oktober 2023)</p>
          </div>
        </Card>

        <!-- Bayar Sekarang -->
        <Card class="p-6">
          <h2 class="text-xl font-bold mb-6 text-foreground">Bayar Sekarang</h2>

          <!-- Pilihan Item yang Akan Dibayar (Daftar Dinamis) -->
          <div v-if="paySpp || payLab || payFieldTrip" class="space-y-3 mb-6 pb-6 border-b">
            <div v-if="paySpp" class="flex items-center justify-between bg-muted/40 px-3 py-2.5 rounded-lg border border-border/50 transition-all duration-200">
              <div class="flex items-center gap-2">
                <span class="text-base">🎓</span>
                <span class="font-medium text-sm text-foreground">Biaya SPP Sekolah</span>
              </div>
              <span class="font-semibold text-sm text-foreground">Rp 1.200.000</span>
            </div>

            <div v-if="payLab" class="flex items-center justify-between bg-muted/40 px-3 py-2.5 rounded-lg border border-border/50 transition-all duration-200">
              <div class="flex items-center gap-2">
                <span class="text-base">🔬</span>
                <span class="font-medium text-sm text-foreground">Biaya Lab & Praktikum</span>
              </div>
              <span class="font-semibold text-sm text-foreground">Rp 150.000</span>
            </div>

            <div v-if="payFieldTrip" class="flex items-center justify-between bg-muted/40 px-3 py-2.5 rounded-lg border border-border/50 transition-all duration-200">
              <div class="flex items-center gap-2">
                <span class="text-base">🚌</span>
                <span class="font-medium text-sm text-foreground">Kunjungan Lapangan</span>
              </div>
              <span class="font-semibold text-sm text-foreground">Rp 100.000</span>
            </div>
          </div>

          <!-- Pilihan Metode Pembayaran -->
          <div class="mb-6">
            <p class="text-sm font-semibold mb-3 text-foreground">Pilih Metode Pembayaran</p>
            <p class="text-xs text-muted-foreground mb-3 font-medium">Virtual Account (Transfer Bank)</p>
            
            <div class="grid grid-cols-2 gap-2 mb-4">
              <Button
                variant="outline"
                type="button"
                :class="[
                  'border-2 py-2 text-sm font-medium transition-all duration-200 justify-center h-10',
                  selectedPaymentMethod === 'bca'
                    ? 'border-primary bg-primary/10 text-primary hover:bg-primary/15'
                    : 'border-border text-muted-foreground hover:border-primary/50 hover:bg-accent'
                ]"
                @click="selectedPaymentMethod = 'bca'"
              >
                BCA
              </Button>
              <Button
                variant="outline"
                type="button"
                :class="[
                  'border-2 py-2 text-sm font-medium transition-all duration-200 justify-center h-10',
                  selectedPaymentMethod === 'mandiri'
                    ? 'border-primary bg-primary/10 text-primary hover:bg-primary/15'
                    : 'border-border text-muted-foreground hover:border-primary/50 hover:bg-accent'
                ]"
                @click="selectedPaymentMethod = 'mandiri'"
              >
                Mandiri
              </Button>
            </div>

            <p class="text-xs text-muted-foreground mb-3 font-medium">Dompet Digital (E-Wallet)</p>
            <div class="grid grid-cols-3 gap-2 mb-4">
              <Button
                variant="outline"
                type="button"
                :class="[
                  'border-2 py-2 text-xs font-medium transition-all duration-200 justify-center h-10',
                  selectedPaymentMethod === 'ovo'
                    ? 'border-primary bg-primary/10 text-primary hover:bg-primary/15'
                    : 'border-border text-muted-foreground hover:border-primary/50 hover:bg-accent'
                ]"
                @click="selectedPaymentMethod = 'ovo'"
              >
                OVO
              </Button>
              <Button
                variant="outline"
                type="button"
                :class="[
                  'border-2 py-2 text-xs font-medium transition-all duration-200 justify-center h-10',
                  selectedPaymentMethod === 'gopay'
                    ? 'border-primary bg-primary/10 text-primary hover:bg-primary/15'
                    : 'border-border text-muted-foreground hover:border-primary/50 hover:bg-accent'
                ]"
                @click="selectedPaymentMethod = 'gopay'"
              >
                GoPay
              </Button>
              <Button
                variant="outline"
                type="button"
                :class="[
                  'border-2 py-2 text-xs font-medium transition-all duration-200 justify-center h-10',
                  selectedPaymentMethod === 'dana'
                    ? 'border-primary bg-primary/10 text-primary hover:bg-primary/15'
                    : 'border-border text-muted-foreground hover:border-primary/50 hover:bg-accent'
                ]"
                @click="selectedPaymentMethod = 'dana'"
              >
                Dana
              </Button>
            </div>

            <Button
              variant="outline"
              type="button"
              :class="[
                'w-full border-2 py-3 text-sm font-medium flex items-center justify-between px-4 transition-all duration-200 h-12',
                selectedPaymentMethod === 'card'
                  ? 'border-primary bg-primary/10 text-primary hover:bg-primary/15'
                  : 'border-border text-muted-foreground hover:border-primary/50 hover:bg-accent'
              ]"
              @click="selectedPaymentMethod = 'card'"
            >
              <div class="flex items-center gap-2">
                <CreditCard class="size-4" />
                <span>Kartu Kredit / Debit</span>
              </div>
              <span class="text-lg">›</span>
            </Button>
          </div>

          <!-- Tombol Bayar -->
          <Button 
            class="w-full bg-primary text-primary-foreground hover:bg-primary/90 py-6 font-bold text-base transition-all duration-200 shadow-md"
            :disabled="totalPayment === 0"
          >
            <CreditCard class="size-5 mr-2" />
            Bayar Sekarang ({{ formatRupiah(totalPayment) }})
          </Button>
        </Card>
      </div>

      <!-- Riwayat Transaksi -->
      <Card class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-foreground">Riwayat Transaksi</h2>
          <Button variant="outline" size="sm" class="text-xs font-semibold text-muted-foreground hover:text-primary flex items-center gap-2">
            <Settings class="size-3.5" /> Filter Riwayat
          </Button>
        </div>

        <!-- Tabel Riwayat -->
        <div class="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead class="font-semibold text-xs uppercase tracking-wider text-muted-foreground">Tanggal</TableHead>
                <TableHead class="font-semibold text-xs uppercase tracking-wider text-muted-foreground">No. Referensi</TableHead>
                <TableHead class="font-semibold text-xs uppercase tracking-wider text-muted-foreground">Deskripsi</TableHead>
                <TableHead class="font-semibold text-xs uppercase tracking-wider text-muted-foreground">Jumlah</TableHead>
                <TableHead class="font-semibold text-xs uppercase tracking-wider text-muted-foreground">Status</TableHead>
                <TableHead class="font-semibold text-xs uppercase tracking-wider text-muted-foreground text-center">Kuitansi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <!-- Transaksi 1 -->
              <TableRow class="hover:bg-accent/50">
                <TableCell class="font-medium text-foreground">10 Sep 2023</TableCell>
                <TableCell class="font-semibold text-foreground">INV-882910</TableCell>
                <TableCell class="text-muted-foreground">SPP Agustus + Biaya Perpustakaan</TableCell>
                <TableCell class="font-bold text-foreground">Rp 1.245.000</TableCell>
                <TableCell>
                  <Badge class="bg-primary/10 text-primary border-primary/20 hover:bg-primary/10 font-bold">LUNAS</Badge>
                </TableCell>
                <TableCell class="text-center">
                  <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-primary">
                    <Download class="size-4" />
                  </Button>
                </TableCell>
              </TableRow>

              <!-- Transaksi 2 -->
              <TableRow class="hover:bg-accent/50">
                <TableCell class="font-medium text-foreground">12 Agt 2023</TableCell>
                <TableCell class="font-semibold text-foreground">INV-871104</TableCell>
                <TableCell class="text-muted-foreground">Paket Seragam & Buku Sekolah</TableCell>
                <TableCell class="font-bold text-foreground">Rp 320.000</TableCell>
                <TableCell>
                  <Badge class="bg-primary/10 text-primary border-primary/20 hover:bg-primary/10 font-bold">LUNAS</Badge>
                </TableCell>
                <TableCell class="text-center">
                  <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-primary">
                    <Download class="size-4" />
                  </Button>
                </TableCell>
              </TableRow>

              <!-- Transaksi 3 -->
              <TableRow class="hover:bg-accent/50">
                <TableCell class="font-medium text-foreground">15 Jul 2023</TableCell>
                <TableCell class="font-semibold text-foreground">INV-860022</TableCell>
                <TableCell class="text-muted-foreground">Biaya Pendaftaran (Tahunan)</TableCell>
                <TableCell class="font-bold text-foreground">Rp 500.000</TableCell>
                <TableCell>
                  <Badge class="bg-primary/10 text-primary border-primary/20 hover:bg-primary/10 font-bold">LUNAS</Badge>
                </TableCell>
                <TableCell class="text-center">
                  <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-primary">
                    <Download class="size-4" />
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Lihat Semua Riwayat -->
        <div class="mt-6 text-center">
          <Button variant="link" class="text-sm font-semibold text-primary hover:underline">
            Lihat Semua Riwayat
          </Button>
        </div>
      </Card>

      <!-- Informasi Keamanan -->
      <div class="flex items-center gap-3 text-xs text-muted-foreground px-4 py-3 bg-muted/50 rounded-lg">
        <ShieldCheck class="size-4 text-primary shrink-0" />
        <span>Semua transaksi dienkripsi dan diproses secara aman oleh Sistem Pembayaran Sekolah.</span>
        <div class="flex gap-4 ml-auto">
          <button class="text-primary hover:underline">Kebijakan Privasi</button>
          <button class="text-primary hover:underline">Syarat & Ketentuan</button>
        </div>
      </div>
    </div>


    <div v-else class="rounded-xl border bg-muted p-6">
      <p class="text-sm text-muted-foreground">
        Anda tidak memiliki akses ke halaman SPP. Silakan kembali ke dashboard atau hubungi administrator.
      </p>
    </div>
  </div>
</template>
