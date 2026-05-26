<script setup lang="ts">
import { CalendarDays } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'

const today = new Date().toLocaleDateString('id-ID', {
  weekday: 'long', day: '2-digit', month: 'short', year: 'numeric'
}).toUpperCase()

const jadwal = [
  { mulai: '07:00', selesai: '08:30', mapel: 'Matematika Dasar', guru: 'Ibu Maria',   aktif: true,  status: 'Berlangsung' },
  { mulai: '09:00', selesai: '10:30', mapel: 'B. Inggris',        guru: 'Bapak Supri', aktif: false, status: 'Mendatang' },
  { mulai: '11:00', selesai: '12:30', mapel: 'Fisika',            guru: 'Bapak Udin',  aktif: false, status: 'Mendatang' },
  { mulai: '13:30', selesai: '15:00', mapel: 'Bahasa Indonesia',  guru: 'Ibu Sari',    aktif: false, status: 'Mendatang' }
]
</script>

<template>
  <Card class="lg:col-span-3">
    <CardHeader class="pb-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <CalendarDays class="size-4 text-primary" />
          <CardTitle class="text-base font-semibold">Jadwal Hari Ini</CardTitle>
        </div>
        <Badge variant="outline" class="text-xs font-semibold text-primary">
          {{ today }}
        </Badge>
      </div>
      <CardDescription class="text-xs">{{ jadwal.length }} pelajaran hari ini</CardDescription>
    </CardHeader>

    <CardContent class="space-y-2 pb-4">
      <div
        v-for="(j, i) in jadwal"
        :key="i"
        :class="[
          'flex items-center gap-4 rounded-xl border px-4 py-3 transition-colors',
          j.aktif ? 'bg-primary/5 border-primary/30' : 'hover:bg-muted/40'
        ]"
      >
        <!-- Waktu -->
        <div class="min-w-[70px] text-right">
          <p :class="['text-sm font-bold tabular-nums', j.aktif ? 'text-primary' : 'text-foreground']">
            {{ j.mulai }} WIB
          </p>
          <p class="text-[10px] text-muted-foreground tabular-nums">{{ j.selesai }} WIB</p>
        </div>

        <!-- Divider -->
        <div :class="['w-px self-stretch', j.aktif ? 'bg-primary/40' : 'bg-border']" />

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <p :class="['text-sm font-semibold truncate', j.aktif ? 'text-primary' : '']">
            {{ j.mapel }}
          </p>
          <p class="text-xs text-muted-foreground">{{ j.guru }}</p>
        </div>

        <!-- Status Button -->
        <Button
          :variant="j.aktif ? 'default' : 'secondary'"
          size="sm"
          class="h-8 text-xs shrink-0"
          :disabled="!j.aktif"
        >
          {{ j.status }}
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
