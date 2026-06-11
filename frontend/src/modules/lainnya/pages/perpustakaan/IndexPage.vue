<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import { Lock } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

import PageHeader from '@/components/page-header/PageHeader.vue'
import PerpustakaanStatCards from '../../components/PerpustakaanStatCards.vue'
import PerpustakaanTable from '../../components/PerpustakaanTable.vue'
import { usePerpustakaanStore } from '@/stores/perpustakaanStore'
import { computed } from 'vue'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const router = useRouter()
const store = usePerpustakaanStore()

const books = computed(() => store.items)

function handleDelete(id) {
  store.delete(id)
  toast.success('Buku berhasil dihapus!')
}

import DataSheet from '@/components/data-sheet/DataSheet.vue'

const isDetailSheetOpen = ref(false)
const selectedItemForDetail = ref(null)

const handleView = (id) => {
  const item = books.value.find(a => a.id === id)
  if (item) {
    selectedItemForDetail.value = item
    isDetailSheetOpen.value = true
  }
}

const rawDetailItem = computed(() => {
  if (!selectedItemForDetail.value) return {}
  return {
    name: selectedItemForDetail.value.name,
    kategori: selectedItemForDetail.value.kategori
  }
})

const detailSections = computed(() => {
  if (!selectedItemForDetail.value) return []
  const item = selectedItemForDetail.value
  return [
    {
      id: 'info',
      title: 'Informasi Buku',
      fields: [
        { label: 'Judul Buku', value: item.name },
        { label: 'ISBN', value: item.isbn || '-' },
        { label: 'Kategori', value: item.kategori },
        { label: 'Penulis', value: item.penulis || '-' },
        { label: 'Penerbit', value: item.penerbit || '-' },
        { label: 'Tahun Terbit', value: item.tahunTerbit || '-' },
        { label: 'Jumlah Stok', value: `${item.jumlahStok} Buku` },
        { label: 'Lokasi Rak', value: item.lokasiRak || '-' },
        { label: 'Deskripsi', value: item.deskripsi || '-' }
      ]
    }
  ]
})
</script>

<template>
  <div v-if="auth.user?.role === 'admin_sekolah'" class="space-y-6">
    <PageHeader 
      title="Manajemen Buku Perpustakaan" 
      description="Kelola koleksi buku yang ada di perpustakaan sekolah" 
    />

    <!-- Stats Cards -->
    <PerpustakaanStatCards />

    <!-- Data Table & Filters -->
    <PerpustakaanTable :items="books" @delete="handleDelete" @view="handleView" />

    <!-- Detail Sheet -->
    <DataSheet
      v-model:open="isDetailSheetOpen"
      :item="rawDetailItem"
      title-key="name"
      :badge="rawDetailItem.kategori"
      :badge-variant="rawDetailItem.kategori === 'Sains' ? 'blue' : (rawDetailItem.kategori === 'TIK' ? 'green' : (rawDetailItem.kategori === 'Bahasa' ? 'amber' : 'red'))"
      :sections="detailSections"
    />
  </div>
  
  <div v-else class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="bg-card border border-border rounded-2xl p-8 max-w-md w-full shadow-sm flex flex-col items-center">
      <div class="size-16 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mb-6 border border-primary/20">
        <Lock class="size-8" />
      </div>
      <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-foreground mb-2">Akses Dibatasi</h2>
      <p class="text-sm text-muted-foreground leading-relaxed mb-8">
        Halaman Manajemen Perpustakaan untuk role Anda (Kepala Sekolah / Admin Yayasan) belum tersedia dan sedang dalam tahap pengembangan.
      </p>
      <Button @click="router.push('/dashboard')" size="lg" class="w-full font-semibold rounded-xl transition-all active:scale-[0.98]">
        Kembali ke Dashboard
      </Button>
    </div>
  </div>
</template>
