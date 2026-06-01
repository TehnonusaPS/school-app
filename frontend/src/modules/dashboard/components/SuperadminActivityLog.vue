<script setup lang="ts">
import { AlertCircle, CheckCircle2, PlusCircle, Settings } from 'lucide-vue-next'
import WidgetList from '@/components/dashboard-widget/WidgetList.vue'
import { activitiesData } from '../data/superadminActivityLogData'

const getInitials = (title: string) => {
  return title.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase()
}

const getActivityConfig = (type: string) => {
  switch (type) {
    case 'add': return { icon: PlusCircle, color: 'text-emerald-500' }
    case 'update': return { icon: Settings, color: 'text-blue-500' }
    case 'success': return { icon: CheckCircle2, color: 'text-emerald-500' }
    case 'warning': return { icon: AlertCircle, color: 'text-amber-500' }
    default: return { icon: CheckCircle2, color: 'text-muted-foreground' }
  }
}
</script>

<template>
  <WidgetList
    title="Aktivitas Sistem"
    description="Log aktivitas terbaru dari seluruh pengguna"
    footerText="Lihat semua log"
    :items="activitiesData"
    cardClass="lg:col-span-2"
  >
    <template #item="{ item }">
      <!-- Avatar -->
      <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/20 backdrop-blur-md border border-primary/20 text-[10px] font-bold text-primary drop-shadow-sm">
        {{ getInitials(item.title) }}
      </div>
      <!-- Content -->
      <div class="min-w-0 flex-1">
        <div class="flex items-center justify-between gap-2">
          <p class="truncate text-sm font-medium">{{ item.title }}</p>
          <component
            :is="getActivityConfig(item.type).icon"
            :class="['size-3.5 shrink-0 drop-shadow-sm', getActivityConfig(item.type).color]"
          />
        </div>
        <p class="mt-0.5 truncate text-xs text-muted-foreground">{{ item.desc }}</p>
        <p class="mt-1 text-[10px] text-muted-foreground/70">{{ item.time }}</p>
      </div>
    </template>
  </WidgetList>
</template>
