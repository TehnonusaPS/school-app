<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import { Lock } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

import PageHeader from '@/components/page-header/PageHeader.vue'
import RuanganStatCards from '../../components/RuanganStatCards.vue'
import RuanganTable from '../../components/RuanganTable.vue'
import { useRuanganStore } from '@/stores/ruanganStore'
import { computed } from 'vue'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const router = useRouter()
const store = useRuanganStore()

const rooms = computed(() => store.items)

function handleDelete(id) {
  store.delete(id)
  toast.success('Ruangan berhasil dihapus!')
}

import DataSheet from '@/components/data-sheet/DataSheet.vue'

const isDetailSheetOpen = ref(false)
const selectedItemForDetail = ref(null)

const handleView = (id) => {
  const item = rooms.value.find(a => a.id === id)
  if (item) {
    selectedItemForDetail.value = item
    isDetailSheetOpen.value = true
  }
}

const rawDetailItem = computed(() => {
  if (!selectedItemForDetail.value) return {}
  return {
    name: selectedItemForDetail.value.name,
    category: selectedItemForDetail.value.category
  }
})

const detailSections = computed(() => {
  if (!selectedItemForDetail.value) return []
  const item = selectedItemForDetail.value
  return [
    {
      id: 'info',
      title: 'Informasi Ruangan',
      fields: [
        { label: 'Kode', value: item.code },
        { label: 'Nama Ruangan', value: item.name },
        { label: 'Tipe', value: item.category },
        { label: 'Gedung', value: item.building || '-' },
        { label: 'Kapasitas', value: `${item.capacity} Orang` },
        { label: 'Luas', value: `${item.area} m²` },
        { label: 'Fasilitas', value: item.facilities?.join(', ') || '-' }
      ]
    }
  ]
})
</script>

<template>
  <div v-if="auth.user?.role === 'admin_sekolah'" class="space-y-6">
    <PageHeader 
      title="Manajemen Ruangan Sekolah" 
      description="Kelola data ruangan kelas, laboratorium, dan fasilitas lainnya" 
    />

    <!-- Stats Cards -->
    <RuanganStatCards />

    <!-- Data Table & Filters -->
    <RuanganTable :items="rooms" @delete="handleDelete" @view="handleView" />

    <!-- Detail Sheet -->
    <DataSheet
      v-model:open="isDetailSheetOpen"
      :item="rawDetailItem"
      title-key="name"
      :badge="rawDetailItem.category"
      :badge-variant="rawDetailItem.category === 'Ruang Kelas' ? 'blue' : (rawDetailItem.category === 'Laboratorium' ? 'amber' : 'green')"
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
        Halaman Manajemen Ruangan untuk role Anda (Kepala Sekolah / Admin Yayasan) belum tersedia dan sedang dalam tahap pengembangan.
      </p>
      <Button @click="router.push('/dashboard')" size="lg" class="w-full font-semibold rounded-xl transition-all active:scale-[0.98]">
        Kembali ke Dashboard
      </Button>
    </div>
  </div>
</template>
