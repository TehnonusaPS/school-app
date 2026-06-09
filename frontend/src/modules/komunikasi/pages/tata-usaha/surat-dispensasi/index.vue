<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import { Plus, Mail, Trash2 } from 'lucide-vue-next'
import { useSuratDispensasiStore } from '@/stores/suratDispensasiStore'
import { usePagination } from '@/composables/usePagination'
import { toast } from 'vue-sonner'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import SuratDispensasiPrintModal from '../../../components/SuratDispensasiPrintModal.vue'
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

const store = useSuratDispensasiStore()

const isPrintModalOpen = ref(false)
const selectedSurat = ref(null)

// --- Inline Form State ---
const isFormSheetOpen = ref(false)
const isEditMode = ref(false)
const isLoadingSiswa = ref(false)
const siswaOptions = ref([])

const selectedSiswa = ref([
  { id: Date.now().toString(), siswaId: null, nama: '', nisn: '', kelas: '' }
])

const formItem = ref({
  id: '',
  tanggalAwal: '',
  tanggalAkhir: '',
  perihal: ''
})

onMounted(async () => {
  isLoadingSiswa.value = true
  try {
    const data = await fetchAllSiswa()
    siswaOptions.value = data.map(user => {
      const randomKelas = ['VI A', 'VI B', 'V A', 'V B', 'IV A', 'IV B'][user.id % 6]
      return {
        ...user,
        nisn: '10' + user.id.toString().padStart(4, '0') + Math.floor(1000 + Math.random() * 9000),
        kelas: randomKelas
      }
    })
  } catch (error) {
    toast.error('Gagal memuat data siswa.')
  } finally {
    isLoadingSiswa.value = false
  }
})

function addSiswaField() {
  selectedSiswa.value.push({ 
    id: Date.now().toString() + Math.random(), 
    siswaId: null, 
    nama: '', 
    nisn: '', 
    kelas: '' 
  })
}

function removeSiswaField(index) {
  if (selectedSiswa.value.length > 1) {
    selectedSiswa.value.splice(index, 1)
  }
}

