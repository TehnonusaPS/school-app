<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter
} from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import {
  Info,
  ArrowRight,
  Filter,
  Download,
  Edit,
  Lock,
  Building,
  School,
  X,
  Save,
  Plus,
  Trash2,
  Settings,
  Image,
  BookOpen,
  Layers,
  PhoneCall,
  GripVertical,
  Eye
} from 'lucide-vue-next'
import VueDraggable from 'vuedraggable'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const router = useRouter()

const activeTab = ref('sekolah') // 'sekolah' atau 'yayasan'
const isModalOpen = ref(false)
const selectedItemForEdit = ref(null)

// Tab aktif di dalam modal editor asisten
const modalActiveTab = ref('general') // 'general', 'hero', 'about', 'sections', 'contact'

const themeOptions = [
  {
    id: 'modern',
    name: '🔵 Modern Akademik',
    desc: 'Clean, minimalis, profesional dengan glassmorphism & parallax.'
  },
  {
    id: 'islami',
    name: '🟢 Islami Elegant',
    desc: 'Warna emerald-emas yang hangat dengan geometri ornamentik islami.'
  },
  {
    id: 'playful',
    name: '🟡 Colorful Playful',
    desc: 'Sangat cocok untuk TK/SD dengan bentuk wavy, blob, dan emoji ceria.'
  }
]

// Form state lengkap untuk modal editor
const form = ref({
  theme: 'modern',
  slug: '',
  meta_title: '',
  meta_description: '',
  primary_color: '#7c3aed',
  secondary_color: '#f59e0b',
  accent_color: '#06b6d4',
  hero_title: '',
  hero_subtitle: '',
  hero_description: '',
  hero_cta_text: '',
  hero_cta_link: '',
  hero_images: [],
  about_image: '',
  about_title: '',
  about_description: '',
  about_vision: '',
  about_mission: [],
  contact_email: '',
  contact_phone: '',
  contact_address: '',
  contact_maps_embed: '',
  social_instagram: '',
  social_facebook: '',
  social_youtube: '',
  social_tiktok: '',
  sections: []
})

// Temp state untuk mengedit/menambah item section di dalam modal
const newMissionItem = ref('')
const selectedSectionIndex = ref(null)
const editingSectionItem = ref(null)
const isAddingSectionItem = ref(false)
const sectionItemForm = ref({
  title: '',
  description: '',
  icon: 'star',
  image: '',
  value: ''
})

// Methods untuk upload hero (prototipe frontend)
function onHeroImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  const fakeUrl = URL.createObjectURL(file)
  if (!form.value.hero_images) form.value.hero_images = []
  form.value.hero_images.push({ url: fakeUrl, caption: '' })
  toast.success('Gambar berhasil ditambahkan ke carousel!')
}

function removeHeroImage(index) {
  form.value.hero_images.splice(index, 1)
}

function onAboutImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  form.value.about_image = URL.createObjectURL(file)
  toast.success('Foto profil berhasil diupload!')
}

function onSectionItemImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  sectionItemForm.value.image = URL.createObjectURL(file)
  toast.success('Gambar item berhasil diupload!')
}

// Helper dummy item data generator
const createDefaultSections = () => [
  {
    id: 1,
    type: 'stats',
    title: 'Sekolah Kami Dalam Angka',
    is_visible: true,
    sort_order: 1,
    items: [
      { id: 11, title: 'Siswa Aktif', value: '850+' },
      { id: 12, title: 'Guru Profesional', value: '45' },
      { id: 13, title: 'Kelas / Rombel', value: '24' },
      { id: 14, title: 'Akreditasi', value: 'A+' }
    ]
  },
  {
    id: 2,
    type: 'features',
    title: 'Mengapa Memilih Kami?',
    is_visible: true,
    sort_order: 2,
    items: [
      {
        id: 21,
        title: 'Guru Tersertifikasi',
        description: 'Tenaga pendidik profesional lulusan universitas ternama yang ramah dan kompeten.',
        icon: 'award'
      },
      {
        id: 22,
        title: 'Kurikulum Modern',
        description: 'Kurikulum nasional yang diperkaya dengan program bilingual dan penguatan karakter.',
        icon: 'book-open'
      },
      {
        id: 23,
        title: 'Fasilitas Premium',
        description: 'Ruang kelas ber-AC, laboratorium sains, ruang IT komputer, dan lapangan olahraga indoor.',
        icon: 'shield'
      }
    ]
  },
  {
    id: 3,
    type: 'programs',
    title: 'Program Unggulan',
    is_visible: true,
    sort_order: 3,
    items: [
      {
        id: 31,
        title: 'Bilingual Class (English & Indonesia)',
        description: 'Pengenalan bahasa Inggris aktif sejak dini untuk melatih rasa percaya diri anak.',
        image: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=600'
      },
      {
        id: 32,
        title: 'Coding & Robotics Kid Club',
        description: 'Mengasah logika berpikir komputasional siswa melalui kelas merakit robot seru.',
        image: 'https://images.unsplash.com/photo-1561557944-6e7860d1a7eb?q=80&w=600'
      }
    ]
  },
  {
    id: 4,
    type: 'gallery',
    title: 'Galeri Kegiatan Belajar',
    is_visible: true,
    sort_order: 4,
    items: [
      {
        id: 41,
        title: 'Keseruan Belajar Outbound',
        image: 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?q=80&w=600'
      },
      {
        id: 42,
        title: 'Pentas Seni Tahunan',
        image: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=600'
      },
      {
        id: 43,
        title: 'Kelas Eksperimen Sains',
        image: 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600'
      }
    ]
  },
  {
    id: 5,
    type: 'testimonials',
    title: 'Apa Kata Orang Tua Murid?',
    is_visible: true,
    sort_order: 5,
    items: [
      {
        id: 51,
        title: 'Bunda Larasati',
        description: 'Sangat senang menyekolahkan anak di sini. Gurunya peduli perkembangan anak.',
        value: 'Wali Murid Kelas 3'
      },
      {
        id: 52,
        title: 'Bapak Hendra',
        description: 'Fasilitas sangat memadai, lingkungan aman, dan ekstrakurikulernya banyak!',
        value: 'Wali Murid Kelas 5'
      }
    ]
  },
  {
    id: 6,
    type: 'faq',
    title: 'Pertanyaan yang Sering Diajukan',
    is_visible: true,
    sort_order: 6,
    items: [
      {
        id: 61,
        title: 'Kapan jadwal pendaftaran siswa baru dibuka?',
        description: 'Pendaftaran gelombang 1 dibuka bulan Januari - Maret setiap tahunnya.'
      },
      {
        id: 62,
        title: 'Apakah tersedia layanan antar jemput?',
        description: 'Tentu, kami bekerja sama dengan layanan transportasi terpercaya.'
      }
    ]
  }
]

