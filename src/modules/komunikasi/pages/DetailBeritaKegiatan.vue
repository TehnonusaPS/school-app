<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { ArrowLeft, Calendar, Building, Megaphone } from 'lucide-vue-next'
import { useBeritaKegiatanStore } from '@/stores/beritaKegiatanStore'

const route = useRoute()
const router = useRouter()
const store = useBeritaKegiatanStore()

const item = ref(null)

onMounted(() => {
  const result = store.getById(route.params.id)
  if (!result) {
    toast.error('Berita kegiatan tidak ditemukan!')
    router.push('/komunikasi/berita-kegiatan')
    return
  }
  item.value = result
})

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const [year, month, day] = dateStr.split('-')
  return `${day}/${month}/${year}`
}

const categoryBadgeClass = (kategori) => {
  switch (kategori) {
    case 'AKADEMIK':
      return 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20'
    case 'UMUM':
      return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20'
    case 'KEUANGAN':
      return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20'
    default:
      return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20'
  }
}

function handleBack() {
  router.push('/komunikasi/berita-kegiatan')
}
</script>

<template>
  <div class="space-y-6 p-1" v-if="item">
    <!-- Header -->
    <div class="flex flex-col gap-3 mb-6">
      <Button 
        variant="ghost" 
        size="sm" 
        @click="handleBack"
        class="w-fit gap-1.5 -ml-2 text-muted-foreground hover:text-foreground h-9 rounded-lg"
      >
        <ArrowLeft class="size-4" />
        Kembali ke Daftar
      </Button>
      <div class="flex flex-col gap-1">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text">
          Detail Berita Kegiatan
        </h1>
        <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
          Informasi lengkap mengenai berita kegiatan sekolah yang dipublikasikan
        </p>
      </div>
    </div>

    <!-- Details Container -->
    <Card class="rounded-2xl border-border bg-card shadow-xs overflow-hidden">
      <CardContent class="p-6 sm:p-8 space-y-6">
        
        <!-- Metadata Header Area -->
        <div class="flex flex-wrap items-center gap-3 pb-5 border-b border-border/80">
          <Badge :class="categoryBadgeClass(item.kategori)" class="rounded-full px-3 py-1 font-bold uppercase tracking-wider text-[10px]">
            {{ item.kategori }}
          </Badge>
          
          <div class="flex items-center gap-1.5 text-xs text-muted-foreground font-medium">
            <Calendar class="size-4" />
            <span>Diterbitkan: {{ formatDate(item.tanggal) }}</span>
          </div>

          <div class="flex items-center gap-1.5 text-xs text-muted-foreground font-medium">
            <Building class="size-4" />
            <span>Unit: {{ item.sekolah }}</span>
          </div>
        </div>

        <!-- News Title -->
        <h2 class="text-xl sm:text-2xl font-bold text-foreground leading-snug">
          {{ item.judul }}
        </h2>

        <!-- News Main Image -->
        <div v-if="item.gambar" class="rounded-2xl overflow-hidden max-h-[400px] w-full bg-muted border border-border/40">
          <img :src="item.gambar" alt="Berita Gambar" class="object-cover w-full h-full max-h-[400px]" />
        </div>
        <div v-else class="rounded-2xl border border-dashed border-border bg-muted/20 p-8 flex flex-col items-center justify-center text-center gap-2">
          <Megaphone class="size-8 text-muted-foreground/40" />
          <p class="text-xs text-muted-foreground">Tidak ada gambar utama yang diunggah untuk berita kegiatan ini.</p>
        </div>

        <!-- News Body Text Content -->
        <div class="prose prose-slate max-w-none text-foreground dark:prose-invert">
          <p class="text-sm sm:text-base leading-relaxed whitespace-pre-line text-slate-700 dark:text-slate-300">
            {{ item.isi }}
          </p>
        </div>

      </CardContent>
    </Card>
  </div>
</template>
