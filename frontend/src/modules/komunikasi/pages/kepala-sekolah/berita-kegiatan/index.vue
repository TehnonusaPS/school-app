<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import BeritaKegiatanTable from '../../../components/BeritaKegiatanTable.vue'
import { Button } from '@/components/ui/button'
import { Plus, Megaphone } from 'lucide-vue-next'
import { useBeritaKegiatanStore } from '@/stores/beritaKegiatanStore'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { glassSlide, glassFade } from '@/config/motion'

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
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 p-1"
  >
    <!-- Header & Stats Card -->
    <PageHeader
      title="Daftar Berita Kegiatan"
      description="Kelola dan Kirim Berita kegiatan"
      :actions="[
        {
          label: 'Buat Berita Baru',
          icon: Plus,
          variant: 'default',
          click: openCreateForm
        }
      ]"
    />

    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
    >
      <StatCard
        label="Total Berita Terbuat"
        :value="beritaKegiatans.length"
        :icon="Megaphone"
        variant="primary"
      />
    </div>

    <!-- Main Table -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
    >
      <BeritaKegiatanTable 
        :items="beritaKegiatans" 
        @view="viewBerita" 
        @edit="editBerita" 
        @delete="deleteBerita"
      />
    </div>
  </div>
</template>
