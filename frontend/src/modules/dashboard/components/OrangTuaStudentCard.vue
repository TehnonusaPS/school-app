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
    class="relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border-primary/25 dark:border-primary/15 hover:border-primary/45 group glass-ui flex flex-row min-h-[210px] w-full p-0 gap-0"
  >
    <!-- Info sisi kiri — primary glass styling (vibrant & not faded) -->
    <div class="flex flex-col justify-between p-6 bg-gradient-to-br from-primary/80 via-primary/70 to-primary/60 w-[60%] shrink-0 relative z-10 backdrop-blur-md -mr-px text-primary-foreground">
      <div class="space-y-1">
        <Badge class="text-[10px] mb-2 w-fit bg-primary-foreground/20 text-primary-foreground border-none shadow-none hover:bg-primary-foreground/30">
          Siswa
        </Badge>
        <h2 class="text-xl font-bold leading-tight text-white">Aditya Pratama</h2>
        <p class="text-sm text-white/85 mt-0.5">Kelas 10 - IPA 2</p>
        <p class="text-xs text-white/65">NISN: 0092837411</p>
      </div>

      <!-- Mini stats -->
      <div class="flex gap-3 mt-5">
        <div class="rounded-xl glass-ui bg-primary-foreground/10 border border-primary-foreground/20 shadow-sm backdrop-blur-md px-4 py-3 text-left flex-1 transition-all duration-300 hover:border-white/30">
          <p class="text-2xl font-bold text-white">
            <RollingNumber value="8.8" :delay="computedDelay" />
          </p>
          <p class="text-xs text-white/70 mt-1">Rata-rata</p>
        </div>
        <div class="rounded-xl glass-ui bg-primary-foreground/10 border border-primary-foreground/20 shadow-sm backdrop-blur-md px-4 py-3 text-left flex-1 transition-all duration-300 hover:border-white/30">
          <p class="text-2xl font-bold text-white">
            <RollingNumber value="95%" :delay="computedDelay" />
          </p>
          <p class="text-xs text-white/70 mt-1">Kehadiran</p>
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
      <div class="absolute inset-0 bg-gradient-to-r from-primary/70 via-transparent to-transparent pointer-events-none" />
    </div>
  </Card>
</template>
