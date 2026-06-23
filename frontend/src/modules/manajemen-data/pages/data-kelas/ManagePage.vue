<script setup>
import DataTableCard from '@/components/data-table/DataTableCard.vue';
import PageHeader from '@/components/page-header/PageHeader.vue'
import { Plus } from 'lucide-vue-next';
import { allItems, classes, columns, filters, guruOptions } from './data/siswaTersedia';
import { usePagination } from '@/composables/usePagination';
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import Button from '@/components/ui/button/Button.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import FormSelect from '@/components/forms/FormSelect.vue';
import Progress from '@/components/ui/progress/Progress.vue';
import FormSheet from '@/components/data-sheet/FormSheet.vue';
import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue';
import AlertDialogContent from '@/components/ui/alert-dialog/AlertDialogContent.vue';
import AlertDialogHeader from '@/components/ui/alert-dialog/AlertDialogHeader.vue';
import AlertDialogTitle from '@/components/ui/alert-dialog/AlertDialogTitle.vue';
import AlertDialogDescription from '@/components/ui/alert-dialog/AlertDialogDescription.vue';
import AlertDialogFooter from '@/components/ui/alert-dialog/AlertDialogFooter.vue';
import AlertDialogCancel from '@/components/ui/alert-dialog/AlertDialogCancel.vue';
import AlertDialogAction from '@/components/ui/alert-dialog/AlertDialogAction.vue';


const perPage = ref(10)
const tableItems = ref([...allItems.value])
const selectedStudents = ref([])
const selectedClass = ref('1-A')

const filterValues = ref({
  search: '',
  status: 'all'
})

const isOpenWaliKelas = ref(false)
const selectedItem = ref(null)
const isSaving = ref(false)

const openChangeWaliKelas = () => {
  selectedItem.value = {
    kelasId: 'K0001',
    kelas: '1-A',
    waliKelasId: 'G0005'
  }

  isOpenWaliKelas.value = true
}

const filteredItems = computed(() => {
  return tableItems.value.filter(item => {
    const searchVal = filterValues.value.search?.trim().toLowerCase() || ''
    const searchMatch =
      !searchVal ||
      item.nama.toLowerCase().includes(searchVal)

    const statusVal = filterValues.value.status
    const statusMatch = !statusVal || statusVal === 'all' || item.status === statusVal

    return searchMatch && statusMatch
  })
})

const { currentPage, total, from, to, paginatedItems } = usePagination(filteredItems, perPage)

const actions = computed(() => [
  {
    label: `Tempatkan ke Kelas (${selectedStudents.value.length})`,
    icon: Plus,
    disabled: selectedStudents.value.length === 0,
    click: openPlacementConfirmation
  }
])

const waliKelasSections = [
  {
    title: 'Informasi Wali Kelas',
    fields: [
      {
        name: 'waliKelasId',
        label: 'Wali Kelas',
        type: 'select',
        options: guruOptions,
        required: true
      }
    ]
  }
]

const handleSubmitWaliKelas = formData => {
  isSaving.value = true

    isSaving.value = false
    isOpenWaliKelas.value = false

    toast.success('Wali Kelas Berhasil Diperbarui')
}

const isConfirmOpen = ref(false)

const openPlacementConfirmation = () => {
  isConfirmOpen.value = true
}

const placeStudentsToClass = () => {
  const selectedIds = selectedStudents.value.map(
    student => student.id
  )

  tableItems.value = tableItems.value.map(student => {
    if (selectedIds.includes(student.id)) {
      return {
        ...student,
        kelas: selectedClass.value
      }
    }

    return student
  })

  toast.success('Penempatan Berhasil', {
    description: `${selectedStudents.value.length} siswa berhasil ditempatkan ke kelas ${selectedClass.value}.`
  })

  selectedStudents.value = []
}


</script>

