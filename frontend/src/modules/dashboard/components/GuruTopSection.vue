<script setup lang="ts">
import { computed } from 'vue'
import { BookOpen, ClipboardCheck, TrendingUp, Sun } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { useAuthStore } from '@/stores/authStore'
import StatCard from '@/components/stat-card/StatCard.vue'

const auth = useAuthStore()
const nama = auth.user?.name ?? 'Guru'
const computedDelay = computed(() => (auth.isJustLoggedIn ? 1400 : 0) + 100)
</script>

<template>
  <div class="grid gap-4 lg:grid-cols-3">
    <!-- Welcome Card (Glassmorphism & animated) -->
    <Card
      v-motion
      :initial="{ opacity: 0, y: 30, scale: 0.95 }"
      :visible-once="{ opacity: 1, y: 0, scale: 1, transition: { type: 'spring', stiffness: 200, damping: 20, mass: 0.8, delay: computedDelay } }"
      class="relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-white/40 group glass-ui flex flex-col justify-between"
    >
      <!-- Watermark Icon Background -->
      <Sun class="absolute -right-4 -bottom-4 size-24 opacity-[0.04] rotate-12 transition-transform duration-300 group-hover:scale-110 text-violet-500" />

      <CardHeader class="pb-2 relative z-10">
        <CardDescription class="text-sm font-medium">Selamat Pagi 👋</CardDescription>
        <p class="text-xl font-bold tracking-tight text-violet-500">{{ nama }}</p>
      </CardHeader>
      <CardContent class="space-y-3 relative z-10">
        <p class="text-xs text-muted-foreground leading-relaxed">
          Hari ini Anda memiliki <strong class="text-foreground">4 sesi</strong> mengajar
          dan <strong class="text-foreground">12 tugas</strong> siswa yang perlu dinilai.
        </p>
        <Button variant="outline" size="sm" class="h-8 text-xs gap-1.5 border-white/10 hover:bg-white/5">
          <BookOpen class="size-3.5" />
          Lihat Jadwal Penuh
        </Button>
      </CardContent>
    </Card>

    <!-- Rata-rata Presensi -->
    <StatCard
      :delay="250"
      label="Rata-rata Presensi"
      value="94.8%"
      trend="+2.4% dari bulan lalu"
      trendDirection="up"
      :icon="TrendingUp"
      variant="emerald"
    />

    <!-- Tugas Menunggu -->
    <StatCard
      :delay="400"
      label="Tugas Menunggu"
      value="12"
      trend="Deadline: 2 hari"
      trendDirection="down"
      :icon="ClipboardCheck"
      variant="amber"
    />
  </div>
</template>
