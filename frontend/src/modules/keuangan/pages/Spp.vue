<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRoute } from 'vue-router'
import { useRouter } from 'vue-router'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { NativeSelect } from '@/components/ui/native-select'
import { ClipboardList, CreditCard, CheckCircle2, ArrowRight, CalendarDays, Download, Printer, Search, Wallet, Banknote, Settings, FileText, Mail, RefreshCw, ShieldCheck, BookOpen, HelpCircle } from 'lucide-vue-next'
import CetakKwitansi from './CetakKwitansi.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'

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
      <!-- Page Title -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">Financial Dashboard</h1>
          <p class="text-sm text-muted-foreground mt-1">Manage tuition fees and educational expenses for Lucas Henderson.</p>
        </div>
        <div class="text-right">
          <p class="text-xs text-muted-foreground">Billing Period</p>
          <p class="text-lg font-semibold">October 2023</p>
        </div>
      </div>

      <!-- Bill Summary + Pay Now Section -->
      <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
        <!-- Bill Summary -->
        <Card class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Bill Summary</h2>
            <button class="text-muted-foreground hover:text-primary">📋</button>
          </div>

          <!-- Total Outstanding Balance -->
          <div class="mb-6 pb-6 border-b">
            <p class="text-xs uppercase tracking-[0.3em] text-muted-foreground mb-2">Total Outstanding Balance</p>
            <h3 class="text-4xl font-bold">$1,450.00</h3>
          </div>

          <!-- Bill Items -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-lg">🎓</span>
                <div>
                  <p class="font-medium text-sm">SPP (Monthly Tuition)</p>
                </div>
              </div>
              <span class="font-semibold">$1,200.00</span>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-lg">🔬</span>
                <div>
                  <p class="font-medium text-sm">Lab Fees (Science Dept)</p>
                </div>
              </div>
              <span class="font-semibold">$150.00</span>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-lg">🚌</span>
                <div>
                  <p class="font-medium text-sm">Field Trip - Museum</p>
                </div>
              </div>
              <span class="font-semibold">$100.00</span>
            </div>
          </div>

          <!-- Payment Alert -->
          <div class="mt-6 rounded-lg bg-destructive/10 border border-destructive/20 p-4 flex gap-3">
            <span class="text-lg">⚠️</span>
            <p class="text-sm font-medium text-destructive">Payment due in 4 days (October 15, 2023)</p>
          </div>
        </Card>

        <!-- Pay Now Section -->
        <Card class="p-6">
          <h2 class="text-xl font-bold mb-6">Pay Now</h2>

          <!-- Checkboxes -->
          <div class="space-y-3 mb-6 pb-6 border-b">
            <div class="flex items-center gap-3">
              <input type="checkbox" id="spp" checked class="w-5 h-5 rounded border-border" />
              <label for="spp" class="flex-1 flex items-center justify-between cursor-pointer">
                <span class="font-medium">SPP Tuition</span>
                <span class="font-semibold">$1,200.00</span>
              </label>
            </div>

            <div class="flex items-center gap-3">
              <input type="checkbox" id="lab" checked class="w-5 h-5 rounded border-border" />
              <label for="lab" class="flex-1 flex items-center justify-between cursor-pointer">
                <span class="font-medium">Lab & Activities</span>
                <span class="font-semibold">$250.00</span>
              </label>
            </div>
          </div>

          <!-- Payment Method Selection -->
          <div class="mb-6">
            <p class="text-sm font-semibold mb-3">Select Payment Method</p>
            <p class="text-xs text-muted-foreground mb-3">Virtual Account (Bank Transfer)</p>
            
            <div class="grid grid-cols-2 gap-2 mb-4">
              <button class="border-2 border-border rounded-lg py-2 text-sm font-medium hover:border-primary hover:bg-primary/5 transition">
                BCA
              </button>
              <button class="border-2 border-border rounded-lg py-2 text-sm font-medium hover:border-primary hover:bg-primary/5 transition">
                Mandiri
              </button>
            </div>

            <p class="text-xs text-muted-foreground mb-3">E-Wallets</p>
            <div class="grid grid-cols-3 gap-2 mb-4">
              <button class="border-2 border-border rounded-lg py-2 text-sm font-medium hover:border-primary hover:bg-primary/5 transition">
                OVO
              </button>
              <button class="border-2 border-border rounded-lg py-2 text-sm font-medium hover:border-primary hover:bg-primary/5 transition">
                GoPay
              </button>
              <button class="border-2 border-border rounded-lg py-2 text-sm font-medium hover:border-primary hover:bg-primary/5 transition">
                Dana
              </button>
            </div>

            <button class="w-full border-2 border-border rounded-lg py-3 text-sm font-medium flex items-center justify-between px-4 hover:border-primary hover:bg-primary/5 transition">
              <div class="flex items-center gap-2">
                <span>💳</span>
                <span>Credit / Debit Card</span>
              </div>
              <span>›</span>
            </button>
          </div>

          <!-- Complete Payment Button -->
          <Button class="w-full bg-primary text-primary-foreground hover:bg-primary/90 py-6 font-semibold text-base">
            Complete Payment ($1,450.00)
          </Button>
        </Card>
      </div>

      <!-- Transaction History Section -->
      <Card class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold">Transaction History</h2>
          <button class="text-xs font-medium text-muted-foreground hover:text-primary flex items-center gap-2">
            ⊕ Filter History
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b bg-muted/50">
                <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.2em] text-muted-foreground font-semibold">Date</th>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.2em] text-muted-foreground font-semibold">Reference No.</th>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.2em] text-muted-foreground font-semibold">Description</th>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.2em] text-muted-foreground font-semibold">Amount</th>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.2em] text-muted-foreground font-semibold">Status</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-[0.2em] text-muted-foreground font-semibold">Receipt</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <!-- Transaction 1 -->
              <tr class="hover:bg-accent">
                <td class="px-4 py-4">Sep 10, 2023</td>
                <td class="px-4 py-4 font-medium">INV-882910</td>
                <td class="px-4 py-4">August Tuition + Library Fee</td>
                <td class="px-4 py-4 font-semibold">$1,245.00</td>
                <td class="px-4 py-4">
                  <span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">PAID</span>
                </td>
                <td class="px-4 py-4 text-center">
                  <button class="text-muted-foreground hover:text-primary">👤</button>
                </td>
              </tr>

              <!-- Transaction 2 -->
              <tr class="hover:bg-accent">
                <td class="px-4 py-4">Aug 12, 2023</td>
                <td class="px-4 py-4 font-medium">INV-871104</td>
                <td class="px-4 py-4">Uniform & Books Package</td>
                <td class="px-4 py-4 font-semibold">$320.00</td>
                <td class="px-4 py-4">
                  <span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">PAID</span>
                </td>
                <td class="px-4 py-4 text-center">
                  <button class="text-muted-foreground hover:text-primary">👤</button>
                </td>
              </tr>

              <!-- Transaction 3 -->
              <tr class="hover:bg-accent">
                <td class="px-4 py-4">Jul 15, 2023</td>
                <td class="px-4 py-4 font-medium">INV-860022</td>
                <td class="px-4 py-4">Registration Fee (Annual)</td>
                <td class="px-4 py-4 font-semibold">$500.00</td>
                <td class="px-4 py-4">
                  <span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">PAID</span>
                </td>
                <td class="px-4 py-4 text-center">
                  <button class="text-muted-foreground hover:text-primary">👤</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- View All History -->
        <div class="mt-6 text-center">
          <button class="text-sm font-medium text-foreground hover:text-primary">
            View All History
          </button>
        </div>
      </Card>

      <!-- Security Notice -->
      <div class="flex items-center gap-3 text-xs text-muted-foreground px-4 py-3 bg-muted/50 rounded-lg">
        <span>🔒</span>
        <span>All transactions are encrypted and processed securely by EduFinance Institutional Ledger.</span>
        <div class="flex gap-4 ml-auto">
          <button class="text-primary hover:underline">Privacy Policy</button>
          <button class="text-primary hover:underline">Terms of Service</button>
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
