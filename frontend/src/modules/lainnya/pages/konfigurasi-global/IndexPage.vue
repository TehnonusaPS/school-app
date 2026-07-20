<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'
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
  Eye,
  ExternalLink,
  Users,
  HeartHandshake
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

const colorPalettes = computed(() => {
  const t = form.value.theme
  if (t === 'islami') {
    return [
      { name: 'Default', primary: '#047857', secondary: '#fbbf24', accent: '#14b8a6' },
      { name: 'Desert Gold', primary: '#b45309', secondary: '#fcd34d', accent: '#064e3b' },
      { name: 'Royal Sapphire', primary: '#1e3a8a', secondary: '#38bdf8', accent: '#0ea5e9' },
      { name: 'Serene Sage', primary: '#4d7c0f', secondary: '#a3e635', accent: '#15803d' }
    ]
  }
  if (t === 'playful') {
    return [
      { name: 'Default', primary: '#ec4899', secondary: '#fcd34d', accent: '#06b6d4' },
      { name: 'Candy Pop', primary: '#8b5cf6', secondary: '#f472b6', accent: '#34d399' },
      { name: 'Sunshine Kids', primary: '#eab308', secondary: '#fb923c', accent: '#38bdf8' },
      { name: 'Minty Fresh', primary: '#10b981', secondary: '#6366f1', accent: '#fbbf24' }
    ]
  }
  // default: modern
  return [
    { name: 'Default', primary: '#1e40af', secondary: '#f59e0b', accent: '#0ea5e9' },
    { name: 'Elegant Ruby', primary: '#be123c', secondary: '#fbbf24', accent: '#172554' },
    { name: 'Forest Scholar', primary: '#047857', secondary: '#eab308', accent: '#14b8a6' },
    { name: 'Executive Slate', primary: '#334155', secondary: '#94a3b8', accent: '#2563eb' }
  ]
})

const applyPalette = (palette) => {
  isCustomColor.value = false
  form.value.primary_color = palette.primary
  form.value.secondary_color = palette.secondary
  form.value.accent_color = palette.accent
}

const applyDefaultThemeColor = (newTheme) => {
  let defPrimary = '#1e40af', defSec = '#f59e0b', defAcc = '#0ea5e9'
  if (newTheme === 'islami') {
    defPrimary = '#047857'; defSec = '#fbbf24'; defAcc = '#14b8a6'
  } else if (newTheme === 'playful') {
    defPrimary = '#ec4899'; defSec = '#fcd34d'; defAcc = '#06b6d4'
  }
  form.value.primary_color = defPrimary
  form.value.secondary_color = defSec
  form.value.accent_color = defAcc
  isCustomColor.value = false
}

const isPaletteMatch = (palette) => {
  if (!form.value.primary_color || !palette.primary) return false
  return form.value.primary_color.toLowerCase() === palette.primary.toLowerCase() &&
         form.value.secondary_color.toLowerCase() === palette.secondary.toLowerCase()
}

const isAnyPaletteMatch = computed(() => {
  return colorPalettes.value.some(p => isPaletteMatch(p))
})

// Temp state untuk mengedit/menambah item section di dalam modal
const newMissionItem = ref('')
const selectedSectionIndex = ref(null)
const editingSectionItem = ref(null)
const isAddingSectionItem = ref(false)

const activeSectionType = computed(() => {
  if (selectedSectionIndex.value === null) return null
  return form.value.sections[selectedSectionIndex.value]?.type || null
})

const isCustomColor = ref(false)

const sectionItemForm = ref({
  id: null,
  title: '',
  description: '',
  icon: 'star',
  image: '',
  link: '',
  value: ''
})

// Methods untuk upload hero (prototipe frontend)
const uploadFile = async (file) => {
  const formData = new FormData()
  formData.append('image', file)
  const res = await api.post('/landing-page/upload', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  return res.data.url
}

async function onHeroImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  try {
    const uploadedUrl = await uploadFile(file)
    if (!form.value.hero_images) form.value.hero_images = []
    form.value.hero_images.push({ url: uploadedUrl, caption: '' })
    toast.success('Gambar berhasil diupload dan ditambahkan ke carousel!')
  } catch (err) {
    toast.error('Gagal mengunggah gambar.')
  }
}

function removeHeroImage(index) {
  form.value.hero_images.splice(index, 1)
}

async function onAboutImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  try {
    form.value.about_image = await uploadFile(file)
    toast.success('Foto profil berhasil diupload!')
  } catch (err) {
    toast.error('Gagal mengunggah foto profil.')
  }
}

async function onSectionItemImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  try {
    sectionItemForm.value.image = await uploadFile(file)
    toast.success('Gambar item berhasil diupload!')
  } catch (err) {
    toast.error('Gagal mengunggah gambar item.')
  }
}

