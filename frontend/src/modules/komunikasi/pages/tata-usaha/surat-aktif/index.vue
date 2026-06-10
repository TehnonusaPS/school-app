<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import { Plus, Mail } from 'lucide-vue-next'
import { useSuratAktifStore } from '@/stores/suratAktifStore'
import { usePagination } from '@/composables/usePagination'
import { toast } from 'vue-sonner'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import SuratAktifPrintModal from '../../../components/SuratAktifPrintModal.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { glassSlide, glassFade } from '@/config/motion'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet'
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { fetchAllSiswa } from '@/services/siswaService'

const store = useSuratAktifStore()

const isPrintModalOpen = ref(false)
const selectedSurat = ref(null)

// --- Inline Form State ---
const isFormSheetOpen = ref(false)
const isEditMode = ref(false)
const isLoadingSiswa = ref(false)
const siswaList = ref([])

const formItem = ref({
  id: '',
  nama: '',
  nisn: '',
  tahunAkademik: '2025/2026',
  semester: 'Genap',
  kelas: '',
  tanggalLahir: '',
  alamat: ''
})

onMounted(async () => {
  isLoadingSiswa.value = true
  try {
    const data = await fetchAllSiswa()
    siswaList.value = data.map(user => {
      const randomKelas = ['VI A', 'VI B', 'V A', 'V B', 'IV A', 'IV B'][user.id % 6]
      const year = 2012 + (user.id % 4)
      const month = String(1 + (user.id % 12)).padStart(2, '0')
      const day = String(10 + (user.id % 18)).padStart(2, '0')
      
      return {
        ...user,
        nisn: '10' + user.id.toString().padStart(4, '0') + Math.floor(1000 + Math.random() * 9000),
        kelas: randomKelas,
        tanggalLahir: `${year}-${month}-${day}`
      }
    })
  } catch (error) {
    toast.error('Gagal memuat data siswa.')
  } finally {
    isLoadingSiswa.value = false
  }
})
const computedSiswaList = computed(() => {
  const list = [...siswaList.value]
  if (formItem.value.nama && !list.some(s => s.name === formItem.value.nama)) {
    list.push({
      id: -1,
      name: formItem.value.nama,
      nisn: formItem.value.nisn || '',
      kelas: formItem.value.kelas || '',
      tanggalLahir: formItem.value.tanggalLahir || '',
      address: { street: formItem.value.alamat || '', city: '' }
    })
  }
  return list
})

const selectedSiswaId = computed(() => {
  if (!formItem.value.nama) return undefined
  const found = computedSiswaList.value.find(s => s.name === formItem.value.nama)
  return found ? found.id.toString() : undefined
})

function onSiswaSelected(siswaId) {
  const siswa = computedSiswaList.value.find(s => s.id === parseInt(siswaId))
  if (siswa) {
    formItem.value.nama = siswa.name
    formItem.value.nisn = siswa.nisn
    formItem.value.kelas = siswa.kelas
    formItem.value.tanggalLahir = siswa.tanggalLahir
    formItem.value.alamat = siswa.address ? `${siswa.address.street}, ${siswa.address.city || ''}`.trim().replace(/,$/, '') : ''
  }
}

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
  isEditMode.value = false
  formItem.value = {
    id: '',
    nama: '',
    nisn: '',
    tahunAkademik: '2025/2026',
    semester: 'Genap',
    kelas: '',
    tanggalLahir: '',
    alamat: ''
  }
  isFormSheetOpen.value = true
}

function viewSurat(id, item) {
  if (item && typeof item === 'object') {
    selectedSurat.value = item
  } else if (id && typeof id === 'object') {
    selectedSurat.value = id
  } else if (id) {
    selectedSurat.value = store.getById(id)
  }
  isPrintModalOpen.value = true
}

function editSurat(item) {
  isEditMode.value = true
  formItem.value = {
    id: item.id,
    nama: item.nama,
    nisn: item.nisn,
    tahunAkademik: item.tahunAkademik,
    semester: item.semester,
    kelas: item.kelas,
    tanggalLahir: item.tanggalLahir,
    alamat: item.alamat || ''
  }
  isFormSheetOpen.value = true
}

function deleteSurat(id, item) {
  store.remove(id)
  toast.success('Surat berhasil dihapus!')
}

