<script setup>
import { ref, onMounted } from 'vue'
import { toast } from 'vue-sonner'
import AnnouncementTable from '../../../components/AnnouncementTable.vue'
import AnnouncementDetailModal from '../../../components/AnnouncementDetailModal.vue'
import AnnouncementFormModal from '../../../components/AnnouncementFormModal.vue'
import AnnouncementDeleteModal from '../../../components/AnnouncementDeleteModal.vue'
import { useAuthStore } from '@/stores/authStore'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Plus, Megaphone } from 'lucide-vue-next'
import { glassSlide, glassFade } from '@/config/motion'
import {
  getAnnouncements,
  createAnnouncement,
  updateAnnouncement,
  deleteAnnouncement as deleteAnnouncementApi
} from '@/services/announcementService'

const authStore = useAuthStore()
const currentUser = authStore.user

// Determine school scope based on user role to avoid modifying the auth store itself
const isSchoolRole = currentUser?.role === 'kepala_sekolah' || currentUser?.role === 'admin_sekolah'

// --- State Variables ---
const announcements = ref([])
const loading = ref(false)
const isFormOpen = ref(false)
const isDetailOpen = ref(false)
const isDeleteConfirmOpen = ref(false)
const selectedAnnouncement = ref(null)
const formMode = ref('create')

// --- API Loading ---
async function loadAnnouncements() {
  loading.value = true
  try {
    const data = await getAnnouncements()
    announcements.value = data.map(item => ({
      id: item.id,
      judul: item.title,
      deskripsi: item.content,
      kategori: item.category,
      sekolah: item.target_school ? item.target_school.name : 'SEMUA SEKOLAH',
      tanggal: item.publish_date,
      target_school_id: item.target_school_id
    }))
  } catch (error) {
    console.error('Gagal memuat pengumuman:', error)
    toast.error('Gagal mengambil data pengumuman.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadAnnouncements()
})

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

async function saveAnnouncement(payload) {
  try {
    const body = {
      title: payload.judul,
      content: payload.deskripsi,
      category: payload.kategori,
      target_school_id: payload.target_school_id || null,
      publish_date: payload.tanggal
    }

    if (formMode.value === 'create') {
      await createAnnouncement(body)
      toast.success('Pengumuman baru dipublikasikan!')
    } else {
      await updateAnnouncement(payload.id, body)
      toast.success('Pengumuman berhasil disimpan!')
    }
    loadAnnouncements()
    isFormOpen.value = false
  } catch (error) {
    console.error('Gagal menyimpan pengumuman:', error)
    toast.error('Gagal mempublikasikan pengumuman.')
  }
}

async function deleteAnnouncement() {
  if (!selectedAnnouncement.value) return
  try {
    await deleteAnnouncementApi(selectedAnnouncement.value.id)
    toast.success('Pengumuman berhasil dihapus!')
    loadAnnouncements()
    isDeleteConfirmOpen.value = false
    selectedAnnouncement.value = null
  } catch (error) {
    console.error('Gagal menghapus pengumuman:', error)
    toast.error('Gagal menghapus pengumuman.')
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
        illustration="paper_plane"
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
