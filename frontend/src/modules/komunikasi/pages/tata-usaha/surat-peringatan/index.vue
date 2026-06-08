<script setup>
import { computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Plus, Mail, AlertTriangle, CreditCard } from 'lucide-vue-next'
import { useSuratPeringatanStore } from '@/stores/suratPeringatanStore'
import { usePagination } from '@/composables/usePagination'
import { toast } from 'vue-sonner'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import SuratPeringatanPrintModal from '../../../components/SuratPeringatanPrintModal.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { glassSlide, glassFade } from '@/config/motion'

const router = useRouter()
const store = useSuratPeringatanStore()

const isPrintModalOpen = ref(false)
const selectedSurat = ref(null)

const filterValues = ref({
  search: ''
})

const filters = computed(() => [
  { type: 'search', key: 'search', placeholder: 'Cari Nama Siswa ......' }
])

const columns = computed(() => [
  { key: 'namaSiswa', label: 'Nama Siswa' },
  { key: 'tanggalDibuat', label: 'Tanggal Dibuat', type: 'date' },
  { key: 'jenisSurat', label: 'Jenis Surat' },
  { key: 'actions', label: 'Aksi' }
])

const itemsPerPage = ref(5)

const suratList = computed(() => {
  if (!filterValues.value.search) return store.items
  const query = filterValues.value.search.toLowerCase()
  return store.items.filter(item => item.namaSiswa.toLowerCase().includes(query))
})

const { currentPage, total, from, to, paginatedItems: paginatedSuratList } = usePagination(suratList, itemsPerPage)

watch(suratList, () => {
  currentPage.value = 1
})

const totalPelanggaran = computed(() => {
  return store.items.filter(i => i.jenisSurat === 'Surat Pelanggaran').length
})

const totalTunggakan = computed(() => {
  return store.items.filter(i => i.jenisSurat === 'Surat Tunggakan').length
})

function openCreateForm() {
  router.push('/komunikasi/persuratan/peringatan/buat')
}

function viewSurat(item) {
  selectedSurat.value = item
  isPrintModalOpen.value = true
}

function editSurat(item) {
  router.push('/komunikasi/persuratan/peringatan/edit/' + item.id)
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
      title="Daftar Surat Peringatan Kedisiplinan/Tunggakan"
      description="Kelola Surat Peringatan Kedisiplinan/Tunggakan"
      :actions="[
        {
          label: 'Buat Surat',
          icon: Plus,
          variant: 'default',
          click: openCreateForm
        }
      ]"
    />

    <StatCardGrid
      cols="3"
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
    >
      <StatCard
        label="Total Surat Dibuat"
        :value="store.items.length"
        :icon="Mail"
        variant="primary"
      />
      <StatCard
        label="Surat Kedisiplinan"
        :value="totalPelanggaran"
        :icon="AlertTriangle"
        variant="amber"
      />
      <StatCard
        label="Surat Tunggakan"
        :value="totalTunggakan"
        :icon="CreditCard"
        variant="violet"
      />
    </StatCardGrid>

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
        delete-label="namaSiswa"
      >
        <template #cell-namaSiswa="{ item }">
          <div class="flex flex-col text-left">
            <span class="font-medium text-foreground">{{ item.namaSiswa }}</span>
            <span class="text-[10px] text-muted-foreground">{{ item.nisn }}</span>
          </div>
        </template>

        <template #cell-jenisSurat="{ value }">
          <span class="font-medium" :class="value === 'Surat Pelanggaran' ? 'text-amber-600' : 'text-rose-600'">
            {{ value === 'Surat Pelanggaran' ? 'Kedisiplinan' : 'Tunggakan' }}
          </span>
        </template>
      </DataTableCard>
    </div>

    <!-- Print Modal -->
    <SuratPeringatanPrintModal 
      v-model:open="isPrintModalOpen" 
      :surat="selectedSurat" 
    />
  </div>
</template>
