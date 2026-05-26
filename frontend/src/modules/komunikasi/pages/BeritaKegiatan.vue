<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import BeritaKegiatanTable from '../components/BeritaKegiatanTable.vue'
import { Button } from '@/components/ui/button'
import { Plus, Megaphone } from 'lucide-vue-next'
import { useBeritaKegiatanStore } from '@/stores/beritaKegiatanStore'

const router = useRouter()
const store = useBeritaKegiatanStore()
const beritaKegiatans = computed(() => store.items)

function openCreateForm() {
  router.push('/komunikasi/berita-kegiatan/buat')
}

function viewBerita(item) {
  router.push(`/komunikasi/berita-kegiatan/lihat/${item.id}`)
}

function editBerita(item) {
  router.push(`/komunikasi/berita-kegiatan/edit/${item.id}`)
}

function deleteBerita(item) {
  store.delete(item.id)
  toast.success('Berita kegiatan berhasil dihapus!')
}
</script>

<template>
  <div class="space-y-6 p-1">
    <!-- Header & Stats Card -->
    <div>
      <div class="flex flex-col gap-1 mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text">
          Daftar Berita Kegiatan
        </h1>
        <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
          Kelola dan Kirim Berita kegiatan
        </p>
      </div>

      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-card border border-border/80 p-4 sm:p-5 rounded-2xl shadow-xs">
        <div class="flex items-center gap-3 sm:gap-4">
          <div class="p-2.5 sm:p-3 bg-primary/10 text-primary rounded-xl border border-primary/20 shrink-0">
            <Megaphone class="size-5 sm:size-6 text-primary" />
          </div>
          <div>
            <p class="text-[10px] sm:text-xs font-semibold text-muted-foreground uppercase tracking-wider leading-none">Total Berita Terbuat</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-foreground mt-1 sm:mt-1.5 tabular-nums leading-none">
              {{ beritaKegiatans.length }}
            </p>
          </div>
        </div>

        <Button 
          @click="openCreateForm" 
          size="lg" 
          class="h-11 sm:h-12 w-full sm:w-auto px-6 font-semibold shadow-xs transition-all hover:scale-[1.01] active:scale-[0.99] gap-2 rounded-xl text-sm"
        >
          <Plus class="size-4 sm:size-5" />
          Buat Berita Baru
        </Button>
      </div>
    </div>

    <!-- Main Table -->
    <BeritaKegiatanTable 
      :items="beritaKegiatans" 
      @view="viewBerita" 
      @edit="editBerita" 
      @delete="deleteBerita"
    />
  </div>
</template>
