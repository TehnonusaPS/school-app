<script setup>
import { onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Users } from 'lucide-vue-next'

const store = useLandingEditorStore()
onMounted(() => { store.fetchLandingPage() })
</script>

<template>
  <BuilderLayout>
    <div class="space-y-4">
      <h4 class="text-sm font-bold text-foreground">Informasi PPDB</h4>
      <div class="border-2 border-dashed border-border/50 rounded-2xl p-10 flex flex-col items-center justify-center text-center bg-background/30">
        <Users class="w-10 h-10 text-muted-foreground mb-4 opacity-50" />
        <h5 class="font-bold text-foreground mb-1">Pengaturan PPDB</h5>
        <p class="text-xs text-muted-foreground max-w-sm">
          Fitur pengaturan kuota, gelombang pendaftaran, dan rincian biaya PPDB sedang dalam tahap pengembangan khusus untuk entitas Sekolah.
        </p>
      </div>
    </div>
  </BuilderLayout>
</template>
