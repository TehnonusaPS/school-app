<script setup>
import { computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Plus, Mail } from 'lucide-vue-next'
import { useSuratDispensasiStore } from '@/stores/suratDispensasiStore'
import { usePagination } from '@/composables/usePagination'
import { toast } from 'vue-sonner'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import SuratDispensasiPrintModal from '../components/SuratDispensasiPrintModal.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { glassSlide, glassFade } from '@/config/motion'

const router = useRouter()
const store = useSuratDispensasiStore()

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
  { key: 'siswa', label: 'Nama Siswa' },
  { key: 'tanggalDibuat', label: 'Tanggal Dibuat', type: 'date' },
  { key: 'actions', label: 'Aksi' }
])

const itemsPerPage = ref(5)

const suratList = computed(() => {
  let list = store.items
  
  if (filterValues.value.search) {
    const query = filterValues.value.search.toLowerCase()
    list = list.filter(item => {
      return item.siswa.some(s => s.nama.toLowerCase().includes(query))
    })
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
  router.push('/komunikasi/persuratan/dispensasi/buat')
}

function viewSurat(item) {
  selectedSurat.value = item
  isPrintModalOpen.value = true
}

function editSurat(item) {
  router.push('/komunikasi/persuratan/dispensasi/edit/' + item.id)
}

function deleteSurat(id, item) {
  store.remove(id)
  toast.success('Surat berhasil dihapus!')
}

function formatNamaSiswa(siswaList) {
  if (!siswaList || siswaList.length === 0) return '-'
  const first = siswaList[0].nama
  if (siswaList.length > 1) {
    return `${first} & ${siswaList.length - 1} lainnya`
  }
  return first
}

function formatNisnSiswa(siswaList) {
  if (!siswaList || siswaList.length === 0) return '-'
  const first = siswaList[0].nisn
  if (siswaList.length > 1) {
    return `${first} (+${siswaList.length - 1})`
  }
  return first
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
      title="Daftar Surat Dispensasi Siswa"
      description="Kelola Surat Dispensasi"
      :actions="[
        {
          label: 'Buat Surat Dispensasi Siswa',
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
        label="Total Surat Dispensasi Siswa Dibuat"
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
        :delete-label="(item) => formatNamaSiswa(item.siswa)"
      >
        <template #cell-siswa="{ item }">
          <div class="flex flex-col text-left">
            <span class="font-medium text-foreground">{{ formatNamaSiswa(item.siswa) }}</span>
            <span class="text-[10px] text-muted-foreground">{{ formatNisnSiswa(item.siswa) }}</span>
          </div>
        </template>
      </DataTableCard>
    </div>
    
    <!-- Print Preview Modal -->
    <SuratDispensasiPrintModal v-model:open="isPrintModalOpen" :data="selectedSurat" />
  </div>
</template>
