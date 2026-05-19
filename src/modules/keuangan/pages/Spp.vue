<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRoute } from 'vue-router'
import { ClipboardList, CreditCard, CheckCircle2 } from 'lucide-vue-next'

const auth = useAuthStore()
const route = useRoute()

const currentRole = computed(() => auth.user?.role || 'guest')
const isAdmin = computed(() => currentRole.value === 'admin')
const isSiswa = computed(() => currentRole.value === 'siswa')

const description = computed(() => route.meta.description || 'Selamat datang di halaman ini.')
</script>

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="border-b pb-4">
      <div class="flex flex-col gap-2">
        <div class="flex items-center gap-3">
          <h1 class="text-2xl font-bold tracking-tight">Manajemen Keuangan Siswa</h1>
          <span
            class="rounded-full bg-primary/10 text-primary px-3 py-1 text-xs font-semibold uppercase"
          >
            {{ currentRole }}
          </span>
        </div>
        <p class="text-muted-foreground">{{ description }}</p>
      </div>
    </div>

    <div v-if="isAdmin" class="grid gap-4">
      <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border bg-card p-6">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-muted-foreground">Total Tagihan</span>
            <CreditCard class="size-4 text-primary" />
          </div>
          <div class="text-3xl font-bold">128</div>
          <p class="text-xs text-muted-foreground mt-1">Tagihan SPP aktif</p>
        </div>
        <div class="rounded-xl border bg-card p-6">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-muted-foreground">Belum Lunas</span>
            <ClipboardList class="size-4 text-amber-500" />
          </div>
          <div class="text-3xl font-bold">34</div>
          <p class="text-xs text-muted-foreground mt-1">Siswa dengan tagihan tertunda</p>
        </div>
        <div class="rounded-xl border bg-card p-6">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-muted-foreground">Sudah Lunas</span>
            <CheckCircle2 class="size-4 text-emerald-500" />
          </div>
          <div class="text-3xl font-bold">94</div>
          <p class="text-xs text-muted-foreground mt-1">Pembayaran SPP lengkap</p>
        </div>
      </div>

      <div class="rounded-xl border bg-card p-6">
        <h2 class="text-lg font-semibold">Aksi Admin</h2>
        <p class="text-sm text-muted-foreground mt-2">
          Anda dapat mengelola daftar tagihan, memperbarui status pembayaran, dan meninjau laporan SPP siswa.
        </p>
      </div>
    </div>

    <div v-else-if="isSiswa" class="flex flex-col md:flex-row gap-4">
      <!-- Left column (70%) -->
      <div class="w-full md:w-[70%] space-y-4">
        <div class="rounded-xl border bg-card p-6 h-40">
          <!-- <h2 class="text-lg font-semibold">Status Pembayaran SPP Anda</h2>
          <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl border bg-slate-50 p-4">
              <p class="text-xs text-muted-foreground uppercase">Bulan</p>
              <p class="text-xl font-bold">Mei</p>
            </div>
            <div class="rounded-xl border bg-slate-50 p-4">
              <p class="text-xs text-muted-foreground uppercase">Jumlah</p>
              <p class="text-xl font-bold">Rp 300.000</p>
            </div>
            <div class="rounded-xl border bg-slate-50 p-4">
              <p class="text-xs text-muted-foreground uppercase">Status</p>
              <p class="text-xl font-bold text-emerald-600">Lunas</p>
            </div>
          </div> -->
        </div>

        <div class="rounded-xl border bg-card p-6 h-56">
          <!-- <h2 class="text-lg font-semibold">Ringkasan</h2>
          <p class="text-sm text-muted-foreground mt-2">
            Halaman ini menampilkan ringkasan biaya SPP Anda. Untuk detail dan histori pembayaran, gunakan menu Tagihan & Pembayaran.
          </p> -->
        </div>
      </div>

      <!-- Right column (30%) -->
      <div class="w-full md:w-[30%] space-y-4">
        <div class="rounded-xl border bg-card p-6 h-40">
          <!-- <h2 class="text-lg font-semibold">Detail Singkat</h2> -->
          <!-- <div class="mt-4 grid gap-3 sm:grid-cols-1">
            <div class="rounded-xl border bg-slate-50 p-4">
              <p class="text-xs text-muted-foreground uppercase">Bulan</p>
              <p class="text-xl font-bold">Mei</p>
            </div>
            <div class="rounded-xl border bg-slate-50 p-4">
              <p class="text-xs text-muted-foreground uppercase">Jumlah</p>
              <p class="text-xl font-bold">Rp 300.000</p>
            </div>
            <div class="rounded-xl border bg-slate-50 p-4">
              <p class="text-xs text-muted-foreground uppercase">Status</p>
              <p class="text-xl font-bold text-emerald-600">Lunas</p>
            </div>
          </div> -->
        </div>

        <div class="rounded-xl border bg-card p-6 h-56">
          <h2 class="text-lg font-semibold">Informasi Pembayaran</h2>
          <p class="text-sm text-muted-foreground mt-2">
            Pastikan untuk melakukan pembayaran tepat waktu setiap bulan agar terhindar dari denda keterlambatan. Hubungi bagian keuangan jika Anda memiliki pertanyaan tentang tagihan Anda.
          </p> 
        </div>
      </div>
    </div>

    <!-- Full-width card below -->
    <div v-if="isSiswa" class="rounded-xl border bg-card p-6 mt-4">
      <h2 class="text-lg font-semibold">Catatan Tambahan</h2>
      <p class="text-sm text-muted-foreground mt-2">
        Informasi tambahan ini tampil full-width di bawah kolom kiri dan kanan, cocok untuk ringkasan, pengumuman tagihan, atau catatan penting lainnya.
      </p>
    </div>


    <div v-else class="rounded-xl border bg-muted p-6">
      <p class="text-sm text-muted-foreground">
        Anda tidak memiliki akses ke halaman SPP. Silakan kembali ke dashboard atau hubungi administrator.
      </p>
    </div>
  </div>
</template>
