<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import { Lock } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import PageHeader from '@/components/page-header/PageHeader.vue'

import AsetStatCards from '../../components/AsetStatCards.vue'
import AsetTable from '../../components/AsetTable.vue'
import { useAsetStore } from '@/stores/asetStore'
import { computed } from 'vue'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const router = useRouter()
const store = useAsetStore()

const asets = computed(() => store.items)

function handleDelete(id) {
  store.delete(id)
  toast.success('Aset berhasil dihapus!')
}

import DataSheet from '@/components/data-sheet/DataSheet.vue'

const isDetailSheetOpen = ref(false)
const selectedItemForDetail = ref(null)

const handleView = (id) => {
  const item = asets.value.find(a => a.id === id)
  if (item) {
    selectedItemForDetail.value = item
    isDetailSheetOpen.value = true
  }
}

const rawDetailItem = computed(() => {
  if (!selectedItemForDetail.value) return {}
  return {
    name: selectedItemForDetail.value.name,
    condition: selectedItemForDetail.value.condition
  }
})

const detailSections = computed(() => {
  if (!selectedItemForDetail.value) return []
  const item = selectedItemForDetail.value
  return [
    {
      id: 'info',
      title: 'Informasi Aset',
      fields: [
        { label: 'Nama Aset', value: item.name },
        { label: 'Kode', value: item.code },
        { label: 'Kategori', value: item.category },
        { label: 'Merk/Tipe', value: item.brand || '-' },
        { label: 'Gedung', value: item.building || '-' },
        { label: 'Ruangan', value: item.room || '-' },
        { label: 'Nilai', value: `Rp ${Number(item.value).toLocaleString('id-ID')}` },
        { label: 'Kondisi', value: item.condition }
      ]
    }
  ]
})
</script>

<template>
  <div v-if="auth.user?.role === 'admin_sekolah'" class="space-y-6">
    <PageHeader 
      title="Manajemen Aset Sekolah" 
      description="Kelola aset barang yang ada di sekolah" 
    />

    <!-- Stats Cards -->
    <AsetStatCards />

    <!-- Data Table & Filters -->
    <AsetTable :items="asets" @delete="handleDelete" @view="handleView" />

    <!-- Detail Sheet -->
    <DataSheet
      v-model:open="isDetailSheetOpen"
      :item="rawDetailItem"
      title-key="name"
      :badge="rawDetailItem.condition"
      :badge-variant="rawDetailItem.condition === 'Baik' ? 'green' : (rawDetailItem.condition === 'Rusak Ringan' ? 'amber' : 'red')"
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
        Halaman Manajemen Aset untuk role Anda (Kepala Sekolah / Admin Yayasan) belum tersedia dan sedang dalam tahap pengembangan.
      </p>
      <Button @click="router.push('/dashboard')" size="lg" class="w-full font-semibold rounded-xl transition-all active:scale-[0.98]">
        Kembali ke Dashboard
      </Button>
    </div>
  </div>
</template>