<template>
    <div class="space-y-6 w-full mx-auto px-0 mb-6">
        <PageHeader
            back
            title="Kelola Anggota Kelas"
            description="Tambahkan atau pindahkan siswa ke kelas"
        />
    </div>

    <div class="grid gap-6 md:grid-cols-4">
      <!-- Kolom Kiri: Overview Kelas -->
      <div class="space-y-6">
        <Card>
        <CardHeader>
            <CardTitle class="text-lg">Penempatan Kelas</CardTitle>
            <CardDescription>
            Kelas tujuan penempatan siswa
            </CardDescription>
        </CardHeader>
        <Separator/>
        <CardContent class="space-y-4">
            <FormSelect label="Pilih Kelas" :options="classes" v-model="selectedClass"/>

            <div class="border rounded-lg p-4">
                <div class="flex justify-between mb-2">
                    <span>Kapasitas</span>
                    <span>25 / 30</span>
                </div>

                <Progress :model-value="85"
          :delay="computedDelay + 250"
          class="h-1 sm:h-2" />
            </div>

            <div class="grid grid-cols-2 gap-2">
            <div class="border rounded-lg p-3 text-center">
                <p class="text-xs">LAKI-LAKI</p>
                <p class="font-bold text-lg text-primary">13</p>
            </div>

            <div class="border rounded-lg p-3 text-center">
                <p class="text-xs text-muted-foreground">PEREMPUAN</p>
                <p class="font-bold text-lg text-primary">12</p>
            </div>
            </div>
        </CardContent>
        </Card>
        
        <Card>
          <CardHeader>
            <CardTitle class="text-lg">Wali Kelas</CardTitle>
            <CardDescription>Informasi wali kelas 1-A</CardDescription>
          </CardHeader>
          <Separator/>
          <CardContent>
            <div class="flex items-center gap-4 py-1">
                <Avatar  class="shrink-0 size-20">
                    <AvatarImage src="https://i.pravatar.cc/150?img=12" alt="Foto Profil" />
                    <AvatarFallback class="text-[11px] font-bold"> AA </AvatarFallback>
                </Avatar>
                <div>
                    <p class="font-semibold text-base mb-1">Fajar Mukarami, S.Pd.</p>
                    <p class="text-xs text-muted-foreground mb-1">GURU BAHASA INDONESIA</p>
                    <p class="font-medium text-sm text-primary mb-1">NIP: 0123456789</p>
                </div>
            </div>
            
          </CardContent>
          <CardFooter class="flex justify-end gap-2">
            <Button @click="openChangeWaliKelas">Ubah Wali Kelas</Button>
          </CardFooter>
        </Card>
      </div>

      <!-- Kolom Kanan: Tabel daftar siswa -->
      <div class="md:col-span-3 space-y-6">
        <DataTableCard
            selectable
            v-model:selectedRows="selectedStudents"
            :rowDisabled="item => item.kelas === selectedClass"
            :actions="actions"
            :columns="columns"
            :items="paginatedItems"
            :filters="filters"
            v-model:filterValues="filterValues"
            v-model:perPage="perPage"
            illustration="globe"
            :from="from"
            :to="to"
            :total="total"
            :page="currentPage"
            @update:page="currentPage = $event"
        >
            <template #cell-kehadiran="{ item }">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                <span class="text-sm font-medium" :class="item.kehadiran >= 100 ? 'text-primary' : 'text-amber-600'">
                    {{ item.kehadiran }}
                </span>
                <span class="text-muted-foreground text-xs">%</span>
                </div>
                <div class="w-full bg-secondary rounded-full h-1.5 overflow-hidden">
                <div 
                    class="h-full rounded-full transition-all duration-300"
                    :class="item.kehadiran >= 100 ? 'bg-primary' : 'bg-amber-500'"
                    :style="{ width: `${Math.min((item.kehadiran / 100) * 100, 100)}%` }"
                ></div>
                </div>
            </div>
            </template>

            <template #cell-kelas="{ item }">
              <div class="flex justify-center">
                <span :class="{
                  'text-red-500': item.kelas === 'Belum Ada Kelas',
                  'font-bold': item.kelas === selectedClass}"
                >
                {{ item.kelas }}
                </span>
              </div>
            </template>   
        </DataTableCard>

      </div>
    </div>

    <FormSheet
      v-model:open="isOpenWaliKelas"
      :item="selectedItem"
      title="Ubah Wali Kelas"
      description="Pilih guru yang akan ditetapkan sebagai wali kelas."
      :sections="waliKelasSections"
      :loading="isSaving"
      @submit="handleSubmitWaliKelas"
      avatarKey="false"
    />

        <AlertDialog v-model:open="isConfirmOpen">
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>
            Konfirmasi Penempatan Siswa
          </AlertDialogTitle>

          <AlertDialogDescription>
            <strong>{{ selectedStudents.length }} siswa</strong>
            akan ditempatkan ke kelas
            <strong>{{ selectedClass }}</strong>.
            <br /><br />
            Siswa yang berasal dari kelas lain akan dipindahkan dari kelas sebelumnya.
          </AlertDialogDescription>
        </AlertDialogHeader>

        <AlertDialogFooter>
          <AlertDialogCancel>
            Batal
          </AlertDialogCancel>

          <AlertDialogAction
            @click="placeStudentsToClass"
          >
            Tempatkan
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
</template>