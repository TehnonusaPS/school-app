<script setup>
import { Button } from '@/components/ui/button'
import { Plus, Megaphone } from 'lucide-vue-next'

defineProps({
  total: {
    type: Number,
    required: true
  },
  isSchoolRole: {
    type: Boolean,
    default: false
  }
})

defineEmits(['create'])
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col gap-1 mb-6">
      <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text">
        Daftar Pengumuman
      </h1>
      <p class="text-sm text-muted-foreground leading-relaxed">
        Kelola dan Kirim informasi ke seluruh unit sekolah
      </p>
    </div>

    <!-- Stats & Action -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-card border border-border/80 p-5 rounded-2xl shadow-xs">
      <div class="flex items-center gap-4">
        <div class="p-3 bg-primary/10 text-primary rounded-xl border border-primary/20">
          <Megaphone class="size-6 text-primary" />
        </div>
        <div>
          <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider leading-none">Total Berita Terbuat</p>
          <p class="text-3xl font-extrabold text-foreground mt-1.5 tabular-nums leading-none">
            {{ total }}
          </p>
        </div>
      </div>

      <Button 
        v-if="!isSchoolRole"
        @click="$emit('create')" 
        size="lg" 
        class="h-12 w-full sm:w-auto px-6 font-semibold shadow-xs transition-all hover:scale-[1.01] active:scale-[0.99] gap-2 rounded-xl"
      >
        <Plus class="size-5" />
        Buat Pengumuman Baru
      </Button>
    </div>
  </div>
</template>