function onSiswaSelected(index, siswaId) {
  const siswa = siswaOptions.value.find(s => s.id === parseInt(siswaId))
  if (siswa) {
    selectedSiswa.value[index].siswaId = siswa.id
    selectedSiswa.value[index].nama = siswa.name
    selectedSiswa.value[index].nisn = siswa.nisn
    selectedSiswa.value[index].kelas = siswa.kelas
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
  isEditMode.value = false
  formItem.value = {
    id: '',
    tanggalAwal: '',
    tanggalAkhir: '',
    perihal: ''
  }
  selectedSiswa.value = [
    { id: Date.now().toString(), siswaId: null, nama: '', nisn: '', kelas: '' }
  ]
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
    tanggalAwal: item.tanggalAwal,
    tanggalAkhir: item.tanggalAkhir,
    perihal: item.perihal
  }
  
  if (item.siswa) {
    item.siswa.forEach(s => {
      if (!siswaOptions.value.some(o => o.name === s.nama)) {
        siswaOptions.value.push({
          id: -Math.floor(Math.random() * 1000000) - 1,
          name: s.nama,
          nisn: s.nisn,
          kelas: s.kelas
        })
      }
    })
  }
  
  if (item.siswa && item.siswa.length > 0) {
    selectedSiswa.value = item.siswa.map(s => {
      // Find matching siswa in options to set selected ID
      const opt = siswaOptions.value.find(o => o.name === s.nama)
      return {
        id: String(Math.random()),
        siswaId: opt ? opt.id : null,
        nama: s.nama,
        nisn: s.nisn,
        kelas: s.kelas
      }
    })
  } else {
    selectedSiswa.value = [
      { id: Date.now().toString(), siswaId: null, nama: '', nisn: '', kelas: '' }
    ]
  }
  isFormSheetOpen.value = true
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

function handleSave() {
  const validSiswa = selectedSiswa.value.filter(s => s.siswaId !== null)
  if (validSiswa.length === 0) {
    toast.error('Pilih minimal 1 siswa.')
    return
  }
  if (!formItem.value.tanggalAwal || !formItem.value.tanggalAkhir || !formItem.value.perihal) {
    toast.error('Mohon lengkapi Tanggal Izin dan Perihal.')
    return
  }

  const payload = {
    tanggalAwal: formItem.value.tanggalAwal,
    tanggalAkhir: formItem.value.tanggalAkhir,
    perihal: formItem.value.perihal,
    siswa: validSiswa.map(s => ({
      nama: s.nama,
      nisn: s.nisn,
      kelas: s.kelas
    }))
  }

  try {
    if (isEditMode.value) {
      store.update(formItem.value.id, payload)
      toast.success('Surat dispensasi berhasil diperbarui!')
    } else {
      store.add(payload)
      toast.success('Surat dispensasi berhasil dibuat!')
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
        illustration="paper_plane"
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
    
    <!-- Form Sheet (Inline Create/Edit) -->
    <Sheet v-model:open="isFormSheetOpen">
      <SheetContent :show-close-button="false" class="sm:max-w-[500px] flex flex-col h-full gap-2">
        <SheetHeader class="border-b border-border pb-3 text-left">
          <SheetTitle class="text-base font-bold text-foreground">
            {{ isEditMode ? 'Edit Surat Dispensasi' : 'Buat Surat Dispensasi' }}
          </SheetTitle>
          <SheetDescription class="text-xs text-muted-foreground">
            {{ isEditMode ? 'Perbarui informasi surat dispensasi siswa.' : 'Lengkapi formulir di bawah ini untuk menerbitkan surat dispensasi baru.' }}
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
                  <!-- Siswa List Selection -->
                  <div class="space-y-3">
                    <label class="text-xs font-semibold text-muted-foreground">Siswa Terpilih</label>
                    <div class="space-y-3">
                      <div v-for="(item, index) in selectedSiswa" :key="item.id" class="flex items-center gap-2 w-full">
                        <div class="flex-1">
                          <Select 
                            :modelValue="item.siswaId ? item.siswaId.toString() : undefined"
                            @update:modelValue="(val) => onSiswaSelected(index, val)" 
                            :disabled="isLoadingSiswa"
                          >
                            <SelectTrigger class="h-10 rounded-xl">
                              <SelectValue :placeholder="isLoadingSiswa ? 'Memuat data siswa...' : 'Pilih Siswa...'" />
                            </SelectTrigger>
                            <SelectContent>
                              <SelectItem 
                                v-for="siswa in siswaOptions" 
                                :key="siswa.id" 
                                :value="siswa.id.toString()"
                              >
                                {{ siswa.name }} - {{ siswa.nisn }}
                              </SelectItem>
                            </SelectContent>
                          </Select>
                        </div>
                        
                        <Button 
                          v-if="selectedSiswa.length > 1" 
                          variant="ghost" 
                          size="xs" 
                          class="h-10 text-destructive hover:bg-destructive/10 rounded-xl px-2.5"
                          @click="removeSiswaField(index)"
                        >
                          <Trash2 class="size-4" />
                        </Button>
                      </div>

                      <Button 
                        type="button" 
                        variant="outline" 
                        size="xs"
                        class="h-9 w-full flex items-center gap-1.5 bg-muted/20 border-border rounded-xl text-[11px]"
                        @click="addSiswaField"
                      >
                        <Plus class="size-3.5" />
                        Tambah Siswa Lainnya
                      </Button>
                    </div>
                  </div>

                  <!-- Tanggal Awal & Akhir -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                      <label class="text-xs font-semibold text-muted-foreground">Tanggal Izin Awal</label>
                      <Input type="date" v-model="formItem.tanggalAwal" class="h-10 rounded-xl" />
                    </div>

                    <div class="space-y-1.5">
                      <label class="text-xs font-semibold text-muted-foreground">Tanggal Izin Akhir</label>
                      <Input type="date" v-model="formItem.tanggalAkhir" class="h-10 rounded-xl" />
                    </div>
                  </div>

                  <!-- Perihal -->
                  <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-muted-foreground">Perihal Izin</label>
                    <Textarea v-model="formItem.perihal" placeholder="Masukkan keterangan perihal izin dispensasi..." class="min-h-24 rounded-xl resize-none" />
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
    <SuratDispensasiPrintModal v-model:open="isPrintModalOpen" :data="selectedSurat" />
  </div>
</template>
