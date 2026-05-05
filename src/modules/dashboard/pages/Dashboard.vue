<template>
  <div class="min-h-screen w-full bg-gradient-to-br from-slate-100 to-blue-200 p-6 font-sans">
    
    <!-- Top Header / Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div v-for="stats in summaryStats" :key="stats.label" 
           class="rounded-2xl border border-white/40 bg-white/30 p-6 shadow-xl backdrop-blur-md transition-transform hover:scale-[1.02]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-600">{{ stats.label }}</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ stats.value }}</h3>
          </div>
          <div :class="`flex h-12 w-12 items-center justify-center rounded-xl ${stats.bgIcon} text-white shadow-lg`">
            <component :is="stats.icon" class="h-6 w-6" />
          </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
          <span :class="stats.trendUp ? 'text-emerald-600' : 'text-rose-600'" class="font-bold flex items-center">
            {{ stats.trend }}
            <component :is="stats.trendUp ? TrendingUp : TrendingDown" class="ml-1 h-4 w-4" />
          </span>
          <span class="ml-2 text-slate-500 text-xs">vs bulan lalu</span>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Chart Area (Left & Center) -->
      <div class="lg:col-span-2 space-y-8">
        <div class="rounded-3xl border border-white/40 bg-white/40 p-8 shadow-2xl backdrop-blur-xl">
          <div class="flex items-center justify-between mb-8">
            <div>
              <h2 class="text-xl font-bold text-slate-900 leading-none">Analisis Performa Operasi</h2>
              <p class="text-sm text-slate-500 mt-1">Data statistik harian waktu nyata</p>
            </div>
            <select class="rounded-lg border-none bg-white/50 px-4 py-2 text-sm font-medium text-blue-900 outline-none focus:ring-2 focus:ring-blue-900/20">
              <option>7 Hari Terakhir</option>
              <option>30 Hari Terakhir</option>
            </select>
          </div>
          
          <div class="h-[400px]">
            <canvas id="mainChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Recent Activities / Table (Right) -->
      <div class="lg:col-span-1">
        <div class="h-full rounded-3xl border border-white/40 bg-white/40 p-8 shadow-2xl backdrop-blur-xl">
          <h2 class="text-xl font-bold text-slate-900 mb-6">Log Aktivitas Terbaru</h2>
          <div class="space-y-6">
            <div v-for="log in recentLogs" :key="log.id" class="flex gap-4 group cursor-pointer">
              <div class="relative">
                <div class="h-10 w-10 rounded-full border-2 border-white bg-blue-900 flex items-center justify-center text-white z-10 relative">
                  <component :is="log.icon" class="h-4 w-4" />
                </div>
                <div v-if="log.id !== recentLogs.length" class="absolute left-1/2 top-10 h-10 w-[2px] -translate-x-1/2 bg-blue-900/10 group-hover:bg-blue-900/30"></div>
              </div>
              <div class="flex-1 pb-6">
                <p class="text-sm font-bold text-slate-900">{{ log.title }}</p>
                <p class="text-xs text-slate-500 mb-1">{{ log.time }}</p>
                <p class="text-sm text-slate-600 line-clamp-1">{{ log.desc }}</p>
              </div>
            </div>
          </div>
          <button class="w-full mt-4 rounded-xl border-2 border-blue-900/20 py-3 text-sm font-bold text-blue-900 transition-all hover:bg-blue-900 hover:text-white">
            Lihat Semua Laporan
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue'
import Chart from 'chart.js/auto'
import { 
  Users, BarChart3, Activity, ShieldCheck, 
  TrendingUp, TrendingDown, Clock, Bell, CheckCircle2 
} from 'lucide-vue-next'

const summaryStats = [
  { label: 'Total Pengguna', value: '12,840', icon: Users, bgIcon: 'bg-blue-900', trend: '+12.5%', trendUp: true },
  { label: 'Sesi Aktif', value: '458', icon: Activity, bgIcon: 'bg-indigo-600', trend: '-2.4%', trendUp: false },
  { label: 'Efisiensi Ops', value: '94.2%', icon: BarChart3, bgIcon: 'bg-slate-800', trend: '+4.1%', trendUp: true },
  { label: 'Status Keamanan', value: 'Aman', icon: ShieldCheck, bgIcon: 'bg-cyan-700', trend: 'Stabil', trendUp: true },
]

const recentLogs = [
  { id: 1, title: 'Sistem Terupdate', time: '5 menit yang lalu', desc: 'Versi v2.4.0 berhasil diunggah ke server utama.', icon: CheckCircle2 },
  { id: 2, title: 'Peringatan Kapasitas', time: '1 jam yang lalu', desc: 'Storage server pusat mencapai ambang batas 85%.', icon: Bell },
  { id: 3, title: 'Audit Keamanan', time: '4 jam yang lalu', desc: 'Pemindaian rutin mingguan selesai tanpa ancaman.', icon: ShieldCheck },
  { id: 4, title: 'Backup Berhasil', time: 'Kemarin, 23:00', desc: 'Data berhasil diamankan ke cloud storage.', icon: Clock },
]

onMounted(() => {
  const ctx = document.getElementById('mainChart').getContext('2d')
  
  // Gradient for chart
  const gradient = ctx.createLinearGradient(0, 0, 0, 400)
  gradient.addColorStop(0, 'rgba(30, 58, 138, 0.4)')
  gradient.addColorStop(1, 'rgba(30, 58, 138, 0.0)')

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
      datasets: [{
        label: 'Aktivitas Operasi',
        data: [65, 78, 72, 90, 85, 110, 105],
        borderColor: '#1e3a8a',
        backgroundColor: gradient,
        fill: true,
        tension: 0.4,
        borderWidth: 3,
        pointBackgroundColor: '#fff',
        pointBorderColor: '#1e3a8a',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0, 0, 0, 0.05)' },
          border: { display: false }
        },
        x: {
          grid: { display: false },
          border: { display: false }
        }
      }
    }
  })
})
</script>

<style scoped>
/* Transisi halus */
.transition-transform {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
</style>