// Mock Data Yayasan
const foundationMappings = ref([
  {
    id: 'FDN-2023-001',
    name: 'Yayasan Pendidikan Nusantara',
    template: 'Modern Institutional',
    status: 'Aktif',
    landing_page_enabled: true,
    is_published: true,
    lastUpdated: '12 Okt 2023, 14:30',
    avatarLetter: 'YN',
    avatarColor: 'bg-indigo-500/10 text-indigo-500 border border-indigo-500/20',
    indicatorColor: 'bg-emerald-500',
    theme: 'modern',
    slug: 'yayasan-nusantara',
    meta_title: 'Yayasan Pendidikan Nusantara',
    meta_description: 'Mewujudkan pendidikan berstandar tinggi di Indonesia.',
    primary_color: '#1e40af',
    secondary_color: '#f59e0b',
    accent_color: '#0ea5e9',
    hero_title: 'Membangun Pendidikan Berkualitas',
    hero_subtitle: 'Yayasan Pendidikan Nusantara',
    hero_description: 'Kami berkomitmen menaungi sekolah-sekolah unggulan masa depan.',
    about_title: 'Tentang Yayasan',
    about_description:
      'Berdiri sejak tahun 2005 dengan fokus utama pada pembiayaan dan manajemen kurikulum.',
    about_vision: 'Mewujudkan sistem pendidikan berdaya saing global.',
    about_mission: ['Meningkatkan kualitas SDM pendidik.', 'Menyediakan fasilitas belajar modern.'],
    contact_email: 'info@nusantara.org',
    contact_phone: '+62 21-555-1234',
    contact_address: 'Gedung Nusantara Lt. 5, Jakarta',
    contact_maps_embed: '',
    social_instagram: '',
    social_facebook: '',
    social_youtube: '',
    social_tiktok: '',
    sections: createDefaultSections()
  }
])

// Mock Data Sekolah
const schoolMappings = ref([
  {
    id: 'SCH-2023-001',
    name: 'SMA Nusantara',
    template: 'Modern Institutional',
    status: 'Aktif',
    landing_page_enabled: true,
    is_published: false,
    lastUpdated: '12 Okt 2023, 14:30',
    avatarLetter: 'SN',
    avatarColor: 'bg-violet-500/10 text-violet-500 border border-violet-500/20',
    indicatorColor: 'bg-emerald-500',
    theme: 'modern',
    slug: 'sma-nusantara',
    meta_title: 'SMA Nusantara Unggul',
    meta_description: 'Sekolah menengah atas terbaik se-DKI Jakarta.',
    primary_color: '#7c3aed',
    secondary_color: '#f59e0b',
    accent_color: '#06b6d4',
    hero_title: 'Sekolah Masa Depan Anda',
    hero_subtitle: 'SMA Nusantara Terakreditasi A',
    hero_description: 'Mari bergabung dengan ekosistem belajar yang modern dan inovatif.',
    about_title: 'Profil Singkat',
    about_description:
      'SMA Nusantara fokus melatih siswa berpikir analitis, berdaya saing global, dan berkarakter Pancasila.',
    about_vision: 'Melahirkan pemimpin masa depan berkarakter.',
    about_mission: ['Menerapkan model active learning.', 'Mengembangkan minat olahraga dan seni.'],
    contact_email: 'info@smanusantara.sch.id',
    contact_phone: '+62 812-3456-7890',
    contact_address: 'Jl. Pendidikan No. 12, Jakarta',
    contact_maps_embed: '',
    social_instagram: 'https://instagram.com/smanusantara',
    social_facebook: '',
    social_youtube: '',
    social_tiktok: 'https://tiktok.com/@smanusantara',
    sections: createDefaultSections()
  }
])

const filterValues = ref({})
const page = ref(1)
const perPage = ref(5)

const columns = [
  { key: 'name', label: 'NAMA INSTANSI' },
  { key: 'template', label: 'TEMPLATE SAAT INI' },
  { key: 'access', label: 'AKSES BUILDER' },
  { key: 'status', label: 'STATUS' },
  { key: 'lastUpdated', label: 'TERAKHIR DIPERBARUI' },
  { key: 'actions', label: 'AKSI' }
]

