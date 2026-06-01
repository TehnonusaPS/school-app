<script setup>
import { defineProps } from 'vue'
import { Wallet, Users, LayoutGrid, School, UserCheck, CalendarDays, Activity, MessageCircle } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import WidgetList from '@/components/dashboard-widget/WidgetList.vue'
import WidgetProgressList from '@/components/dashboard-widget/WidgetProgressList.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

const props = defineProps({
  searchQuery: {
    type: String,
    default: ''
  }
})

const match = keywords => {
  if (!props.searchQuery) return true
  return keywords.toLowerCase().includes(props.searchQuery.toLowerCase())
}

const dummyListItems = [
  { id: 1, name: 'Bapak Udin', info: 'Masuk tepat waktu', time: '07:05' },
  { id: 2, name: 'Ibu Sari', info: 'Masuk tepat waktu', time: '07:12' },
  { id: 3, name: 'Bapak Supri', info: 'Terlambat 15 menit', time: '07:45' }
]

const dummyProgressItems = [
  { id: 1, label: 'Kelas 10-A', value: 95 },
  { id: 2, label: 'Kelas 11-B', value: 88 },
  { id: 3, label: 'Kelas 12-A', value: 92 }
]
</script>

<template>
  <div class="space-y-12">
    <!-- 61. STAT CARD -->
    <section v-show="match('stat card statistik dashboard progress trend')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">61. Stat Card</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen kartu statistik responsif dengan desain Glassmorphism. Tersedia dalam berbagai varian warna dan mendukung indikator trend maupun progress bar.
        </p>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 p-6 border rounded-xl bg-card">
        <!-- Variant Blue -->
        <StatCard label="Total Siswa" value="402"
          sub="siswa aktif" trend="+12 bulan ini"
          trendDirection="up" :icon="Users" variant="blue" />

        <!-- Variant Violet -->
        <StatCard label="Total Kelas" value="18"
          sub="rombel aktif" trend="Semester Ganjil"
          trendDirection="neutral" :icon="LayoutGrid" variant="violet" />

        <!-- Variant Emerald -->
        <StatCard label="Absensi Hari Ini" value="378"
          sub="94% hadir" trend="+2% dari kemarin"
          trendDirection="up" :icon="UserCheck" variant="emerald" />

        <!-- Variant Amber -->
        <StatCard label="SPP Terkumpul" value="82.2%"
          sub="Target Rp 2,4M" trend="+5.1% bulan ini"
          trendDirection="up" :icon="Wallet" variant="amber" />

        <!-- Variant Primary -->
        <StatCard label="Alpa Hari Ini" value="0"
          sub="tidak hadir tanpa ket." trendDirection="neutral"
          variant="primary" />

        <!-- Variant Progress -->
        <StatCard label="Total Anggaran" value="Rp 1,2M"
          sub="Realisasi Anggaran" :progress="78.5"
          :icon="Wallet" variant="emerald" />
      </div>
    </section>

    <!-- 62. WIDGET CARD -->
    <section v-show="match('widget card container dashboard wrapper')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">62. Widget Card</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Container generik untuk widget dashboard. Memiliki title, description, icon bawaan, dan slots untuk konten utama, header actions, dan footer.
        </p>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 p-6 border rounded-xl bg-card">
        <WidgetCard
          title="Overview Akademik"
          description="Ringkasan aktivitas hari ini"
          :icon="Activity"
        >
          <template #header-action>
            <Badge variant="outline">Hari Ini</Badge>
          </template>
          
          <div class="h-[120px] flex items-center justify-center text-sm text-muted-foreground bg-muted/20 rounded-lg border border-dashed">
            Konten Custom Widget
          </div>
          
          <template #footer>
            <Button variant="ghost" class="w-full text-xs h-8">Lihat Detail Lengkap</Button>
          </template>
        </WidgetCard>
      </div>
    </section>

    <!-- 63. WIDGET LIST -->
    <section v-show="match('widget list daftar scrollable agenda pesan')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">63. Widget List</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Widget untuk menampilkan daftar item (list) dengan area yang dapat di-scroll. Menggunakan scoped slot `#item` untuk custom layout baris.
        </p>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 p-6 border rounded-xl bg-card">
        <WidgetList
          title="Log Aktivitas"
          description="Aktivitas terbaru sistem"
          :icon="MessageCircle"
          :items="dummyListItems"
          listClass="h-[180px] px-4"
        >
          <template #item="{ item }">
            <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
              <UserCheck class="size-4 text-primary" />
            </div>
            <div class="min-w-0 flex-1 ml-2">
              <p class="text-sm font-semibold truncate">{{ item.name }}</p>
              <p class="text-xs text-muted-foreground mt-0.5">{{ item.info }}</p>
            </div>
            <div class="text-xs font-medium tabular-nums">{{ item.time }}</div>
          </template>
        </WidgetList>
      </div>
    </section>

    <!-- 64. WIDGET PROGRESS LIST -->
    <section v-show="match('widget progress list bar ranking indikator')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">64. Widget Progress List</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Widget untuk menampilkan daftar progres dengan progress bar. Cocok untuk ranking, tingkat kehadiran, atau penggunaan kapasitas.
        </p>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 p-6 border rounded-xl bg-card">
        <WidgetProgressList
          title="Tingkat Kehadiran Kelas"
          description="Persentase kehadiran per kelas bulan ini"
          :icon="Users"
          :items="dummyProgressItems"
          listClass="h-[180px] px-4"
        >
          <template #value="{ item }">
            <span class="text-xs font-bold tabular-nums">{{ item.value }}%</span>
          </template>
        </WidgetProgressList>
      </div>
    </section>

  </div>
</template>
