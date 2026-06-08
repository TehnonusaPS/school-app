<script setup>
import { ref } from 'vue'
import { toast } from 'vue-sonner'
import { mockAnnouncements } from '../../../data/mockAnnouncements'
import AnnouncementTable from '../../../components/AnnouncementTable.vue'
import AnnouncementDetailModal from '../../../components/AnnouncementDetailModal.vue'
import AnnouncementFormModal from '../../../components/AnnouncementFormModal.vue'
import AnnouncementDeleteModal from '../../../components/AnnouncementDeleteModal.vue'
import { useAuthStore } from '@/stores/authStore'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Plus, Megaphone } from 'lucide-vue-next'
import { glassSlide, glassFade } from '@/config/motion'

const authStore = useAuthStore()
const currentUser = authStore.user

// Determine school scope based on user role to avoid modifying the auth store itself
const isSchoolRole = currentUser?.role === 'kepala_sekolah' || currentUser?.role === 'admin_sekolah'
const userSchool = isSchoolRole
  ? '8D Tehnonusa I'
  : currentUser?.sekolah

// Filter announcements so that school-scoped users (like Kepala Sekolah) only see their school's data and "SEMUA SEKOLAH"
const filteredMockData = userSchool
  ? mockAnnouncements.filter(item => item.sekolah === userSchool || item.sekolah === 'SEMUA SEKOLAH')
  : mockAnnouncements

// --- State Variables ---
const announcements = ref([...filteredMockData])
const isFormOpen = ref(false)
const isDetailOpen = ref(false)
const isDeleteConfirmOpen = ref(false)
const selectedAnnouncement = ref(null)
const formMode = ref('create')

// --- CRUD Actions ---
function openCreateDialog() {
  formMode.value = 'create'
  selectedAnnouncement.value = null
  isFormOpen.value = true
}

function openEditDialog(item) {
  formMode.value = 'edit'
  selectedAnnouncement.value = item
  isFormOpen.value = true
}

function openDetailDialog(item) {
  selectedAnnouncement.value = item
  isDetailOpen.value = true
}

function confirmDelete(item) {
  selectedAnnouncement.value = item
  isDeleteConfirmOpen.value = true
}

function saveAnnouncement(payload) {
  if (formMode.value === 'create') {
    announcements.value.unshift({
      id: Date.now(),
      judul: payload.judul,
      deskripsi: payload.deskripsi,
      kategori: payload.kategori,
      sekolah: payload.sekolah,
      tanggal: payload.tanggal
    })
    toast.success('Pengumuman baru dipublikasikan!')
  } else {
    const index = announcements.value.findIndex(a => a.id === payload.id)
    if (index !== -1) {
      announcements.value[index] = {
        ...announcements.value[index],
        judul: payload.judul,
        deskripsi: payload.deskripsi,
        kategori: payload.kategori,
        sekolah: payload.sekolah,
        tanggal: payload.tanggal
      }
      toast.success('Pengumuman berhasil disimpan!')
    }
  }
  isFormOpen.value = false
}

function deleteAnnouncement() {
  if (!selectedAnnouncement.value) return
  announcements.value = announcements.value.filter(a => a.id !== selectedAnnouncement.value.id)
  toast.success('Pengumuman berhasil dihapus!')
  isDeleteConfirmOpen.value = false
  selectedAnnouncement.value = null
}
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 p-1"
  >
    <!-- Header & Stats Card component -->
    <PageHeader
      title="Daftar Pengumuman"
      description="Kelola dan Kirim informasi ke seluruh unit sekolah"
      :actions="!isSchoolRole ? [
        {
          label: 'Buat Pengumuman Baru',
          icon: Plus,
          variant: 'default',
          click: openCreateDialog
        }
      ] : []"
    />

    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
    >
      <StatCard
        label="Total Pengumuman"
        :value="announcements.length"
        :icon="Megaphone"
        variant="primary"
      />
    </div>

    <!-- Main Data Table & Filtering & Pagination -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
    >
      <AnnouncementTable 
        :items="announcements" 
        :is-school-role="isSchoolRole"
        @view="openDetailDialog" 
        @edit="openEditDialog" 
        @delete="confirmDelete" 
      />
    </div>

    <!-- Dialog / Modals components -->
    <AnnouncementDetailModal 
      v-model:open="isDetailOpen" 
      :announcement="selectedAnnouncement" 
    />

    <AnnouncementFormModal 
      v-model:open="isFormOpen" 
      :mode="formMode" 
      :announcement="selectedAnnouncement" 
      @save="saveAnnouncement" 
    />

    <AnnouncementDeleteModal 
      v-model:open="isDeleteConfirmOpen" 
      :announcement="selectedAnnouncement" 
      @confirm="deleteAnnouncement" 
    />
  </div>
</template>
