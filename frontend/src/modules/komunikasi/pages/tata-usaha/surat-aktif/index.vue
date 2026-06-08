<script setup>
import { computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Plus, Mail } from 'lucide-vue-next'
import { useSuratAktifStore } from '@/stores/suratAktifStore'
import { usePagination } from '@/composables/usePagination'
import { toast } from 'vue-sonner'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import SuratAktifPrintModal from '../../../components/SuratAktifPrintModal.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { glassSlide, glassFade } from '@/config/motion'

const router = useRouter()
const store = useSuratAktifStore()

const isPrintModalOpen = ref(false)
const selectedSurat = ref(null)

const filterValues = ref({
  search: '',
  tanggalDibuat: ''
})

const filters = computed(() => [
  { type: 'search', key: 'search', placeholder: 'Cari Nama Siswa ......' },
  { type: 'date', key: 'tanggalDibuat', label: 'Tanggal:' }
])

const columns = computed(() => [
  { key: 'nama', label: 'Nama Siswa' },
  { key: 'tanggalDibuat', label: 'Tanggal Dibuat', type: 'date' },
  { key: 'actions', label: 'Aksi' }
])

const itemsPerPage = ref(5)

const suratList = computed(() => {
  let list = store.items
  
  if (filterValues.value.search) {
    const query = filterValues.value.search.toLowerCase()
    list = list.filter(item => item.nama.toLowerCase().includes(query))
  }
  
  if (filterValues.value.tanggalDibuat) {
    list = list.filter(item => item.tanggalDibuat === filterValues.value.tanggalDibuat)
  }
  
  return list
})

const { currentPage, total, from, to, paginatedItems: paginatedSuratList } = usePagination(suratList, itemsPerPage)

watch(suratList, () => {
  currentPage.value = 1
})

function openCreateForm() {
  router.push('/komunikasi/persuratan/aktif/buat')
}

function viewSurat(item) {
  selectedSurat.value = item
  isPrintModalOpen.value = true
}

function editSurat(item) {
  router.push('/komunikasi/persuratan/aktif/edit/' + item.id)
}

function deleteSurat(id, item) {
  store.remove(id)
  toast.success('Surat berhasil dihapus!')
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
      title="Daftar Surat Keterangan Aktif"
      description="Kelola Surat Keterangan Aktif"
      :actions="[
        {
          label: 'Buat Surat Keterangan Aktif',
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
        label="Total Surat Keterangan Aktif Dibuat"
        :value="store.items.length"
        :icon="Mail"
        variant="primary"
      />
    </div>

    <!-- Filters & Table using DataTableCard -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
    >
      <DataTableCard
        :columns="columns"
        :items="paginatedSuratList"
        :filters="filters"
        v-model:filterValues="filterValues"
        v-model:perPage="itemsPerPage"
        :from="from"
        :to="to"
        :total="total"
        :page="currentPage"
        @update:page="currentPage = $event"
        @view="(id, item) => viewSurat(item)"
        @edit="editSurat"
        @delete="deleteSurat"
        delete-label="nama"
      >
        <template #cell-nama="{ item }">
          <div class="flex flex-col text-left">
            <span class="font-medium text-foreground">{{ item.nama }}</span>
            <span class="text-[10px] text-muted-foreground">{{ item.nisn }}</span>
          </div>
        </template>
      </DataTableCard>
    </div>
    
    <!-- Print Preview Modal -->
    <SuratAktifPrintModal v-model:open="isPrintModalOpen" :data="selectedSurat" />
  </div>
</template>