const filters = [{ key: 'search', type: 'search', placeholder: 'Cari nama...' }]
const actions = [
  {
    label: 'Filter',
    icon: Filter,
    variant: 'outline',
    class: 'border-white/10 text-foreground bg-white/5 hover:bg-white/10'
  }
]

const currentItems = computed(() => {
  return activeTab.value === 'sekolah' ? schoolMappings.value : foundationMappings.value
})

const paginatedItems = computed(() => {
  const start = (page.value - 1) * perPage.value
  return currentItems.value.slice(start, start + perPage.value)
})

const showAccessDialog = ref(false)
const itemToToggleAccess = ref(null)

const confirmToggleAccess = item => {
  itemToToggleAccess.value = item
  showAccessDialog.value = true
}

const executeToggleAccess = () => {
  if (itemToToggleAccess.value) {
    itemToToggleAccess.value.landing_page_enabled = !itemToToggleAccess.value.landing_page_enabled
    toast.success(`Akses untuk ${itemToToggleAccess.value.name} berhasil diubah!`)
    showAccessDialog.value = false
    itemToToggleAccess.value = null
  }
}

const showPublishDialog = ref(false)

const confirmTogglePublish = () => {
  showPublishDialog.value = true
}

const executeTogglePublish = () => {
  if (selectedItemForEdit.value) {
    selectedItemForEdit.value.is_published = !selectedItemForEdit.value.is_published
    toast.success(`Status publikasi untuk ${selectedItemForEdit.value.name} berhasil diubah!`)
    showPublishDialog.value = false
  }
}

// Buka Modal & Load data detail ke form secara komprehensif
const openModalEditor = item => {
  selectedItemForEdit.value = item
  modalActiveTab.value = 'general'
  form.value = {
    theme: item.theme || 'modern',
    slug: item.slug || '',
    meta_title: item.meta_title || '',
    meta_description: item.meta_description || '',
    primary_color: item.primary_color || '#7c3aed',
    secondary_color: item.secondary_color || '#f59e0b',
    accent_color: item.accent_color || '#06b6d4',
    hero_title: item.hero_title || '',
    hero_subtitle: item.hero_subtitle || '',
    hero_description: item.hero_description || '',
    about_title: item.about_title || '',
    about_description: item.about_description || '',
    about_vision: item.about_vision || '',
    about_mission: [...(item.about_mission || [])],
    contact_email: item.contact_email || '',
    contact_phone: item.contact_phone || '',
    contact_address: item.contact_address || '',
    contact_maps_embed: item.contact_maps_embed || '',
    social_instagram: item.social_instagram || '',
    social_facebook: item.social_facebook || '',
    social_youtube: item.social_youtube || '',
    social_tiktok: item.social_tiktok || '',
    sections: JSON.parse(JSON.stringify(item.sections || createDefaultSections()))
  }
  isModalOpen.value = true
}

// Simpan data lengkap dari modal editor asisten ke baris instansi
const saveModalData = () => {
  if (selectedItemForEdit.value) {
    const item = selectedItemForEdit.value
    Object.assign(item, form.value)
    item.template =
      form.value.theme === 'modern'
        ? 'Modern Institutional'
        : form.value.theme === 'islami'
          ? 'Classic Academic'
          : 'Modern Playful'
    item.lastUpdated = 'Baru saja'

    isModalOpen.value = false
    toast.success(`Konfigurasi landing page lengkap untuk ${item.name} disimpan!`)
  }
}

// Helper Misi
const addMission = () => {
  if (newMissionItem.value.trim()) {
    form.value.about_mission.push(newMissionItem.value.trim())
    newMissionItem.value = ''
  }
}
const removeMission = idx => {
  form.value.about_mission.splice(idx, 1)
}

// Handler CRUD Item Section di dalam Modal
const openSectionItemEditor = (sectionIdx, item = null) => {
  selectedSectionIndex.value = sectionIdx
  if (item) {
    editingSectionItem.value = item
    isAddingSectionItem.value = false
    sectionItemForm.value = { ...item }
  } else {
    editingSectionItem.value = null
    isAddingSectionItem.value = true
    sectionItemForm.value = { title: '', description: '', icon: 'star', image: '', value: '' }
  }
}

const saveSectionItem = () => {
  const section = form.value.sections[selectedSectionIndex.value]
  if (!section) return

  if (isAddingSectionItem.value) {
    const newItem = { id: Date.now(), ...sectionItemForm.value }
    section.items.push(newItem)
    toast.success('Item ditambahkan ke section!')
  } else {
    const idx = section.items.findIndex(i => i.id === editingSectionItem.value.id)
    if (idx !== -1) {
      section.items[idx] = { ...section.items[idx], ...sectionItemForm.value }
      toast.success('Item diperbarui!')
    }
  }
  closeSectionItemEditor()
}

const deleteSectionItem = (sectionIdx, itemId) => {
  const section = form.value.sections[sectionIdx]
  if (section) {
    section.items = section.items.filter(i => i.id !== itemId)
    toast.success('Item dihapus dari section!')
  }
}

const closeSectionItemEditor = () => {
  selectedSectionIndex.value = null
  editingSectionItem.value = null
  isAddingSectionItem.value = false
}
</script>

