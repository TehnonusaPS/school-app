<script setup>
import { onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Building } from 'lucide-vue-next'

const store = useLandingEditorStore()
onMounted(() => { store.fetchLandingPage() })
</script>

<template>
  <BuilderLayout>
    <div class="space-y-4">
      <h4 class="text-sm font-bold text-foreground">Kelola Lembaga Naungan</h4>
      <div class="border-2 border-dashed border-border/50 rounded-2xl p-10 flex flex-col items-center justify-center text-center bg-background/30">
        <Building class="w-10 h-10 text-muted-foreground mb-4 opacity-50" />
        <h5 class="font-bold text-foreground mb-1">Daftar Unit Lembaga</h5>
        <p class="text-xs text-muted-foreground max-w-sm">
          Fitur untuk mengelola unit sekolah tingkat TK hingga SMA yang bernaung di bawah Yayasan sedang dikerjakan.
        </p>
      </div>
    </div>
  </BuilderLayout>
</template>
