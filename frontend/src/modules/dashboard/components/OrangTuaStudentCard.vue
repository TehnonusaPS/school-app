<script setup lang="ts">
import { computed } from 'vue'
import { Card } from '@/components/ui/card'
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
  <Card
    v-motion
    :initial="{ opacity: 0, y: 30, scale: 0.98 }"
    :visible-once="{ opacity: 1, y: 0, scale: 1, transition: { type: 'spring', stiffness: 200, damping: 20, mass: 0.8, delay: computedDelay } }"
    class="relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-white/40 group glass-ui flex flex-row min-h-[210px] w-full p-0 gap-0"
  >
    <!-- Info sisi kiri — primary glass styling -->
    <div class="flex flex-col justify-between p-6 bg-gradient-to-br from-primary/25 via-primary/15 to-primary/5 w-[60%] shrink-0 relative z-10 backdrop-blur-sm -mr-px">
      <div class="space-y-1">
        <Badge class="text-[10px] mb-2 w-fit bg-primary/15 hover:bg-primary/25 text-primary border-none shadow-none">
          Siswa
        </Badge>
        <h2 class="text-xl font-bold leading-tight text-foreground">Aditya Pratama</h2>
        <p class="text-sm text-muted-foreground mt-0.5">Kelas 10 - IPA 2</p>
        <p class="text-xs text-muted-foreground/75">NISN: 0092837411</p>
      </div>

      <!-- Mini stats -->
      <div class="flex gap-3 mt-5">
        <div class="rounded-lg bg-primary/10 dark:bg-primary/5 px-4 py-3 text-left flex-1">
          <p class="text-2xl font-bold text-foreground">
            <RollingNumber value="8.8" :delay="computedDelay" />
          </p>
          <p class="text-xs text-muted-foreground mt-1">Rata-rata</p>
        </div>
        <div class="rounded-lg bg-primary/10 dark:bg-primary/5 px-4 py-3 text-left flex-1">
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
      <div class="absolute inset-0 bg-gradient-to-r from-primary/25 via-transparent to-transparent pointer-events-none" />
    </div>
  </Card>
</template>