function handleSave() {
  if (!formItem.value.nama || !formItem.value.tahunAkademik || !formItem.value.semester || !formItem.value.kelas) {
    toast.error('Mohon lengkapi semua kolom yang wajib diisi.')
    return
  }

  try {
    if (isEditMode.value) {
      store.update(formItem.value.id, { ...formItem.value })
      toast.success('Surat keterangan aktif berhasil diperbarui!')
    } else {
      store.add({ ...formItem.value })
      toast.success('Surat keterangan aktif berhasil dibuat!')
    }
    isFormSheetOpen.value = false
  } catch (error) {
    toast.error('Gagal menyimpan surat.')
  }
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
        @view="viewSurat"
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
    
    <!-- Form Sheet (Inline Create/Edit) -->
    <Sheet v-model:open="isFormSheetOpen">
      <SheetContent :show-close-button="false" class="sm:max-w-[500px] flex flex-col h-full gap-2">
        <SheetHeader class="border-b border-border pb-3 text-left">
          <SheetTitle class="text-base font-bold text-foreground">
            {{ isEditMode ? 'Edit Surat Keterangan Aktif' : 'Buat Surat Keterangan Aktif' }}
          </SheetTitle>
          <SheetDescription class="text-xs text-muted-foreground">
            {{ isEditMode ? 'Perbarui informasi surat keterangan siswa aktif.' : 'Lengkapi formulir di bawah ini untuk menerbitkan surat aktif baru.' }}
          </SheetDescription>
        </SheetHeader>

        <div class="flex-1 overflow-y-auto py-6 pr-1 space-y-6 no-scrollbar">
          <Accordion type="multiple" class="w-full" :default-value="['info']">
            <AccordionItem value="info">
              <AccordionTrigger class="text-sm font-semibold">
                Informasi Umum
              </AccordionTrigger>
              <AccordionContent class="space-y-4 pt-3 text-left">
                <div class="space-y-4">
                  <!-- Nama Siswa -->
                  <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-muted-foreground">Nama Siswa</label>
                    <Select :modelValue="selectedSiswaId" @update:modelValue="onSiswaSelected" :disabled="isLoadingSiswa">
                      <SelectTrigger class="h-10 rounded-xl">
                        <SelectValue :placeholder="isLoadingSiswa ? 'Memuat data siswa...' : 'Pilih Siswa...'" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem 
                          v-for="siswa in computedSiswaList" 
                          :key="siswa.id" 
                          :value="siswa.id.toString()"
                        >
                          {{ siswa.name }} - {{ siswa.nisn }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  </div>

                  <!-- Tahun Akademik & Semester -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                      <label class="text-xs font-semibold text-muted-foreground">Tahun Akademik</label>
                      <Select v-model="formItem.tahunAkademik">
                        <SelectTrigger class="h-10 rounded-xl">
                          <SelectValue placeholder="Pilih Tahun Akademik" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="2024/2025">2024/2025</SelectItem>
                          <SelectItem value="2025/2026">2025/2026</SelectItem>
                          <SelectItem value="2026/2027">2026/2027</SelectItem>
                        </SelectContent>
                      </Select>
                    </div>

                    <div class="space-y-1.5">
                      <label class="text-xs font-semibold text-muted-foreground">Semester</label>
                      <Select v-model="formItem.semester">
                        <SelectTrigger class="h-10 rounded-xl">
                          <SelectValue placeholder="Pilih Semester" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="Ganjil">Ganjil</SelectItem>
                          <SelectItem value="Genap">Genap</SelectItem>
                        </SelectContent>
                      </Select>
                    </div>
                  </div>

                  <!-- Kelas & Tanggal Lahir -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                      <label class="text-xs font-semibold text-muted-foreground">Kelas</label>
                      <Input v-model="formItem.kelas" placeholder="Misal: VI A" class="h-10 rounded-xl" />
                    </div>

                    <div class="space-y-1.5">
                      <label class="text-xs font-semibold text-muted-foreground">Tanggal Lahir</label>
                      <Input type="date" v-model="formItem.tanggalLahir" class="h-10 rounded-xl" />
                    </div>
                  </div>

                  <!-- Alamat -->
                  <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-muted-foreground">Alamat</label>
                    <Textarea v-model="formItem.alamat" placeholder="Masukkan alamat lengkap siswa..." class="min-h-24 rounded-xl resize-none" />
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>

        <div class="border-t border-border pt-4 flex items-center justify-end gap-2 shrink-0">
          <Button type="button" variant="ghost" class="text-xs font-bold rounded-xl" @click="isFormSheetOpen = false">
            Batal
          </Button>
          <Button type="button" class="text-xs font-bold rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 shadow-none border-none" @click="handleSave">
            {{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}
          </Button>
        </div>
      </SheetContent>
    </Sheet>

    <!-- Print Preview Modal -->
    <SuratAktifPrintModal v-model:open="isPrintModalOpen" :data="selectedSurat" />
  </div>
</template>
