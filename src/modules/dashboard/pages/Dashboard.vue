<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart'
import {
  Building2,
  School,
  GraduationCap,
  Users,
  TrendingUp,
  ArrowUpRight,
  CheckCircle2,
  PlusCircle,
  AlertCircle,
  Settings,
  ChevronRight
} from 'lucide-vue-next'
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardDescription,
  CardFooter
} from '@/components/ui/card'

import { Button } from '@/components/ui/button'
import { ScrollArea } from '@/components/ui/scroll-area'
import {
  ChartContainer,
  ChartCrosshair,
  ChartTooltip,
  ChartTooltipContent,
  componentToString
} from '@/components/ui/chart'
import { VisXYContainer, VisGroupedBar, VisAxis } from '@unovis/vue'

// ── Chart ─────────────────────────────────────────────────────────
const chartData = [
  { bulan: 'Jan', sekolah: 12 },
  { bulan: 'Feb', sekolah: 18 },
  { bulan: 'Mar', sekolah: 14 },
  { bulan: 'Apr', sekolah: 22 },
  { bulan: 'Mei', sekolah: 28 },
  { bulan: 'Jun', sekolah: 24 },
  { bulan: 'Jul', sekolah: 32 },
  { bulan: 'Agt', sekolah: 38 },
  { bulan: 'Sep', sekolah: 35 },
  { bulan: 'Okt', sekolah: 45 },
  { bulan: 'Nov', sekolah: 42 },
  { bulan: 'Des', sekolah: 52 }
]

const chartConfig = {
  sekolah: {
    label: 'Sekolah Baru',
    color: 'var(--primary)'
  }
} satisfies ChartConfig

// ── Stat Cards ────────────────────────────────────────────────────
const stats = [
  {
    label: 'Total Yayasan',
    value: '120',
    sub: '+5 bulan ini',
    trend: '+4.3%',
    icon: Building2,
    color: 'text-blue-500',
    bg: 'bg-blue-500/10'
  },
  {
    label: 'Total Sekolah',
    value: '270',
    sub: '+12 bulan ini',
    trend: '+4.6%',
    icon: School,
    color: 'text-violet-500',
    bg: 'bg-violet-500/10'
  },
  {
    label: 'Total Guru',
    value: '2.930',
    sub: '+48 bulan ini',
    trend: '+1.7%',
    icon: GraduationCap,
    color: 'text-emerald-500',
    bg: 'bg-emerald-500/10'
  },
  {
    label: 'Total Siswa',
    value: '1.239K',
    sub: '+320 bulan ini',
    trend: '+2.1%',
    icon: Users,
    color: 'text-amber-500',
    bg: 'bg-amber-500/10'
  }
]

// ── Activity Log ──────────────────────────────────────────────────
const activities = [
  {
    id: 1,
    title: 'Yayasan Al-Hidayah',
    desc: 'Menambahkan entitas sekolah baru: SDN 12 Sentosa',
    time: '2 menit lalu',
    type: 'success',
    icon: PlusCircle,
    initials: 'AH'
  },
  {
    id: 2,
    title: 'Admin Sekolah SMPN 5',
    desc: 'Memperbarui data guru: Budi Santoso, S.Pd',
    time: '15 menit lalu',
    type: 'info',
    icon: Settings,
    initials: 'AS'
  },
  {
    id: 3,
    title: 'Superadmin',
    desc: 'Mengaktifkan akun yayasan: Yayasan Cerdas Nusantara',
    time: '1 jam lalu',
    type: 'success',
    icon: CheckCircle2,
    initials: 'SA'
  },
  {
    id: 4,
    title: 'Yayasan Bina Insani',
    desc: 'Mengajukan permintaan penambahan sekolah baru',
    time: '3 jam lalu',
    type: 'warning',
    icon: AlertCircle,
    initials: 'BI'
  },
  {
    id: 5,
    title: 'Admin Sistem',
    desc: 'Backup database mingguan berhasil diselesaikan',
    time: 'Kemarin, 23:00',
    type: 'success',
    icon: CheckCircle2,
    initials: 'DB'
  }
]

const activityTypeClass = (type: string) =>
  ({
    success: 'text-emerald-500',
    info: 'text-blue-500',
    warning: 'text-amber-500',
    error: 'text-rose-500'
  })[type] ?? 'text-muted-foreground'
</script>

