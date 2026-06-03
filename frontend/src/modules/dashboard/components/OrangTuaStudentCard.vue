<script setup lang="ts">
import { computed } from 'vue'
import { Badge } from '@/components/ui/badge'
import { useAuthStore } from '@/stores/authStore'
import RollingNumber from '@/components/ui/rolling-number/RollingNumber.vue'

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const auth = useAuthStore()
const computedDelay = computed(() => (auth.isJustLoggedIn ? 1400 : 0) + props.delay)
</script>

<template>
  <div
    v-motion
    :initial="{ opacity: 0, y: 30, scale: 0.98 }"
    :visible-once="{ opacity: 1, y: 0, scale: 1, transition: { type: 'spring', stiffness: 200, damping: 20, mass: 0.8, delay: computedDelay } }"
    class="relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-white/40 group glass-ui flex min-h-[210px] w-full rounded-xl"
  >
    <!-- Info sisi kiri — glass styling -->
    <div class="flex flex-col justify-between p-6 bg-card/45 dark:bg-card/25 w-[60%] shrink-0 relative z-10 border-r border-white/10">
      <div class="space-y-1">
        <Badge variant="outline" class="text-[10px] mb-2 w-fit border-primary/20 bg-primary/10 text-primary hover:bg-primary/25">
          Siswa
        </Badge>
        <h2 class="text-xl font-bold leading-tight text-foreground">Aditya Pratama</h2>
        <p class="text-sm text-muted-foreground mt-0.5">Kelas 10 - IPA 2</p>
        <p class="text-xs text-muted-foreground/70">NISN: 0092837411</p>
      </div>

      <!-- Mini stats -->
      <div class="flex gap-3 mt-5">
        <div class="rounded-lg border border-white/10 bg-white/5 dark:bg-white/5 px-4 py-3 text-left flex-1">
          <p class="text-2xl font-bold text-foreground">
            <RollingNumber value="8.8" :delay="computedDelay" />
          </p>
          <p class="text-xs text-muted-foreground mt-1">Rata-rata</p>
        </div>
        <div class="rounded-lg border border-white/10 bg-white/5 dark:bg-white/5 px-4 py-3 text-left flex-1">
          <p class="text-2xl font-bold text-foreground">
            <RollingNumber value="95%" :delay="computedDelay" />
          </p>
          <p class="text-xs text-muted-foreground mt-1">Kehadiran</p>
        </div>
      </div>
    </div>

    <!-- Foto siswa sisi kanan -->
    <div class="relative flex-1 overflow-hidden">
      <img
        src="/images/student-dummy.png"
        alt="Foto Siswa"
        class="absolute inset-0 w-full h-full object-cover object-top"
      />
      <div class="absolute inset-0 bg-gradient-to-r from-card/65 dark:from-card/45 via-transparent to-transparent pointer-events-none" />
    </div>
  </div>
</template>