<template>
  <div
    v-if="auth.user?.role === 'superadmin'"
    class="space-y-8 pb-10"
  >
    <PageHeader
      title="Konfigurasi Global - Landing Page"
      description="Atur hak akses dan template website landing page untuk semua sekolah & yayasan"
    />

    <!-- Tab Selector -->
    <div class="flex gap-2 p-1.5 bg-white/5 border border-white/10 rounded-2xl max-w-sm">
      <button
        @click="
          activeTab = 'sekolah';
          page = 1;
        "
        class="flex-1 flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-bold transition-all"
        :class="
          activeTab === 'sekolah'
            ? 'bg-primary text-white shadow-md'
            : 'text-gray-400 hover:text-white'
        "
      >
        <School class="w-4 h-4" />
        Pemetaan Sekolah
      </button>
      <button
        @click="
          activeTab = 'yayasan';
          page = 1;
        "
        class="flex-1 flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-bold transition-all"
        :class="
          activeTab === 'yayasan'
            ? 'bg-primary text-white shadow-md'
            : 'text-gray-400 hover:text-white'
        "
      >
        <Building class="w-4 h-4" />
        Pemetaan Yayasan
      </button>
    </div>

    <!-- Mapping Section -->
    <div class="space-y-4">
      <DataTableCard
        :columns="columns"
        :items="paginatedItems"
        :filters="filters"
        :actions="actions"
        v-model:filterValues="filterValues"
        :page="page"
        :per-page="perPage"
        :total="currentItems.length"
        :from="(page - 1) * perPage + 1"
        :to="Math.min(page * perPage, currentItems.length)"
        @update:page="page = $event"
        @update:perPage="perPage = $event"
      >
        <template #cell-name="{ item }">
          <div class="flex items-center gap-3">
            <div
              class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-extrabold shrink-0"
              :class="item.avatarColor"
            >
              {{ item.avatarLetter }}
            </div>
            <div>
              <p class="font-bold text-sm text-foreground">{{ item.name }}</p>
              <p class="text-[10px] text-gray-400 mt-0.5">ID: {{ item.id }}</p>
            </div>
          </div>
        </template>

        <template #cell-template="{ item }">
          <div class="flex items-center gap-2">
            <span
              class="w-2 h-2 rounded-full"
              :class="item.indicatorColor"
            ></span>
            <span class="text-xs font-medium text-foreground">{{ item.template }}</span>
          </div>
        </template>

        <template #cell-access="{ item }">
          <button
            @click="confirmToggleAccess(item)"
            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
            :class="item.landing_page_enabled ? 'bg-primary' : 'bg-white/10 border-white/20'"
          >
            <span
              class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 ease-in-out"
              :class="item.landing_page_enabled ? 'translate-x-4' : 'translate-x-0'"
            />
          </button>
        </template>

        <template #cell-status="{ item }">
          <Badge
            variant="secondary"
            class="bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-medium px-2 py-0 text-[10px] rounded-lg"
          >
            {{ item.status }}
          </Badge>
        </template>

        <template #cell-actions="{ item }">
          <Button
            size="sm"
            variant="default"
            class="h-7 text-[10px] font-bold px-3 rounded-lg"
            @click="openModalEditor(item)"
          >
            Kelola Halaman
          </Button>
        </template>
      </DataTableCard>
    </div>

    <!-- MODAL EDITOR ASISTEN LENGKAP -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-background/40"
    >
      <div
        class="glass-mini border border-border/50 w-full max-w-4xl rounded-3xl overflow-hidden shadow-2xl flex flex-col h-[90vh]"
      >
        <!-- Header -->
        <div class="p-6 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
          <div>
            <h3 class="font-extrabold text-lg text-foreground flex items-center gap-2">
              <span>🛠️</span> Asisten Setup Landing Page
            </h3>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-xs text-muted-foreground">
                Konfigurasi lengkap untuk: <strong>{{ selectedItemForEdit?.name }}</strong>
              </span>
              <span class="text-border">|</span>
              <span
                class="flex h-2 w-2 rounded-full"
                :class="selectedItemForEdit?.is_published ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500'"
              ></span>
              <span class="text-[10px] font-bold uppercase tracking-wider" :class="selectedItemForEdit?.is_published ? 'text-emerald-500' : 'text-amber-500'">
                {{ selectedItemForEdit?.is_published ? 'Aktif Publik' : 'Draft Offline' }}
              </span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="confirmTogglePublish"
              class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-all bg-primary hover:bg-primary/90"
            >
              {{ selectedItemForEdit?.is_published ? 'Nonaktifkan' : 'Aktifkan Publik' }}
            </button>
            <a
              v-if="form.slug"
              :href="`/s/${form.slug}`"
              target="_blank"
              class="flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl border border-border/50 bg-muted/10 hover:bg-muted/50 font-bold text-xs transition-colors text-foreground"
            >
              <Eye class="w-3.5 h-3.5" /> Lihat Landing Page
            </a>
            <Button
              variant="ghost"
              size="icon"
              @click="isModalOpen = false"
              class="text-muted-foreground hover:text-foreground rounded-xl hover:bg-muted/50"
            >
              <X class="w-5 h-5" />
            </Button>
          </div>
        </div>

        <div class="flex-1 flex overflow-hidden">
          <!-- Left: Modal Sidebar Menu -->
          <aside
            class="w-48 border-r border-border p-4 space-y-1 shrink-0 bg-muted/10 shadow-[1px_0_0_0_theme(colors.border)]"
          >
            <Button
              @click="modalActiveTab = 'general'"
              :variant="modalActiveTab === 'general' ? 'default' : 'ghost'"
              class="w-full justify-start gap-2 rounded-xl text-xs font-bold"
            >
              <Settings class="w-4 h-4" /> Pengaturan Umum
            </Button>
            <Button
              @click="modalActiveTab = 'hero'"
              :variant="modalActiveTab === 'hero' ? 'default' : 'ghost'"
              class="w-full justify-start gap-2 rounded-xl text-xs font-bold"
            >
              <Image class="w-4 h-4" /> Hero Banner
            </Button>
            <Button
              @click="modalActiveTab = 'about'"
              :variant="modalActiveTab === 'about' ? 'default' : 'ghost'"
              class="w-full justify-start gap-2 rounded-xl text-xs font-bold"
            >
              <BookOpen class="w-4 h-4" /> Profil Tentang
            </Button>
            <Button
              @click="modalActiveTab = 'sections'"
              :variant="modalActiveTab === 'sections' ? 'default' : 'ghost'"
              class="w-full justify-start gap-2 rounded-xl text-xs font-bold"
            >
              <Layers class="w-4 h-4" /> Kelola Section
            </Button>
            <Button
              @click="modalActiveTab = 'contact'"
              :variant="modalActiveTab === 'contact' ? 'default' : 'ghost'"
              class="w-full justify-start gap-2 rounded-xl text-xs font-bold"
            >
              <PhoneCall class="w-4 h-4" /> Kontak & Sosmed
            </Button>
          </aside>

          <!-- Right: Modal Content Area -->
          <div class="flex-1 overflow-y-auto p-6">
            <!-- TAB 1: General -->
            <div
              v-if="modalActiveTab === 'general'"
              class="space-y-6"
            >
              <h4 class="text-sm font-bold text-foreground">Pengaturan Umum & Branding</h4>
              <div class="space-y-4">
                <Label class="text-xs text-muted-foreground block">Pilih Tema Visual</Label>
                <div class="grid md:grid-cols-3 gap-4">
                  <label
                    v-for="opt in themeOptions"
                    :key="opt.id"
                    class="flex flex-col p-3 rounded-xl border-2 cursor-pointer transition-all hover:bg-muted/50"
                    :class="
                      form.theme === opt.id ? 'border-primary bg-primary/10' : 'border-border'
                    "
                  >
                    <input
                      type="radio"
                      v-model="form.theme"
                      :value="opt.id"
                      class="sr-only"
                    />
                    <span class="font-bold text-xs text-foreground">{{ opt.name }}</span>
                    <span class="text-[10px] text-muted-foreground mt-1">{{ opt.desc }}</span>
                  </label>
                </div>
              </div>
              <div class="space-y-4">
                <Label class="text-xs text-muted-foreground block">Alamat Halaman (Slug URL)</Label>
                <div
                  class="flex rounded-xl shadow-sm border border-border/50 overflow-hidden bg-background/50"
                >
                  <span
                    class="bg-muted/50 text-muted-foreground px-3 py-2 text-xs flex items-center border-r border-border/50 font-bold"
                    >/s/</span
                  >
                  <Input
                    type="text"
                    v-model="form.slug"
                    class="flex-1 rounded-none border-0 text-xs focus-visible:ring-0 bg-transparent"
                    placeholder="contoh: sdit-nur-iman"
                  />
                </div>
              </div>

              <div class="grid grid-cols-3 gap-4 pt-2">
                <div>
                  <Label class="text-[10px] font-bold text-muted-foreground uppercase mb-1 block"
                    >Warna Utama</Label
                  >
                  <div class="flex items-center gap-2">
                    <Input
                      type="color"
                      v-model="form.primary_color"
                      class="w-8 h-8 p-0 border-0 rounded-lg cursor-pointer bg-transparent shrink-0"
                    />
                    <Input
                      type="text"
                      v-model="form.primary_color"
                      class="flex-1 px-2 py-1.5 text-[10px] rounded-lg uppercase bg-background/30"
                    />
                  </div>
                </div>
                <div>
                  <Label class="text-[10px] font-bold text-muted-foreground uppercase mb-1 block"
                    >Warna Sekunder</Label
                  >
                  <div class="flex items-center gap-2">
                    <Input
                      type="color"
                      v-model="form.secondary_color"
                      class="w-8 h-8 p-0 border-0 rounded-lg cursor-pointer bg-transparent shrink-0"
                    />
                    <Input
                      type="text"
                      v-model="form.secondary_color"
                      class="flex-1 px-2 py-1.5 text-[10px] rounded-lg uppercase bg-background/30"
                    />
                  </div>
                </div>
                <div>
                  <Label class="text-[10px] font-bold text-muted-foreground uppercase mb-1 block"
                    >Warna Aksen</Label
                  >
                  <div class="flex items-center gap-2">
                    <Input
                      type="color"
                      v-model="form.accent_color"
                      class="w-8 h-8 p-0 border-0 rounded-lg cursor-pointer bg-transparent shrink-0"
                    />
                    <Input
                      type="text"
                      v-model="form.accent_color"
                      class="flex-1 px-2 py-1.5 text-[10px] rounded-lg uppercase bg-background/30"
                    />
                  </div>
                </div>
              </div>
              <div class="space-y-4">
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Meta Title SEO</Label
                  ><Input
                    type="text"
                    v-model="form.meta_title"
                    class="rounded-xl text-xs"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block"
                    >Meta Description SEO</Label
                  ><Textarea
                    v-model="form.meta_description"
                    rows="2"
                    class="rounded-xl text-xs"
                  ></Textarea>
                </div>
              </div>
            </div>

            <!-- TAB 2: Hero -->
            <div
              v-if="modalActiveTab === 'hero'"
              class="space-y-4"
            >
              <h4 class="text-sm font-bold text-foreground">Konten Banner Hero</h4>
              <div>
                <Label class="text-xs text-muted-foreground mb-1.5 block"
                  >Slide Gambar Carousel</Label
                >
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                  <div
                    v-for="(img, idx) in form.hero_images"
                    :key="idx"
                    class="relative group rounded-xl overflow-hidden border border-border/50 aspect-video"
                  >
                    <img
                      :src="img.url"
                      class="w-full h-full object-cover"
                    />
                    <button
                      type="button"
                      @click="removeHeroImage(idx)"
                      class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition-opacity duration-300"
                    >
                      <Trash2 class="w-5 h-5 text-red-400" />
                    </button>
                  </div>
                  <label
                    class="flex flex-col items-center justify-center border-2 border-dashed border-border/50 rounded-xl cursor-pointer hover:bg-muted/50 aspect-video transition-colors bg-background/30"
                  >
                    <Plus class="w-6 h-6 text-muted-foreground" />
                    <span class="text-[10px] text-muted-foreground mt-2 font-bold"
                      >Tambah Gambar</span
                    >
                    <input
                      type="file"
                      @change="onHeroImageUpload"
                      class="sr-only"
                      accept="image/*"
                    />
                  </label>
                </div>
                <p class="text-[10px] text-muted-foreground mt-2">
                  Rekomendasi ukuran: 1920x1080px (Landscape). Upload lebih dari 1 untuk carousel.
                </p>
              </div>
              <div>
                <Label class="text-xs text-muted-foreground mb-1.5 block">Headline Utama</Label
                ><Input
                  type="text"
                  v-model="form.hero_title"
                  class="rounded-xl text-xs"
                />
              </div>
              <div>
                <Label class="text-xs text-muted-foreground mb-1.5 block">Sub-headline</Label
                ><Input
                  type="text"
                  v-model="form.hero_subtitle"
                  class="rounded-xl text-xs"
                />
              </div>
              <div>
                <Label class="text-xs text-muted-foreground mb-1.5 block">Deskripsi Singkat</Label
                ><Textarea
                  v-model="form.hero_description"
                  rows="3"
                  class="rounded-xl text-xs"
                ></Textarea>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Label Tombol CTA</Label
                  ><Input
                    type="text"
                    v-model="form.hero_cta_text"
                    class="rounded-xl text-xs"
                    placeholder="misal: Daftar Sekarang"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Link Tombol CTA</Label
                  ><Input
                    type="text"
                    v-model="form.hero_cta_link"
                    class="rounded-xl text-xs"
                    placeholder="misal: #registration_cta"
                  />
                </div>
              </div>
            </div>

            <!-- TAB 3: About -->
            <div
              v-if="modalActiveTab === 'about'"
              class="space-y-6"
            >
              <h4 class="text-sm font-bold text-foreground">Profil Singkat & Visi Misi</h4>
              <div class="grid md:grid-cols-3 gap-6">
                <!-- Kolom Kiri: Image Preview -->
                <div class="md:col-span-1 flex flex-col space-y-2">
                  <Label class="text-xs text-muted-foreground block text-center uppercase font-bold">Foto Sekolah / Profil</Label>
                  <div class="relative rounded-2xl overflow-hidden border border-border/50 aspect-video bg-background/30 flex items-center justify-center">
                    <img v-if="form.about_image" :src="form.about_image" class="w-full h-full object-cover" />
                    <span v-else class="text-3xl">🏫</span>
                  </div>
                  <label class="block text-center py-2 rounded-xl border border-border/50 hover:bg-muted/50 font-bold text-[10px] cursor-pointer transition-colors bg-background/50 text-foreground">
                    Ganti Foto
                    <input type="file" @change="onAboutImageUpload" class="sr-only" accept="image/*" />
                  </label>
                </div>
                <!-- Kolom Kanan: Title & Description -->
                <div class="md:col-span-2 flex flex-col space-y-3">
                  <div>
                    <Label class="text-xs text-muted-foreground mb-1.5 block uppercase font-bold">Judul Profil Tentang Kami</Label>
                    <Input type="text" v-model="form.about_title" class="rounded-xl text-xs" />
                  </div>
                  <div class="flex-1 flex flex-col">
                    <Label class="text-xs text-muted-foreground mb-1.5 block uppercase font-bold">Deskripsi Tentang Sekolah</Label>
                    <Textarea v-model="form.about_description" class="rounded-xl text-xs flex-1 min-h-[110px] resize-none"></Textarea>
                  </div>
                </div>
              </div>

              <div>
                <Label class="text-xs text-muted-foreground mb-1.5 block">Visi Instansi</Label
                ><Textarea
                  v-model="form.about_vision"
                  rows="2"
                  class="rounded-xl text-xs"
                ></Textarea>
              </div>
              <div class="space-y-2">
                <Label class="text-xs text-muted-foreground block">Misi Instansi</Label>
                <div
                  v-for="(mission, index) in form.about_mission"
                  :key="index"
                  class="flex gap-2 items-center"
                >
                  <span
                    class="text-primary font-bold text-xs bg-primary/10 w-6 h-6 flex items-center justify-center rounded-lg"
                    >{{ index + 1 }}</span
                  >
                  <Input
                    type="text"
                    v-model="form.about_mission[index]"
                    class="flex-1 rounded-xl text-xs"
                  />
                  <Button
                    variant="ghost"
                    size="icon"
                    @click="removeMission(index)"
                    class="text-destructive hover:text-destructive hover:bg-destructive/10"
                    ><Trash2 class="w-4 h-4"
                  /></Button>
                </div>
                <div class="flex gap-2">
                  <input
                    type="text"
                    v-model="newMissionItem"
                    class="flex-1 px-3 py-2 text-xs bg-background/30 border border-border/50 rounded-xl text-foreground focus:outline-none"
                    placeholder="Tambah misi..."
                    @keydown.enter.prevent="addMission"
                  />
                  <Button
                    variant="secondary"
                    size="sm"
                    @click="addMission"
                    class="text-xs font-bold rounded-xl"
                    >Tambah</Button
                  >
                </div>
              </div>
            </div>

            <!-- TAB 4: Sections (Keunggulan, Statistik, FAQ, dll) -->
            <div
              v-if="modalActiveTab === 'sections'"
              class="space-y-6"
            >
              <h4 class="text-sm font-bold text-foreground">Kelola Section Konten</h4>

              <!-- Inline Section Item Sub-Editor -->
              <div
                v-if="selectedSectionIndex !== null"
                class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4"
              >
                <h5 class="font-bold text-xs text-primary">
                  {{ isAddingSectionItem ? 'Tambah Item Baru' : 'Edit Item' }}
                </h5>
                <div class="grid md:grid-cols-2 gap-4">
                  <div>
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Judul Item</Label>
                    <Input
                      type="text"
                      v-model="sectionItemForm.title"
                      class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                    />
                  </div>
                  <div>
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block"
                      >Nilai / Badge (Opsional)</Label
                    >
                    <Input
                      type="text"
                      v-model="sectionItemForm.value"
                      class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                      placeholder="misal: 100+ atau A"
                    />
                  </div>
                  <div>
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Ikon (Lucide)</Label>
                    <Input
                      type="text"
                      v-model="sectionItemForm.icon"
                      class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                      placeholder="contoh: star, check"
                    />
                  </div>
                  <div>
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Link (Tautan)</Label>
                    <Input
                      type="text"
                      v-model="sectionItemForm.link"
                      class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                      placeholder="contoh: https://..."
                    />
                  </div>
                  <div class="md:col-span-2">
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block"
                      >Deskripsi Singkat</Label
                    >
                    <Textarea
                      v-model="sectionItemForm.description"
                      rows="2"
                      class="w-full px-3 py-2 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                    ></Textarea>
                  </div>
                  <div class="md:col-span-2">
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Upload Gambar</Label>
                    <div class="flex items-center gap-4">
                      <div class="w-14 h-14 rounded-xl border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 flex items-center justify-center overflow-hidden">
                        <img v-if="sectionItemForm.image" :src="sectionItemForm.image" class="w-full h-full object-cover" />
                        <span v-else class="text-lg opacity-50">🖼️</span>
                      </div>
                      <label class="px-4 py-2 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/50 hover:bg-gray-50 dark:hover:bg-white/5 rounded-xl font-bold text-xs cursor-pointer text-foreground transition-colors">
                        Unggah Foto
                        <input type="file" @change="onSectionItemImageUpload" class="sr-only" accept="image/*" />
                      </label>
                    </div>
                  </div>
                </div>
                <div class="flex justify-end gap-2">
                  <Button
                    variant="outline"
                    size="sm"
                    @click="closeSectionItemEditor"
                    class="rounded-lg text-xs"
                    >Batal</Button
                  >
                  <Button
                    size="sm"
                    @click="saveSectionItem"
                    class="rounded-lg text-xs font-bold"
                    >Simpan</Button
                  >
                </div>
              </div>

              <!-- List Section items -->
              <div v-else class="space-y-6">
                <VueDraggable v-model="form.sections" item-key="id" class="space-y-6" handle=".section-drag-handle" animation="200">
                  <template #item="{ element: sec, index: secIdx }">
                    <div class="glass-mini p-5 border border-gray-100 dark:border-white/10 rounded-2xl space-y-3 section-drag-handle cursor-grab active:cursor-grabbing hover:bg-white/50 dark:hover:bg-white/5 transition-colors">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                          <span class="text-xs font-bold uppercase tracking-wider text-primary bg-primary/15 px-2 py-0.5 rounded">{{ sec.type || 'SECTION' }}</span>
                          <span class="font-extrabold text-sm text-foreground">{{ sec.title || 'Section Tanpa Judul' }}</span>
                        </div>
                        <button type="button" @click="openSectionItemEditor(secIdx)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary/15 hover:bg-primary/25 text-primary font-bold text-xs transition-colors">
                          <Plus class="w-3.5 h-3.5" /> Tambah Item
                        </button>
                      </div>
                      
                      <VueDraggable v-if="sec.items" v-model="sec.items" item-key="id" class="space-y-2" handle=".item-drag-handle" animation="200">
                        <template #item="{ element: item }">
                          <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-white/5 text-xs item-drag-handle cursor-grab active:cursor-grabbing hover:bg-gray-50 dark:hover:bg-white/10 transition-colors">
                            <div class="flex gap-4">
                              <div v-if="item.image" class="w-10 h-10 rounded-lg overflow-hidden shrink-0 border border-gray-200 dark:border-white/10">
                                <img :src="item.image" class="w-full h-full object-cover" />
                              </div>
                              <div v-else-if="item.icon" class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0 border border-primary/20">
                                <span class="font-bold text-xs uppercase">{{ item.icon.substring(0, 2) }}</span>
                              </div>
                              <div>
                                <span class="font-semibold text-foreground">{{ item.title }}</span>
                                <span v-if="item.value" class="text-primary font-bold ml-2">({{ item.value }})</span>
                                <p class="text-[10px] text-muted-foreground mt-0.5 line-clamp-1">{{ item.description }}</p>
                              </div>
                            </div>
                            <div class="flex gap-1 shrink-0">
                              <button type="button" @click="openSectionItemEditor(secIdx, item)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 transition-colors"><Edit class="w-3.5 h-3.5" /></button>
                              <button type="button" @click="deleteSectionItem(secIdx, item.id)" class="p-1.5 rounded-lg hover:bg-destructive/10 text-destructive transition-colors"><Trash2 class="w-3.5 h-3.5" /></button>
                            </div>
                          </div>
                        </template>
                      </VueDraggable>
                    </div>
                  </template>
                </VueDraggable>
              </div>
            </div>

            <!-- TAB 5: Contact -->
            <div
              v-if="modalActiveTab === 'contact'"
              class="space-y-6"
            >
              <h4 class="text-sm font-bold text-foreground">Hubungi Kami & Media Sosial</h4>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block"
                    >Email Sekolah/Yayasan</Label
                  ><Input
                    type="email"
                    v-model="form.contact_email"
                    class="rounded-xl text-xs"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Telepon</Label
                  ><Input
                    type="text"
                    v-model="form.contact_phone"
                    class="rounded-xl text-xs"
                  />
                </div>
                <div class="md:col-span-2">
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Alamat Lengkap</Label
                  ><Textarea
                    v-model="form.contact_address"
                    rows="2"
                    class="rounded-xl text-xs"
                  ></Textarea>
                </div>
                <div class="md:col-span-2">
                  <Label class="text-xs text-muted-foreground mb-1.5 block"
                    >Google Maps Embed URL</Label
                  ><Textarea
                    v-model="form.contact_maps_embed"
                    rows="2"
                    class="rounded-xl text-xs"
                  ></Textarea>
                </div>
              </div>
              <div class="grid md:grid-cols-2 gap-4 pt-2">
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">TikTok URL</Label
                  ><Input
                    type="text"
                    v-model="form.social_tiktok"
                    class="rounded-xl text-xs"
                    placeholder="https://tiktok.com/@sekolah"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Instagram URL</Label
                  ><Input
                    type="text"
                    v-model="form.social_instagram"
                    class="rounded-xl text-xs"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Facebook URL</Label
                  ><Input
                    type="text"
                    v-model="form.social_facebook"
                    class="rounded-xl text-xs"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">YouTube URL</Label
                  ><Input
                    type="text"
                    v-model="form.social_youtube"
                    class="rounded-xl text-xs"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div
          class="p-6 border-t border-border flex justify-end gap-3 bg-muted/10 shadow-[0_-1px_0_0_theme(colors.border)]"
        >
          <Button
            variant="outline"
            @click="isModalOpen = false"
            class="rounded-xl text-xs font-bold"
          >
            Batal
          </Button>
          <Button
            @click="saveModalData"
            class="rounded-xl text-xs font-bold shadow-md"
          >
            <Save class="w-4 h-4 mr-1.5" /> Simpan Pengaturan
          </Button>
        </div>
      </div>
    </div>
  </div>

  <div
    v-else
    class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4"
  >
    <div
      class="bg-card border border-border rounded-2xl p-8 max-w-md w-full shadow-sm flex flex-col items-center"
    >
      <div
        class="size-16 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mb-6 border border-primary/20"
      >
        <Lock class="size-8" />
      </div>
      <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-foreground mb-2">
        Akses Dibatasi
      </h2>
      <p class="text-sm text-muted-foreground leading-relaxed mb-8">
        Halaman Konfigurasi Global hanya dapat diakses oleh peran Superadmin. Anda tidak memiliki
        izin untuk melihat atau mengubah pengaturan ini.
      </p>
      <Button
        @click="router.push('/dashboard')"
        size="lg"
        class="w-full font-semibold rounded-xl transition-all active:scale-[0.98]"
      >
        Kembali ke Dashboard
      </Button>
    </div>
  </div>

  <!-- Alert Dialog Konfirmasi Akses -->
  <AlertDialog :open="showAccessDialog" @update:open="showAccessDialog = $event">
    <AlertDialogContent class="sm:max-w-md">
      <AlertDialogHeader>
        <AlertDialogTitle>Konfirmasi Perubahan Akses</AlertDialogTitle>
        <AlertDialogDescription v-if="itemToToggleAccess">
          Anda akan mengubah status akses builder Landing Page untuk <strong>{{ itemToToggleAccess.name }}</strong>. 
          Apakah Anda yakin ingin melanjutkan tindakan ini?
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel @click="showAccessDialog = false">Batal</AlertDialogCancel>
        <AlertDialogAction @click="executeToggleAccess">Ya, Lanjutkan</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>

  <!-- Alert Dialog Konfirmasi Publikasi -->
  <AlertDialog :open="showPublishDialog" @update:open="showPublishDialog = $event">
    <AlertDialogContent class="sm:max-w-md">
      <AlertDialogHeader>
        <AlertDialogTitle>Konfirmasi Perubahan Status Publikasi</AlertDialogTitle>
        <AlertDialogDescription v-if="selectedItemForEdit">
          Anda akan {{ selectedItemForEdit.is_published ? 'menonaktifkan (menyembunyikan)' : 'mengaktifkan (mempublikasikan)' }} landing page <strong>{{ selectedItemForEdit.name }}</strong> dari jangkauan publik.
          Apakah Anda yakin ingin melanjutkan tindakan ini?
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel @click="showPublishDialog = false">Batal</AlertDialogCancel>
        <AlertDialogAction @click="executeTogglePublish">Ya, Lanjutkan</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