<template>
  <div class="space-y-6">
    <!-- ── Stat Cards ── -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <Card
        v-for="(stat, i) in stats"
        :key="i"
        class="transition-all duration-200 hover:shadow-md hover:-translate-y-0.5"
      >
        <CardHeader class="pb-2">
          <div class="flex items-center justify-between">
            <CardDescription class="text-sm font-medium">{{ stat.label }}</CardDescription>
            <div :class="['rounded-lg p-2', stat.bg]">
              <component :is="stat.icon" :class="['size-4', stat.color]" />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <div class="text-3xl font-bold tracking-tight">{{ stat.value }}</div>
          <div class="mt-1 flex items-center gap-1.5">
            <span class="flex items-center gap-0.5 text-xs font-semibold text-emerald-500">
              <TrendingUp class="size-3" />
              {{ stat.trend }}
            </span>
            <span class="text-xs text-muted-foreground">{{ stat.sub }}</span>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- ── Chart + Activity ── -->
    <div class="grid gap-4 lg:grid-cols-5">

      <!-- Bar Chart -->
      <Card class="lg:col-span-3">
        <CardHeader>
          <div class="flex items-start justify-between">
            <div>
              <CardTitle class="text-base font-semibold">Tren Pertumbuhan Sekolah Baru</CardTitle>
              <CardDescription class="mt-0.5 text-xs">
                Jumlah sekolah terdaftar per bulan — 2025
              </CardDescription>
            </div>
            <Badge variant="secondary" class="gap-1 text-xs">
              <ArrowUpRight class="size-3 text-emerald-500" />
              +18.4%
            </Badge>
          </div>
        </CardHeader>

        <CardContent>
          <ChartContainer :config="chartConfig" class="h-[320px] w-full bar-chart-container">
            <VisXYContainer
              :data="chartData"
              :x-domain="[-0.5, chartData.length - 0.5]"
              :margin="{ left: -24 }"
            >
              <VisGroupedBar
                :x="(_d: any, i: number) => i"
                :y="[(d: any) => d.sekolah]"
                :color="['var(--color-sekolah)']"
                :rounded-corners="4"
              />
              <VisAxis
                type="x"
                :tick-format="(i: number) => chartData[i]?.bulan"
                :tick-values="chartData.map((_d, i) => i)"
                :grid-line="false"
                :tick-line="false"
                :domain-line="false"
              />
              <VisAxis
                type="y"
                :num-ticks="4"
                :tick-line="false"
                :domain-line="false"
                :grid-line="true"
              />
              <ChartTooltip />
              <ChartCrosshair
                :template="componentToString(chartConfig, ChartTooltipContent, {
                  labelFormatter: (x: number) => chartData[Math.round(x)]?.bulan || ''
                })"
                :color="['var(--color-sekolah)']"
                :hide-when-far-from-pointer="false"
                :skip-range-check="true"
              />
            </VisXYContainer>
          </ChartContainer>
        </CardContent>

        <CardFooter class="flex-col items-start gap-2 text-sm border-t pt-4">
          <div class="flex gap-2 font-medium leading-none">
            Naik 18.4% dibanding bulan lalu <TrendingUp class="h-4 w-4 text-emerald-500" />
          </div>
          <div class="leading-none text-muted-foreground text-xs">
            Menampilkan total sekolah baru selama 12 bulan terakhir
          </div>
        </CardFooter>
      </Card>

      <!-- Activity Log -->
      <Card class="lg:col-span-2 flex flex-col">
        <CardHeader>
          <CardTitle class="text-base font-semibold">Aktivitas Sistem</CardTitle>
          <CardDescription class="text-xs">
            Log aktivitas terbaru dari seluruh pengguna
          </CardDescription>
        </CardHeader>

        <CardContent class="flex-1 p-0">
          <ScrollArea class="h-[360px] px-6">
            <div class="space-y-1 py-1">
              <div
                v-for="activity in activities"
                :key="activity.id"
                class="group flex items-start gap-3 rounded-lg p-2.5 transition-colors hover:bg-muted/50 cursor-default"
              >
                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted text-[10px] font-bold text-muted-foreground">
                  {{ activity.initials }}
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex items-center justify-between gap-2">
                    <p class="truncate text-sm font-medium">{{ activity.title }}</p>
                    <component
                      :is="activity.icon"
                      :class="['size-3.5 shrink-0', activityTypeClass(activity.type)]"
                    />
                  </div>
                  <p class="mt-0.5 truncate text-xs text-muted-foreground">{{ activity.desc }}</p>
                  <p class="mt-1 text-[10px] text-muted-foreground/70">{{ activity.time }}</p>
                </div>
              </div>
            </div>
          </ScrollArea>
        </CardContent>

        <CardFooter class="border-t pt-4">
          <Button variant="ghost" class="w-full gap-1.5 text-xs h-8 text-muted-foreground hover:text-foreground">
            Lihat semua log
            <ChevronRight class="size-3.5" />
          </Button>
        </CardFooter>
      </Card>

    </div>
  </div>
</template>

<style scoped>
/* Crosshair bar highlight — sama persis seperti ShowcasePart2 */
:deep(.bar-chart-container) {
  --vis-crosshair-line-stroke-width: 56px;
  --vis-crosshair-line-stroke-color: var(--muted);
  --vis-crosshair-line-stroke-opacity: 0.65;
}
:deep(.bar-chart-container circle) {
  display: none !important;
}
</style>