// Helper dummy item data generator
const createSchoolSections = () => [
  { id: 1, type: 'stats', title: 'Sekolah Kami Dalam Angka', is_visible: true, sort_order: 1, items: [
    { id: 11, title: 'Siswa Aktif', value: '850+' },
    { id: 12, title: 'Guru Profesional', value: '45' },
    { id: 13, title: 'Kelas / Rombel', value: '24' },
    { id: 14, title: 'Akreditasi', value: 'A' },
    { id: 15, title: 'Lulusan PTN', value: '95%' }
  ] },
  { id: 2, type: 'features', title: 'Mengapa Memilih Kami?', is_visible: true, sort_order: 2, items: [
    { id: 21, title: 'Guru Tersertifikasi', description: 'Tenaga pendidik profesional lulusan universitas ternama yang ramah dan kompeten.', icon: 'award' },
    { id: 22, title: 'Kurikulum Modern', description: 'Pembelajaran berbasis proyek (Project-Based Learning) terintegrasi dengan teknologi digital.', icon: 'book' },
    { id: 23, title: 'Fasilitas Lengkap', description: 'Ruang kelas ber-AC, laboratorium canggih, dan sarana olahraga yang memadai.', icon: 'monitor' },
    { id: 24, title: 'Lingkungan Asri', description: 'Area sekolah hijau dan luas untuk mendukung kegiatan belajar mengajar yang nyaman.', icon: 'heart' }
  ] },
  { id: 3, type: 'programs', title: 'Program Unggulan', is_visible: true, sort_order: 3, items: [
    { id: 31, title: 'Kelas Bilingual', description: 'Pengantar bahasa Inggris di mata pelajaran Matematika dan Sains.', image: 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600' },
    { id: 32, title: 'Tahfidz Al-Quran', description: 'Program hafalan Al-Quran dengan target 3 Juz untuk tingkat SD.', image: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=600' },
    { id: 33, title: 'Klub Sains & Robotika', description: 'Mengembangkan logika anak melalui praktik sains aplikatif dan pembuatan robot sederhana.', image: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=600' }
  ] },
  { id: 4, type: 'gallery', title: 'Galeri Kegiatan Belajar', is_visible: true, sort_order: 4, items: [
    { id: 41, title: 'Lomba Cerdas Cermat', image: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=600' },
    { id: 42, title: 'Pentas Seni Tahunan', image: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=600' },
    { id: 43, title: 'Kunjungan Edukatif', image: 'https://images.unsplash.com/photo-1523580494112-071dcb92a11d?q=80&w=600' },
    { id: 44, title: 'Praktikum Biologi', image: 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=600' }
  ] },
  { id: 5, type: 'testimonials', title: 'Apa Kata Orang Tua Murid?', is_visible: true, sort_order: 5, items: [
    { id: 51, title: 'Bunda Larasati', description: 'Sangat senang menyekolahkan anak di sini. Gurunya sangat peduli perkembangan emosional anak.', value: 'Wali Murid Kelas 3' },
    { id: 52, title: 'Bapak Hermawan', description: 'Fasilitas IT dan Coding-nya luar biasa. Anak saya jadi punya hobi baru yang produktif.', value: 'Wali Murid Kelas 5' },
    { id: 53, title: 'Ibu Dina', description: 'Lingkungannya sangat mendukung anak untuk berekspresi. Sangat recommended.', value: 'Wali Murid Kelas 1' }
  ] },
  { id: 6, type: 'faq', title: 'Pertanyaan yang Sering Diajukan', is_visible: true, sort_order: 6, items: [
    { id: 61, title: 'Bagaimana cara melakukan pendaftaran?', description: 'Anda dapat menekan tombol Daftar Sekarang di atas lalu mengisi formulir secara online.' },
    { id: 62, title: 'Apakah tersedia antar jemput sekolah?', description: 'Ya, sekolah menyediakan armada antar jemput resmi untuk radius maksimal 10 KM.' },
    { id: 63, title: 'Apakah ada fasilitas makan siang?', description: 'Kami menyediakan kantin sehat yang terintegrasi dengan sistem uang saku digital anak.' }
  ] }
]

const createFoundationSections = (schools = []) => {
  const images = [
    'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600',
    'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=600',
    'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=600',
    'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=600'
  ];
  
  const programItems = schools.length > 0 
    ? schools.map((s, idx) => ({
        id: 31 + idx,
        title: s.name,
        description: s.level ? `Pendidikan tingkat ${s.level}.` : 'Lembaga pendidikan unggulan.',
        image: images[idx % images.length]
      }))
    : [
        { id: 31, title: 'Pondok Pesantren', description: 'Pusat pendidikan agama dan pengkajian kitab kuning.', image: images[1] },
        { id: 32, title: 'Sekolah Terpadu', description: 'Pendidikan formal dari tingkat TK hingga SMA.', image: images[0] },
        { id: 33, title: 'Pusat Diklat Terpadu', description: 'Lembaga pengembangan SDM untuk mencetak profesional tangguh.', image: images[2] }
      ];

  return [
  { id: 1, type: 'stats', title: 'Jejaring Yayasan', is_visible: true, sort_order: 1, items: [
    { id: 11, title: 'Lembaga Pendidikan', value: schools.length > 0 ? schools.length.toString() : '12' },
    { id: 12, title: 'Total Siswa/Santri', value: '5,400+' },
    { id: 13, title: 'Alumni Tersebar', value: '15,000+' },
    { id: 14, title: 'Pondok Pesantren', value: '3' }
  ] },
  { id: 2, type: 'features', title: 'Fokus Yayasan Kami', is_visible: true, sort_order: 2, items: [
    { id: 21, title: 'Pendidikan Inklusif', description: 'Membangun lembaga yang dapat diakses oleh seluruh lapisan masyarakat.', icon: 'award' },
    { id: 22, title: 'Pemberdayaan Umat', description: 'Menyelenggarakan program beasiswa dan bantuan pendidikan bagi yatim dhuafa.', icon: 'heart' },
    { id: 23, title: 'Jejaring Global', description: 'Berkomitmen mendidik dengan kurikulum yang diakui secara internasional.', icon: 'book' }
  ] },
  { id: 3, type: 'programs', title: 'Lembaga Pendidikan', is_visible: true, sort_order: 3, items: programItems },
  { id: 4, type: 'gallery', title: 'Kegiatan Sosial & Diklat', is_visible: true, sort_order: 4, items: [
    { id: 41, title: 'Santunan Yatim & Dhuafa', image: 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=600' },
    { id: 42, title: 'Pelatihan Guru Nasional', image: 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=600' },
    { id: 43, title: 'Bakti Sosial Kesehatan', image: 'https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?q=80&w=600' },
    { id: 44, title: 'Perayaan Hari Besar', image: 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=600' }
  ] },
  { id: 5, type: 'testimonials', title: 'Kata Mitra Kami', is_visible: true, sort_order: 5, items: [
    { id: 51, title: 'Bapak H. Abdullah', description: 'Yayasan yang sangat amanah dalam menyalurkan dana umat untuk pendidikan berkualitas.', value: 'Donatur Tetap' },
    { id: 52, title: 'Dinas Pendidikan Daerah', description: 'Lembaga pendidikan di bawah yayasan ini selalu menjadi teladan dalam penerapan kurikulum.', value: 'Mitra Pemerintah' },
    { id: 53, title: 'Aksi Cepat Tanggap', description: 'Kolaborasi yang luar biasa gesit saat terjun di lapangan. Profesional dan amanah.', value: 'NGO Partner' }
  ] },
  { id: 6, type: 'faq', title: 'FAQ Yayasan', is_visible: true, sort_order: 6, items: [
    { id: 61, title: 'Bagaimana cara berdonasi ke yayasan?', description: 'Anda dapat menyalurkan donasi melalui rekening resmi yayasan atau menghubungi kami secara langsung.' },
    { id: 62, title: 'Apakah yayasan membuka kerja sama CSR?', description: 'Ya, kami sangat terbuka untuk bersinergi dalam program sosial dan pemberdayaan.' },
    { id: 63, title: 'Di mana cabang yayasan ini?', description: 'Pusat kami ada di Jakarta, namun unit-unit sekolah kami tersebar di berbagai provinsi.' }
  ] }
];
}

// Helper data factory
const createDefaultEntity = (id, name, slug, avatarLetter, colorClass, type, status = 'Aktif') => ({
  id, name, slug, avatarLetter, avatarColor: colorClass, status, type,
  template: type === 'Yayasan' ? 'Modern Akademik' : 'Islami Elegant', 
  landing_page_enabled: true, is_published: true, lastUpdated: 'Kemarin', 
  indicatorColor: status === 'Aktif' ? 'bg-emerald-500' : 'bg-red-500', theme: type === 'Yayasan' ? 'modern' : 'islami',
  legal_number: 'NPSN: 10293847', slogan: 'Mencerdaskan Kehidupan Bangsa',
  meta_title: name, meta_description: 'Website resmi ' + name + ' yang berdedikasi tinggi dalam pendidikan.', 
  primary_color: type === 'Yayasan' ? '#1e40af' : '#7c3aed', secondary_color: '#f59e0b', accent_color: '#06b6d4',
  hero_title: 'Selamat Datang di ' + name, hero_subtitle: 'Pendidikan Berkualitas, Karakter Unggul', hero_description: 'Kami berkomitmen mencetak generasi masa depan yang tangguh, berakhlak mulia, dan siap menghadapi tantangan global.', 
  hero_cta_text: 'Jelajahi Program', hero_cta_link: '#programs',
  hero_images: [
    { url: 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?q=80&w=1200', caption: 'Gedung Sekolah Utama' },
    { url: 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1200', caption: 'Kegiatan Belajar Mengajar' },
    { url: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1200', caption: 'Fasilitas Laboratorium Modern' }
  ],
  about_image: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=800',
  about_title: 'Lebih Dekat dengan ' + name, about_description: 'Berdiri sebagai pusat pendidikan terpadu, kami tidak hanya mengedepankan prestasi akademik namun juga penanaman nilai moral yang kuat dalam setiap kegiatan.', 
  about_vision: 'Menjadi institusi pendidikan terbaik yang menginspirasi kreativitas dan mencerdaskan bangsa.', 
  about_mission: ['Menyelenggarakan pendidikan inovatif', 'Menanamkan budi pekerti luhur'],
  contact_email: 'halo@' + slug + '.sch.id', contact_phone: '0812-3456-7890', contact_address: 'Jl. Pendidikan No. 123, Kota Nusantara', contact_maps_embed: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2736417711413!2d106.79724127585258!3d-6.227606360984852!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f14be6d7d6f5%3A0x6e2df40fe930!2sJakarta%20Selatan!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid', social_instagram: 'https://instagram.com/' + slug, social_facebook: 'https://facebook.com/' + slug, social_youtube: 'https://youtube.com/c/' + slug, social_tiktok: 'https://tiktok.com/@' + slug,
  sections: type === 'Yayasan' ? createFoundationSections([]) : createSchoolSections()
})

const defaultFoundationMappings = [
  createDefaultEntity('FDN-2023-001', 'Yayasan Pendidikan Nusantara', 'yayasan-nusantara', 'YN', 'bg-indigo-500/10 text-indigo-500 border border-indigo-500/20', 'Yayasan'),
  createDefaultEntity('FDN-2023-002', 'Yayasan Bina Cendekia', 'yayasan-cendekia', 'YC', 'bg-blue-500/10 text-blue-500 border border-blue-500/20', 'Yayasan'),
  createDefaultEntity('FDN-2023-003', 'Yayasan Pelita Bangsa', 'yayasan-pelita', 'YP', 'bg-teal-500/10 text-teal-500 border border-teal-500/20', 'Yayasan'),
  createDefaultEntity('FDN-2023-004', 'Yayasan Generasi Emas', 'yayasan-emas', 'YE', 'bg-amber-500/10 text-amber-500 border border-amber-500/20', 'Yayasan'),
  createDefaultEntity('FDN-2023-005', 'Yayasan Citra Mandiri', 'yayasan-citra', 'YM', 'bg-rose-500/10 text-rose-500 border border-rose-500/20', 'Yayasan', 'Nonaktif')
]

const defaultSchoolMappings = [
  createDefaultEntity('SCH-2023-001', 'SMA Nusantara', 'sma-nusantara', 'SN', 'bg-violet-500/10 text-violet-500 border border-violet-500/20', 'Sekolah'),
  createDefaultEntity('SCH-2023-002', 'SMP Bina Cendekia', 'smp-cendekia', 'SC', 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20', 'Sekolah'),
  createDefaultEntity('SCH-2023-003', 'SD Pelita Bangsa', 'sd-pelita', 'SP', 'bg-sky-500/10 text-sky-500 border border-sky-500/20', 'Sekolah'),
  createDefaultEntity('SCH-2023-004', 'TK Generasi Emas', 'tk-emas', 'TE', 'bg-pink-500/10 text-pink-500 border border-pink-500/20', 'Sekolah'),
  createDefaultEntity('SCH-2023-005', 'SMK Citra Mandiri', 'smk-citra', 'SM', 'bg-orange-500/10 text-orange-500 border border-orange-500/20', 'Sekolah'),
  createDefaultEntity('SCH-2023-006', 'SDIT Al-Falah', 'sdit-alfalah', 'SA', 'bg-green-500/10 text-green-500 border border-green-500/20', 'Sekolah')
]

const foundationMappings = ref([])
const schoolMappings = ref([])

const generateSlug = (name) => {
  if (!name) return ''
  return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '')
}

const fetchConfigData = async () => {
  try {
    const fRes = await api.get('/landing-page/foundations')
    foundationMappings.value = fRes.data.map(f => {
      let config = f.landing_page_config || {}
      if (typeof config === 'string') {
        try { config = JSON.parse(config) } catch(e){}
      }
      return {
        id: f.id,
        name: f.name,
        slug: config.slug || generateSlug(f.name),
        avatarLetter: f.name.charAt(0),
        avatarColor: 'bg-indigo-500/10 text-indigo-500 border border-indigo-500/20',
        status: 'Aktif',
        type: 'Yayasan',
        template: f.landing_page_theme === 'islami' ? 'Islami Elegant' : (f.landing_page_theme === 'playful' ? 'Colorful Playful' : 'Modern Akademik'),
        landing_page_enabled: !!f.landing_page_enabled,
        is_published: !!f.landing_page_enabled,
        lastUpdated: f.updated_at ? new Date(f.updated_at).toLocaleDateString() : 'Baru saja',
        indicatorColor: 'bg-emerald-500',
        theme: f.landing_page_theme || 'modern',
        ...config,
        contact_email: f.email || '',
        contact_phone: f.phone || '',
        contact_address: f.address || '',
        meta_title: f.name,
        legal_number: f.deed_number || '',
        schools: f.schools || [],
        sections: config.sections || createFoundationSections(f.schools || [])
      }
    })

    const sRes = await api.get('/landing-page/schools')
    schoolMappings.value = sRes.data.map(s => {
      let config = s.landing_page_config || {}
      if (typeof config === 'string') {
        try { config = JSON.parse(config) } catch(e){}
      }
      return {
        id: s.id,
        name: s.name,
        slug: config.slug || generateSlug(s.name),
        avatarLetter: s.name.charAt(0),
        avatarColor: 'bg-sky-500/10 text-sky-500 border border-sky-500/20',
        status: 'Aktif',
        type: 'Sekolah',
        template: s.landing_page_theme === 'islami' ? 'Islami Elegant' : (s.landing_page_theme === 'playful' ? 'Colorful Playful' : 'Modern Akademik'),
        landing_page_enabled: !!s.landing_page_enabled,
        is_published: !!s.landing_page_enabled,
        lastUpdated: s.updated_at ? new Date(s.updated_at).toLocaleDateString() : 'Baru saja',
        indicatorColor: 'bg-emerald-500',
        theme: s.landing_page_theme || 'modern',
        ...config,
        contact_email: s.email || '',
        contact_phone: s.phone || '',
        contact_address: s.address || '',
        social_instagram: s.instagram || '',
        social_facebook: s.facebook || '',
        meta_title: s.name,
        legal_number: s.npsn || '',
        sections: config.sections || createSchoolSections()
      }
    })
  } catch (err) {
    console.error('Error fetching config data:', err)
  }
}

onMounted(() => {
  fetchConfigData()
})

const filterValues = ref({})
const page = ref(1)
const perPage = ref(5)

const columns = [
  { key: 'name', label: 'Nama Instansi', sortable: true },
  { key: 'template', label: 'Template UI' },
  { key: 'color', label: 'Warna Tema' },
  { key: 'access', label: 'Akses Builder' },
  { key: 'publish', label: 'Status Web' },
  { key: 'actions', label: 'Aksi' }
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

const saveToBackend = async (item) => {
  const payload = {
    landing_page_enabled: item.landing_page_enabled,
    landing_page_theme: item.theme,
    landing_page_config: { ...item }
  }
  try {
    if (item.type === 'Yayasan') {
      await api.put(`/landing-page/foundations/${item.id}`, payload)
    } else {
      await api.put(`/landing-page/schools/${item.id}`, payload)
    }
  } catch (err) {
    console.error(err)
  }
}

const executeToggleAccess = async () => {
  if (itemToToggleAccess.value) {
    itemToToggleAccess.value.landing_page_enabled = !itemToToggleAccess.value.landing_page_enabled
    await saveToBackend(itemToToggleAccess.value)
    toast.success(`Akses untuk ${itemToToggleAccess.value.name} berhasil diubah!`)
    showAccessDialog.value = false
    itemToToggleAccess.value = null
  }
}

const showPublishDialog = ref(false)
const itemToTogglePublish = ref(null)

const confirmTogglePublish = item => {
  itemToTogglePublish.value = item
  showPublishDialog.value = true
}

const executeTogglePublish = async () => {
  if (itemToTogglePublish.value) {
    itemToTogglePublish.value.is_published = !itemToTogglePublish.value.is_published
    await saveToBackend(itemToTogglePublish.value)
    toast.success(`Status publikasi untuk ${itemToTogglePublish.value.name} berhasil diubah!`)
    showPublishDialog.value = false
    itemToTogglePublish.value = null
  }
}

// Buka Modal & Load data detail ke form secara komprehensif
const openModalEditor = item => {
  if (!item.type) {
    item.type = activeTab.value === 'yayasan' ? 'Yayasan' : 'Sekolah'
  }
  selectedItemForEdit.value = item
  modalActiveTab.value = 'general'
  isCustomColor.value = false
  
  const defaultTheme = item.theme || 'modern'
  
  form.value = {
    theme: defaultTheme,
    slug: item.slug || '',
    legal_number: item.legal_number || '',
    slogan: item.slogan || (item.type === 'Yayasan' ? 'Membangun Generasi Emas dan Berakhlak' : 'Sekolah Masa Depan Anda'),
    meta_title: item.meta_title || item.name,
    meta_description: item.meta_description || ('Situs web resmi ' + item.name + '. ' + (item.type === 'Yayasan' ? 'Membangun Generasi Emas dan Berakhlak.' : 'Sekolah Masa Depan Anda.')),
    primary_color: item.primary_color || (defaultTheme === 'islami' ? '#047857' : defaultTheme === 'playful' ? '#ec4899' : '#1e40af'),
    secondary_color: item.secondary_color || (defaultTheme === 'islami' ? '#fbbf24' : defaultTheme === 'playful' ? '#fcd34d' : '#f59e0b'),
    accent_color: item.accent_color || (defaultTheme === 'islami' ? '#14b8a6' : defaultTheme === 'playful' ? '#06b6d4' : '#0ea5e9'),
    hero_title: item.hero_title || ('Selamat Datang di ' + item.name),
    hero_subtitle: item.hero_subtitle || 'Pendidikan Berkualitas untuk Masa Depan',
    hero_description: item.hero_description || 'Kami berkomitmen memberikan pendidikan terbaik dengan fasilitas modern dan pengajar profesional.',
    hero_cta_text: item.hero_cta_text || 'Daftar Sekarang',
    hero_cta_link: item.hero_cta_link || '#daftar',
    hero_images: item.hero_images && item.hero_images.length ? [...item.hero_images] : [
      { url: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1200', caption: 'Gedung Sekolah Utama' },
      { url: 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1200', caption: 'Fasilitas Terpadu' },
      { url: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1200', caption: 'Kenyamanan Belajar' }
    ],
    about_image: item.about_image || 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=800',
    about_title: item.about_title || ('Tentang ' + item.name),
    about_description: item.about_description || 'Berdiri sebagai pusat pendidikan terpadu, kami tidak hanya mengedepankan prestasi akademik namun juga penanaman nilai moral yang kuat.',
    about_vision: item.about_vision || 'Menjadi institusi pendidikan terbaik yang menginspirasi kreativitas dan mencerdaskan bangsa.',
    about_mission: item.about_mission && item.about_mission.length ? [...item.about_mission] : [
      'Menyelenggarakan pendidikan inovatif dan bermutu tinggi',
      'Menanamkan budi pekerti luhur dan akhlak mulia',
      'Mengembangkan potensi dan minat bakat siswa',
      'Menyediakan fasilitas belajar yang modern dan memadai',
      'Membina kerja sama erat dengan orang tua dan masyarakat'
    ],
    contact_email: item.contact_email || '',
    contact_phone: item.contact_phone || '',
    contact_address: item.contact_address || '',
    contact_maps_embed: item.contact_maps_embed || '',
    social_instagram: item.social_instagram || '',
    social_facebook: item.social_facebook || '',
    social_youtube: item.social_youtube || '',
    social_tiktok: item.social_tiktok || '',
    sections: JSON.parse(
      JSON.stringify(
        item.sections || (activeTab.value === 'sekolah' ? createSchoolSections() : createFoundationSections(item.schools || []))
      )
    )
  }
  isModalOpen.value = true
}

// Simpan data lengkap dari modal editor asisten ke baris instansi
const saveModalData = async () => {
  if (selectedItemForEdit.value) {
    const item = selectedItemForEdit.value
    Object.assign(item, {
      theme: form.value.theme,
      slug: form.value.slug,
      legal_number: form.value.legal_number,
      slogan: form.value.slogan,
      meta_title: form.value.meta_title,
      meta_description: form.value.meta_description,
      primary_color: form.value.primary_color,
      secondary_color: form.value.secondary_color,
      accent_color: form.value.accent_color,
      hero_title: form.value.hero_title,
      hero_subtitle: form.value.hero_subtitle,
      hero_description: form.value.hero_description,
      hero_cta_text: form.value.hero_cta_text,
      hero_cta_link: form.value.hero_cta_link,
      hero_images: [...form.value.hero_images],
      about_image: form.value.about_image,
      about_title: form.value.about_title,
      about_description: form.value.about_description,
      about_vision: form.value.about_vision,
      about_mission: form.value.about_mission,
      contact_email: form.value.contact_email,
      contact_phone: form.value.contact_phone,
      contact_address: form.value.contact_address,
      contact_maps_embed: form.value.contact_maps_embed,
      social_instagram: form.value.social_instagram,
      social_facebook: form.value.social_facebook,
      social_youtube: form.value.social_youtube,
      social_tiktok: form.value.social_tiktok,
      sections: form.value.sections
    })
    item.template =
      form.value.theme === 'modern'
        ? 'Modern Akademik'
        : form.value.theme === 'islami'
          ? 'Islami Elegant'
          : 'Colorful Playful'
    item.lastUpdated = 'Baru saja'

    await saveToBackend(item)
    isModalOpen.value = false
    toast.success(`Konfigurasi landing page lengkap untuk ${item.name} disimpan ke database!`)
  }
}

// Helper Misi
const addMission = () => {
  if (newMissionItem.value.trim()) {
    form.value.about_mission.push(newMissionItem.value.trim())
    newMissionItem.value = ''
  }
}
const removeMission = index => {
  form.value.about_mission.splice(index, 1)
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

const showDeleteDialog = ref(false)
const itemToDelete = ref(null)

const confirmDeleteSectionItem = (sectionIdx, item) => {
  itemToDelete.value = { sectionIdx, id: item.id, title: item.title }
  showDeleteDialog.value = true
}

const executeDeleteSectionItem = () => {
  if (!itemToDelete.value) return
  const { sectionIdx, id } = itemToDelete.value
  const section = form.value.sections[sectionIdx]
  if (section) {
    section.items = section.items.filter(i => i.id !== id)
    toast.success('Item dihapus dari section!')
  }
  showDeleteDialog.value = false
  itemToDelete.value = null
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

        <template #cell-color="{ item }">
          <div class="flex items-center gap-1.5" title="Warna Utama, Sekunder, dan Aksen">
            <div
              class="w-3.5 h-3.5 rounded-full shadow-sm ring-1 ring-border"
              :style="{ backgroundColor: item.primary_color || (item.theme === 'modern' ? '#1e40af' : '#7c3aed') }"
            ></div>
            <div
              class="w-3.5 h-3.5 rounded-full shadow-sm ring-1 ring-border"
              :style="{ backgroundColor: item.secondary_color || '#f59e0b' }"
            ></div>
            <div
              class="w-3.5 h-3.5 rounded-full shadow-sm ring-1 ring-border"
              :style="{ backgroundColor: item.accent_color || '#06b6d4' }"
            ></div>
          </div>
        </template>

        <template #cell-access="{ item }">
          <button
            @click="confirmToggleAccess(item)"
            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
            :class="item.landing_page_enabled ? 'bg-primary' : 'bg-white/10 border-white/20'"
            title="Izinkan Yayasan/Sekolah mengedit Landing Page"
          >
            <span
              class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 ease-in-out"
              :class="item.landing_page_enabled ? 'translate-x-4' : 'translate-x-0'"
            />
          </button>
        </template>

        <template #cell-publish="{ item }">
          <button
            @click="confirmTogglePublish(item)"
            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
            :class="item.is_published ? 'bg-emerald-500' : 'bg-white/10 border-white/20'"
            title="Publikasikan Website"
          >
            <span
              class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 ease-in-out"
              :class="item.is_published ? 'translate-x-4' : 'translate-x-0'"
            />
          </button>
        </template>


        <template #cell-actions="{ item }">
          <div class="flex items-center justify-center gap-2">
            <Button
              size="sm"
              variant="default"
              class="h-7 text-[10px] font-bold px-3 rounded-lg"
              @click="openModalEditor(item)"
            >
              Kelola Data
            </Button>
            <a :href="`http://localhost:5173/s/${item.slug}`" target="_blank" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-primary transition-colors" title="Lihat Web Landing Page">
              <ExternalLink class="w-4 h-4" />
            </a>
          </div>
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
              <span>🛠️</span> Editor Data Utama Landing Page
            </h3>
            <p class="text-xs text-muted-foreground mt-1">
              Anda sedang mengelola instansi <span class="text-primary font-bold">{{ selectedItemForEdit?.name }}</span>
            </p>
          </div>

          <div class="flex flex-wrap items-center gap-3">
            <button
              @click="isModalOpen = false"
              class="p-2 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 rounded-xl text-gray-500 dark:text-gray-300 transition-colors"
            >
              <X class="w-4 h-4" />
            </button>
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
              <BookOpen class="w-4 h-4" /> 
              {{ selectedItemForEdit?.type === 'Yayasan' ? 'Profil Yayasan' : 'Profil Akademik' }}
            </Button>
            
            <!-- Tab Spesifik: PPDB (Sekolah) -->
            <Button
              v-if="selectedItemForEdit?.type === 'Sekolah'"
              @click="modalActiveTab = 'ppdb'"
              :variant="modalActiveTab === 'ppdb' ? 'default' : 'ghost'"
              class="w-full justify-start gap-2 rounded-xl text-xs font-bold"
            >
              <Users class="w-4 h-4" /> Informasi PPDB
            </Button>

            <!-- Tab Spesifik: Lembaga & Donasi (Yayasan) -->
            <template v-if="selectedItemForEdit?.type === 'Yayasan'">
              <Button
                @click="modalActiveTab = 'institutions'"
                :variant="modalActiveTab === 'institutions' ? 'default' : 'ghost'"
                class="w-full justify-start gap-2 rounded-xl text-xs font-bold"
              >
                <Building class="w-4 h-4" /> Lembaga Naungan
              </Button>
              <Button
                @click="modalActiveTab = 'donasi'"
                :variant="modalActiveTab === 'donasi' ? 'default' : 'ghost'"
                class="w-full justify-start gap-2 rounded-xl text-xs font-bold"
              >
                <HeartHandshake class="w-4 h-4" /> Rekening Donasi
              </Button>
            </template>

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
                      @change="applyDefaultThemeColor(opt.id)"
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
                    readonly
                    class="flex-1 rounded-none border-0 text-xs focus-visible:ring-0 bg-transparent text-muted-foreground cursor-not-allowed"
                    placeholder="contoh: sdit-nur-iman"
                  />
                </div>
              </div>

              <!-- Palet Warna Harmonis -->
              <div class="space-y-3 pt-4 border-t border-border mt-4">
                <Label class="text-xs font-bold text-foreground block">🎨 Rekomendasi Palet Harmonik & Kustomisasi</Label>
                <div class="flex flex-wrap gap-3">
                  <!-- Tombol Palet Preset -->
                  <button
                    v-for="(palette, idx) in colorPalettes"
                    :key="idx"
                    @click="applyPalette(palette)"
                    type="button"
                    class="group relative flex flex-col items-center gap-1.5 p-2 rounded-xl border transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                    :class="isPaletteMatch(palette) ? 'border-primary bg-primary/10 ring-2 ring-primary/20' : 'border-border bg-background/50 hover:bg-muted/50 hover:border-primary/50'"
                    :title="palette.name"
                  >
                    <div class="flex items-center -space-x-1">
                      <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[3]" :style="{ backgroundColor: palette.primary }"></div>
                      <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[2]" :style="{ backgroundColor: palette.secondary }"></div>
                      <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[1]" :style="{ backgroundColor: palette.accent }"></div>
                    </div>
                    <span class="text-[9px] font-medium transition-colors" :class="isPaletteMatch(palette) ? 'text-primary font-bold' : 'text-muted-foreground group-hover:text-foreground'">{{ palette.name }}</span>
                  </button>

                  <!-- Tombol Mode Custom -->
                  <button
                    @click="isCustomColor = true"
                    type="button"
                    class="group relative flex flex-col items-center gap-1.5 p-2 rounded-xl border transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                    :class="(!isAnyPaletteMatch || isCustomColor) ? 'border-primary bg-primary/10' : 'border-border bg-background/50 hover:bg-muted/50 hover:border-primary/50'"
                    title="Kustomisasi Bebas"
                  >
                    <div class="flex items-center -space-x-1">
                      <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[3]" :style="{ backgroundColor: (!isAnyPaletteMatch || isCustomColor) ? form.primary_color : '#000000' }"></div>
                      <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[2]" :style="{ backgroundColor: (!isAnyPaletteMatch || isCustomColor) ? form.secondary_color : '#000000' }"></div>
                      <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[1]" :style="{ backgroundColor: (!isAnyPaletteMatch || isCustomColor) ? form.accent_color : '#000000' }"></div>
                    </div>
                    <span class="text-[9px] font-medium transition-colors" :class="(!isAnyPaletteMatch || isCustomColor) ? 'text-primary font-bold' : 'text-muted-foreground group-hover:text-foreground'">Warna Custom</span>
                  </button>
                </div>
              </div>

              <div v-if="!isAnyPaletteMatch || isCustomColor" class="grid grid-cols-3 gap-4 pt-4 border-t border-border mt-4">
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
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Meta Title SEO <span class="text-[10px] text-blue-500 font-normal">(Otomatis dari Profil)</span></Label>
                  <Input
                    type="text"
                    :model-value="selectedItemForEdit?.name || '(Belum Diatur)'"
                    readonly
                    class="rounded-xl text-xs bg-muted/30 text-muted-foreground cursor-not-allowed"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Meta Description SEO</Label>
                  <Textarea
                    v-model="form.meta_description"
                    rows="2"
                    class="rounded-xl text-xs"
                    :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'Contoh: Lembaga penaung institusi pendidikan terbaik yang berfokus pada pengembangan umat.' : 'Contoh: Sekolah menengah atas terbaik se-DKI Jakarta yang berfokus pada karakter.'"
                  ></Textarea>
                </div>
              </div>

              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Nomor Legalitas / Izin <span class="text-[10px] text-blue-500 font-normal">(Otomatis dari Profil)</span></Label>
                  <Input
                    type="text"
                    :model-value="selectedItemForEdit?.legal_number || '(Belum Diatur di Profil)'"
                    readonly
                    class="rounded-xl text-xs bg-muted/30 text-muted-foreground cursor-not-allowed"
                  />
                  <p class="text-[10px] text-muted-foreground mt-1.5">Ditampilkan di bagian catatan kaki (footer) web.</p>
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Motto / Slogan (Opsional)</Label>
                  <Input
                    type="text"
                    v-model="form.slogan"
                    class="rounded-xl text-xs"
                    :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'Contoh: Membangun Generasi Rabbani' : 'Contoh: Cerdas, Kreatif, Berkarakter'"
                  />
                  <p class="text-[10px] text-muted-foreground mt-1.5">Semboyan yang mencerminkan instansi Anda.</p>
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
                <Label class="text-xs text-muted-foreground mb-1.5 block">Headline Utama</Label>
                <Input
                  type="text"
                  v-model="form.hero_title"
                  class="rounded-xl text-xs"
                  :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'Contoh: Membangun Generasi Emas' : 'Contoh: Sekolah Masa Depan Anda'"
                />
              </div>
              <div>
                <Label class="text-xs text-muted-foreground mb-1.5 block">Sub-headline</Label>
                <Input
                  type="text"
                  v-model="form.hero_subtitle"
                  class="rounded-xl text-xs"
                  :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'Contoh: Berkhidmat Membangun Peradaban' : 'Contoh: Terakreditasi A dan Berkarakter'"
                />
              </div>
              <div>
                <Label class="text-xs text-muted-foreground mb-1.5 block">Deskripsi Singkat</Label>
                <Textarea
                  v-model="form.hero_description"
                  rows="3"
                  class="rounded-xl text-xs"
                  :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'Contoh: Yayasan kami bergerak di bidang pendidikan, sosial, dan keagamaan dengan mengedepankan pembentukan karakter dan kemanfaatan umat...' : 'Contoh: Mari bergabung dengan ekosistem belajar yang modern, inovatif, dan berpusat pada minat bakat siswa...'"
                ></Textarea>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Label Tombol CTA</Label>
                  <Input
                    type="text"
                    v-model="form.hero_cta_text"
                    class="rounded-xl text-xs"
                    :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'misal: Profil Yayasan / Donasi' : 'misal: Daftar Sekarang'"
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
                  <Label class="text-xs text-muted-foreground block text-center uppercase font-bold">Foto Profil {{ selectedItemForEdit?.type || 'Instansi' }}</Label>
                  <div class="relative rounded-2xl overflow-hidden border border-border/50 aspect-video bg-background/30 flex items-center justify-center">
                    <img v-if="form.about_image" :src="form.about_image" class="w-full h-full object-cover" />
                    <span v-else class="text-3xl">🏫</span>
                  </div>
                  <label class="block text-center py-2 rounded-xl border border-border/50 hover:bg-muted/50 font-bold text-[10px] cursor-pointer transition-colors bg-background/50 text-foreground">
                    Ganti Foto
                    <input type="file" @change="onAboutImageUpload" class="sr-only" accept="image/*" />
                  </label>
                </div>
                <div class="md:col-span-2 flex flex-col space-y-3">
                  <div>
                    <Label class="text-xs text-muted-foreground mb-1.5 block uppercase font-bold">Judul Profil Tentang Kami</Label>
                    <Input type="text" v-model="form.about_title" class="rounded-xl text-xs" :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'Contoh: Sejarah Yayasan Pendidikan Nusantara' : 'Contoh: Profil Singkat SMA Nusantara'" />
                  </div>
                  <div class="flex-1 flex flex-col">
                    <Label class="text-xs text-muted-foreground mb-1.5 block uppercase font-bold">Deskripsi Tentang {{ selectedItemForEdit?.type || 'Instansi' }}</Label>
                    <Textarea v-model="form.about_description" class="rounded-xl text-xs flex-1 min-h-[110px] resize-none" placeholder="Tuliskan latar belakang, sejarah, atau filosofi instansi di sini..."></Textarea>
                  </div>
                </div>
              </div>

              <div>
                <Label class="text-xs text-muted-foreground mb-1.5 block">Visi Instansi</Label
                ><Textarea
                  v-model="form.about_vision"
                  rows="2"
                  class="rounded-xl text-xs"
                  placeholder="Contoh: Mewujudkan generasi muda yang beriman, bertakwa, berakhlak mulia..."
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

            <!-- TAB: PPDB (Sekolah) -->
            <div
              v-if="modalActiveTab === 'ppdb'"
              class="space-y-6"
            >
              <h4 class="text-sm font-bold text-foreground">Informasi PPDB</h4>
              <div class="border-2 border-dashed border-border/50 rounded-2xl p-10 flex flex-col items-center justify-center text-center bg-background/30">
                <Users class="w-10 h-10 text-muted-foreground mb-4 opacity-50" />
                <h5 class="font-bold text-foreground mb-1">Pengaturan PPDB</h5>
                <p class="text-xs text-muted-foreground max-w-sm">Fitur pengaturan kuota, gelombang pendaftaran, dan rincian biaya PPDB sedang dalam tahap pengembangan khusus untuk entitas Sekolah.</p>
              </div>
            </div>

            <!-- TAB: Lembaga Naungan (Yayasan) -->
            <div
              v-if="modalActiveTab === 'institutions'"
              class="space-y-6"
            >
              <h4 class="text-sm font-bold text-foreground">Kelola Lembaga Naungan</h4>
              <div class="border-2 border-dashed border-border/50 rounded-2xl p-10 flex flex-col items-center justify-center text-center bg-background/30">
                <Building class="w-10 h-10 text-muted-foreground mb-4 opacity-50" />
                <h5 class="font-bold text-foreground mb-1">Daftar Unit Lembaga</h5>
                <p class="text-xs text-muted-foreground max-w-sm">Fitur untuk mengelola unit sekolah tingkat TK hingga SMA yang bernaung di bawah Yayasan sedang dikerjakan.</p>
              </div>
            </div>

            <!-- TAB: Rekening Donasi (Yayasan) -->
            <div
              v-if="modalActiveTab === 'donasi'"
              class="space-y-6"
            >
              <h4 class="text-sm font-bold text-foreground">Rekening Infaq / Donasi</h4>
              <div class="border-2 border-dashed border-border/50 rounded-2xl p-10 flex flex-col items-center justify-center text-center bg-background/30">
                <HeartHandshake class="w-10 h-10 text-muted-foreground mb-4 opacity-50" />
                <h5 class="font-bold text-foreground mb-1">Pengaturan Donasi & Wakaf</h5>
                <p class="text-xs text-muted-foreground max-w-sm">Fitur QRIS dan informasi rekening transfer untuk keperluan donasi/wakaf Yayasan sedang dalam tahap pengembangan.</p>
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
                <div class="flex items-center gap-2 mb-4">
                  <h5 class="font-bold text-xs text-primary">
                    {{ isAddingSectionItem ? 'Tambah Item Baru' : 'Edit Item' }}
                  </h5>
                  <span class="text-[10px] text-gray-400 font-medium">untuk section</span>
                  <span class="text-[9px] font-extrabold uppercase tracking-widest text-primary bg-primary/15 px-2 py-0.5 rounded-md">
                    {{ form.sections[selectedSectionIndex]?.title || form.sections[selectedSectionIndex]?.type || 'Tanpa Judul' }}
                  </span>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                  <div class="md:col-span-2">
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Judul Item</Label>
                    <Input
                      type="text"
                      v-model="sectionItemForm.title"
                      class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                      placeholder="Masukkan nama atau judul item"
                    />
                  </div>
                  
                  <div v-if="['programs'].includes(activeSectionType)">
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Link (Tautan) - Opsional</Label>
                    <Input
                      type="text"
                      v-model="sectionItemForm.link"
                      class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                      placeholder="https://..."
                    />
                  </div>

                  <div v-if="['stats', 'testimonials'].includes(activeSectionType)">
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">
                      {{ activeSectionType === 'stats' ? 'Angka Statistik' : 'Nilai / Peran' }}
                    </Label>
                    <Input
                      type="text"
                      v-model="sectionItemForm.value"
                      class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                      :placeholder="activeSectionType === 'stats' ? 'misal: 100+' : 'misal: Wali Murid'"
                    />
                  </div>

                  <div v-if="['features'].includes(activeSectionType)">
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Nama Ikon (Opsional)</Label>
                    <Input
                      type="text"
                      v-model="sectionItemForm.icon"
                      class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                      placeholder="misal: star, award, book"
                    />
                  </div>

                  <div v-if="['features', 'programs', 'testimonials', 'faq'].includes(activeSectionType)" class="md:col-span-2">
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">
                      {{ activeSectionType === 'faq' ? 'Jawaban' : 'Deskripsi Singkat' }}
                    </Label>
                    <Textarea
                      v-model="sectionItemForm.description"
                      rows="2"
                      class="w-full px-3 py-2 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                      :placeholder="activeSectionType === 'faq' ? 'Tuliskan jawaban pertanyaan...' : 'Tuliskan deskripsi ringkas...'"
                    ></Textarea>
                  </div>

                  <div v-if="['programs', 'gallery', 'testimonials'].includes(activeSectionType)" class="md:col-span-2">
                    <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Media Visual (Opsional)</Label>
                    <div class="flex items-center gap-4 bg-white/30 dark:bg-background/20 p-3 rounded-xl border border-gray-100 dark:border-white/5">
                      <div class="w-12 h-12 rounded-lg border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 flex items-center justify-center overflow-hidden shrink-0">
                        <img v-if="sectionItemForm.image" :src="sectionItemForm.image" class="w-full h-full object-cover" />
                        <span v-else class="text-lg opacity-40">🖼️</span>
                      </div>
                      <div>
                        <label class="px-4 py-2 inline-flex items-center border border-gray-200 dark:border-white/10 bg-white dark:bg-background/50 hover:bg-gray-50 dark:hover:bg-white/5 rounded-lg font-bold text-xs cursor-pointer text-foreground transition-colors shadow-sm">
                          Pilih File Gambar
                          <input type="file" @change="onSectionItemImageUpload" class="sr-only" accept="image/*" />
                        </label>
                        <p class="text-[10px] text-muted-foreground mt-1.5 font-medium">Format: JPG, PNG, atau WebP.</p>
                      </div>
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
                <VueDraggable v-model="form.sections" item-key="id" class="space-y-6 relative" handle=".section-drag-handle" animation="200">
                  <template #item="{ element: sec, index: secIdx }">
                    <div class="glass-mini p-5 border border-gray-100 dark:border-white/10 rounded-2xl space-y-3 section-drag-handle transition-colors cursor-grab active:cursor-grabbing hover:bg-white/50 dark:hover:bg-white/5 group/section">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 sm:gap-3">
                          <div class="p-1.5 rounded-lg text-gray-400 dark:text-white/30 group-hover/section:text-primary group-has-[.item-drag-handle:hover]/section:!text-gray-400 dark:group-has-[.item-drag-handle:hover]/section:!text-white/30 transition-colors cursor-grab active:cursor-grabbing" title="Tahan dan geser untuk memindahkan urutan">
                            <GripVertical class="w-5 h-5" />
                          </div>
                          <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/15 px-2 py-0.5 rounded">{{ sec.type || 'SECTION' }}</span>
                            <div class="font-extrabold text-sm text-foreground mt-1">{{ sec.title || 'Section Tanpa Judul' }}</div>
                          </div>
                        </div>
                        <div class="flex items-center gap-2">
                          <button type="button" @click="openSectionItemEditor(secIdx)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary/15 hover:bg-primary/25 text-primary font-bold text-xs transition-colors">
                            <Plus class="w-3.5 h-3.5" /> Tambah Item
                          </button>
                        </div>
                      </div>
                      
                      <VueDraggable v-if="sec.items" v-model="sec.items" item-key="id" class="space-y-2 relative" handle=".item-drag-handle" animation="200">
                        <template #item="{ element: item, index: itemIdx }">
                          <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-white/5 text-xs item-drag-handle transition-colors cursor-grab active:cursor-grabbing hover:bg-gray-50 dark:hover:bg-white/10 group/item">
                            <div class="flex gap-2 sm:gap-3 items-center">
                              <div class="p-1.5 rounded-lg text-gray-400 dark:text-white/30 group-hover/item:text-primary transition-colors cursor-grab active:cursor-grabbing" title="Tahan dan geser untuk memindahkan urutan">
                                <GripVertical class="w-4 h-4" />
                              </div>
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
                              <button type="button" @click="confirmDeleteSectionItem(secIdx, item)" class="p-1.5 rounded-lg hover:bg-destructive/10 text-destructive transition-colors"><Trash2 class="w-3.5 h-3.5" /></button>
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
              <p class="text-[10px] text-muted-foreground -mt-5">Info dengan label biru otomatis diambil dari pengaturan profil utama instansi.</p>
              
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Email {{ selectedItemForEdit?.type || 'Instansi' }} <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span></Label>
                  <Input
                    type="email"
                    v-model="form.contact_email"
                    class="rounded-xl text-xs"
                    :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'Contoh: info@yayasan.com' : 'Contoh: info@sekolah.com'"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Telepon <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span></Label>
                  <Input
                    type="text"
                    v-model="form.contact_phone"
                    class="rounded-xl text-xs"
                    placeholder="Contoh: 021-1234567"
                  />
                </div>
                <div class="md:col-span-2">
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Alamat Lengkap <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span></Label>
                  <Textarea
                    v-model="form.contact_address"
                    rows="2"
                    class="rounded-xl text-xs"
                    :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'Masukkan alamat lengkap yayasan di sini...' : 'Masukkan alamat lengkap sekolah di sini...'"
                  ></Textarea>
                </div>
                <div class="md:col-span-2">
                  <Label class="text-xs text-muted-foreground mb-1.5 block"
                    >Google Maps Embed URL</Label
                  ><Textarea
                    v-model="form.contact_maps_embed"
                    rows="2"
                    class="rounded-xl text-xs"
                    placeholder="Salin tag <iframe src='...'> dari Google Maps"
                  ></Textarea>
                </div>
              </div>

              <div class="grid md:grid-cols-2 gap-4 pt-2">
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Instagram URL <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span></Label>
                  <Input
                    type="text"
                    v-model="form.social_instagram"
                    class="rounded-xl text-xs"
                    :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'https://instagram.com/yayasan' : 'https://instagram.com/sekolah'"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">Facebook URL <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span></Label>
                  <Input
                    type="text"
                    v-model="form.social_facebook"
                    class="rounded-xl text-xs"
                    :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'https://facebook.com/yayasan' : 'https://facebook.com/sekolah'"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">TikTok URL</Label>
                  <Input
                    type="text"
                    v-model="form.social_tiktok"
                    class="rounded-xl text-xs"
                    :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'https://tiktok.com/@yayasan' : 'https://tiktok.com/@sekolah'"
                  />
                </div>
                <div>
                  <Label class="text-xs text-muted-foreground mb-1.5 block">YouTube URL</Label>
                  <Input
                    type="text"
                    v-model="form.social_youtube"
                    class="rounded-xl text-xs"
                    :placeholder="selectedItemForEdit?.type === 'Yayasan' ? 'https://youtube.com/c/yayasan' : 'https://youtube.com/c/sekolah'"
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

  <!-- Alert Dialog Konfirmasi Hapus Item -->
  <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
    <AlertDialogContent class="sm:max-w-md">
      <AlertDialogHeader>
        <AlertDialogTitle>Konfirmasi Penghapusan</AlertDialogTitle>
        <AlertDialogDescription v-if="itemToDelete">
          Apakah Anda yakin ingin menghapus <strong>{{ itemToDelete.title || 'item ini' }}</strong> dari section? 
          Tindakan ini tidak dapat dibatalkan jika Anda menyimpan konfigurasi.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel @click="showDeleteDialog = false">Batal</AlertDialogCancel>
        <AlertDialogAction @click="executeDeleteSectionItem" class="bg-destructive hover:bg-destructive/90 text-white border-0">Ya, Hapus Item</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

