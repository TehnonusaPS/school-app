<script setup lang="ts">
import {
  AlertCircle,
  CheckCircle2,
  ChevronRight,
  PlusCircle,
  Settings
} from 'lucide-vue-next'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle
} from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { ScrollArea } from '@/components/ui/scroll-area'

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
  <Card class="lg:col-span-2 flex flex-col">
    <CardHeader>
      <CardTitle class="text-base font-semibold">Aktivitas Sistem</CardTitle>
      <CardDescription class="text-xs">
        Log aktivitas terbaru dari seluruh pengguna
      </CardDescription>
    </CardHeader>

    <CardContent class="flex-1 p-0 relative z-10">
      <ScrollArea class="h-[360px] px-6">
        <div class="space-y-1 py-1">
          <div
            v-for="activity in activities"
            :key="activity.id"
            class="group flex items-start gap-3 rounded-xl p-3 transition-all hover:bg-white/5 dark:hover:bg-white/5 cursor-default border border-transparent hover:border-white/10 hover:shadow-lg"
          >
            <!-- Avatar -->
            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/20 backdrop-blur-md border border-primary/20 text-[10px] font-bold text-primary drop-shadow-sm">
              {{ activity.initials }}
            </div>
            <!-- Content -->
            <div class="min-w-0 flex-1">
              <div class="flex items-center justify-between gap-2">
                <p class="truncate text-sm font-medium">{{ activity.title }}</p>
                <component
                  :is="activity.icon"
                  :class="['size-3.5 shrink-0 drop-shadow-sm', activityTypeClass(activity.type)]"
                />
              </div>
              <p class="mt-0.5 truncate text-xs text-muted-foreground">{{ activity.desc }}</p>
              <p class="mt-1 text-[10px] text-muted-foreground/70">{{ activity.time }}</p>
            </div>
          </div>
        </div>
      </ScrollArea>
    </CardContent>

    <CardFooter class="border-t border-white/10 pt-4 relative z-10">
      <Button variant="ghost" class="w-full gap-1.5 text-xs h-8 text-muted-foreground hover:text-foreground">
        Lihat semua log
        <ChevronRight class="size-3.5" />
      </Button>
    </CardFooter>
  </Card>
</template>
