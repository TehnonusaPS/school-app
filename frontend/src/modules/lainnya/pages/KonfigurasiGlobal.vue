<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/page-header/PageHeader.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Info, ArrowRight, Filter, Download, Edit, Lock } from 'lucide-vue-next'

const auth = useAuthStore()
const router = useRouter()

// Mock Data for Template Library
const templates = ref([
  {
    id: 'tpl-1',
    name: 'Classic Academic',
    description: 'Desain elegan untuk institusi dengan sejarah panjang dan nilai tradisional.',
    image: 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=600&auto=format&fit=crop',
    colors: ['bg-red-700', 'bg-yellow-500'],
    isActive: false
  },
  {
    id: 'tpl-2',
    name: 'Clean Minimalist',
    description: 'Fokus pada kejelasan informasi dengan tata letak yang bersih dan lapang.',
    image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=600&auto=format&fit=crop',
    colors: ['bg-emerald-600', 'bg-slate-100'],
    isActive: false
  },
  {
    id: 'tpl-3',
    name: 'Professional Corporate',
    description: 'Tampilan profesional yang berwibawa untuk sekolah kejuruan atau sekolah internasional.',
    image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=600&auto=format&fit=crop',
    colors: ['bg-blue-900', 'bg-slate-300'],
    isActive: false
  }
])

// Mock Data for School Mapping
const schoolMappings = ref([
  {
    id: 'SCH-2023-001',
    name: 'SMA Nusantara',
    template: 'Modern Institutional',
    status: 'Aktif',
    lastUpdated: '12 Okt 2023, 14:30',
    avatarLetter: 'SN',
    avatarColor: 'bg-violet-100 text-violet-700',
    indicatorColor: 'bg-emerald-500'
  },
  {
    id: 'SCH-2023-002',
    name: 'SMP Global',
    template: 'Classic Academic',
    status: 'Aktif',
    lastUpdated: '05 Okt 2023, 09:15',
    avatarLetter: 'SG',
    avatarColor: 'bg-emerald-100 text-emerald-700',
    indicatorColor: 'bg-amber-500'
  },
  {
    id: 'SCH-2023-003',
    name: 'SD Harapan',
    template: 'Modern Institutional',
    status: 'Aktif',
    lastUpdated: '28 Sep 2023, 16:45',
    avatarLetter: 'SH',
    avatarColor: 'bg-blue-100 text-blue-700',
    indicatorColor: 'bg-emerald-500'
  }
])

// State and Data for DataTableCard
const filterValues = ref({})
const page = ref(1)
const perPage = ref(5)

const columns = [
  { key: 'school', label: 'NAMA SEKOLAH' },
  { key: 'template', label: 'TEMPLATE SAAT INI' },
  { key: 'status', label: 'STATUS' },
  { key: 'lastUpdated', label: 'TERAKHIR DIPERBARUI' },
  { key: 'actions', label: 'AKSI' }
]

const filters = [
  {
    key: 'search',
    type: 'search',
    placeholder: 'Cari Sekolah...'
  }
]

const actions = [
  {
    label: 'Filter',
    icon: Filter,
    variant: 'outline',
    class: 'border-primary/20 text-primary bg-primary/5 hover:bg-primary/10'
  },
  {
    label: 'Ekspor',
    icon: Download,
    variant: 'outline',
    class: 'border-primary/20 text-primary bg-primary/5 hover:bg-primary/10'
  }
]

const paginatedSchools = computed(() => {
  const start = (page.value - 1) * perPage.value
  return schoolMappings.value.slice(start, start + perPage.value)
})

const handleEdit = (id, item) => {
  console.log('Edit template for school:', item.name)
}
</script>

<template>
  <div v-if="auth.user?.role === 'superadmin'" class="space-y-8 pb-10">
    <PageHeader 
      title="Konfigurasi Global - Template Landing Page" 
      description="Atur tema dan template website landing page untuk semua sekolah" 
    />

    <!-- Top Section: Active Template -->
    <div class="mb-8">
      
      <!-- Active Template Card -->
      <Card class="flex flex-col shadow-xs border-border/60">
        <CardContent class="p-6 flex-1 flex flex-col">
          <div class="flex justify-between items-start mb-4">
            <Badge variant="secondary" class="bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 border-0 rounded-full font-semibold">
              Template Global Aktif
            </Badge>
            <Button size="sm" variant="default">
              <Edit class="w-4 h-4 mr-2" />
              Sesuaikan Editor
            </Button>
          </div>
          
          <div class="mb-4">
            <h2 class="text-2xl font-bold text-foreground">Modern Institutional</h2>
            <p class="text-sm text-muted-foreground mt-1">Versi 2.4.1 (Stable)</p>
          </div>
          
          <div class="rounded-xl overflow-hidden bg-slate-900 border border-slate-800 mt-2 flex-1 relative min-h-[300px]">
            <!-- Placeholder for active template image/preview -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center p-8">
              <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1000&auto=format&fit=crop" alt="Preview" class="w-full h-full object-cover rounded-md opacity-80 shadow-2xl border border-white/10" />
            </div>
            <!-- Pagination dots -->
            <div class="absolute bottom-4 left-6 flex gap-2">
              <div class="w-2.5 h-2.5 rounded-full border border-white/60"></div>
              <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-emerald-400"></div>
              <div class="w-2.5 h-2.5 rounded-full bg-white/80"></div>
            </div>
          </div>
        </CardContent>
      </Card>
      
    </div>

    <!-- Pustaka Template Section -->
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold tracking-tight">Pustaka Template</h2>
        <Button variant="link" class="text-primary font-semibold p-0 h-auto">
          Lihat Semua <ArrowRight class="w-4 h-4 ml-1" />
        </Button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card v-for="tpl in templates" :key="tpl.id" class="shadow-xs border-border/60 overflow-hidden flex flex-col">
          <div class="h-40 relative w-full">
            <img :src="tpl.image" :alt="tpl.name" class="w-full h-full object-cover" />
            <!-- Color Palette Bubbles -->
            <div class="absolute top-3 right-3 flex gap-1 bg-black/40 backdrop-blur-md p-1.5 rounded-full">
              <div v-for="(color, i) in tpl.colors" :key="i" class="w-3.5 h-3.5 rounded-full border border-white" :class="color"></div>
            </div>
          </div>
          <CardContent class="p-5 flex-1 flex flex-col">
            <h3 class="font-bold text-base mb-2">{{ tpl.name }}</h3>
            <p class="text-xs text-muted-foreground leading-relaxed flex-1">
              {{ tpl.description }}
            </p>
          </CardContent>
          <CardFooter class="p-5 pt-0 flex gap-3">
            <Button variant="outline" class="flex-1 border-border hover:bg-muted">Pratinjau</Button>
            <Button class="flex-1" variant="default">Gunakan</Button>
          </CardFooter>
        </Card>
      </div>
    </div>

    <!-- Pemetaan Template Sekolah Section -->
    <div class="space-y-4">
      <h2 class="text-xl font-bold tracking-tight">Pemetaan Template Sekolah</h2>
      <DataTableCard
        :columns="columns"
        :items="paginatedSchools"
        :filters="filters"
        :actions="actions"
        v-model:filterValues="filterValues"
        illustration="atom"
        :page="page"
        :per-page="perPage"
        :total="schoolMappings.length"
        :from="(page - 1) * perPage + 1"
        :to="Math.min(page * perPage, schoolMappings.length)"
        @update:page="page = $event"
        @update:perPage="perPage = $event"
        :on-edit="handleEdit"
      >
        <template #cell-school="{ item }">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-md flex items-center justify-center text-xs font-bold shrink-0" :class="item.avatarColor">
              {{ item.avatarLetter }}
            </div>
            <div>
              <p class="font-semibold text-sm">{{ item.name }}</p>
              <p class="text-[10px] text-muted-foreground mt-0.5">ID: {{ item.id }}</p>
            </div>
          </div>
        </template>

        <template #cell-template="{ item }">
          <div class="flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full" :class="item.indicatorColor"></div>
            <span class="text-xs text-muted-foreground font-medium">{{ item.template }}</span>
          </div>
        </template>

        <template #cell-status="{ item }">
          <Badge variant="secondary" class="bg-emerald-50 text-emerald-600 border border-emerald-200/50 hover:bg-emerald-100 font-medium px-2 py-0 text-[10px] rounded-sm">
            {{ item.status }}
          </Badge>
        </template>
      </DataTableCard>
    </div>
  </div>

  <div v-else class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="bg-card border border-border rounded-2xl p-8 max-w-md w-full shadow-sm flex flex-col items-center">
      <div class="size-16 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mb-6 border border-primary/20">
        <Lock class="size-8" />
      </div>
      <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-foreground mb-2">Akses Dibatasi</h2>
      <p class="text-sm text-muted-foreground leading-relaxed mb-8">
        Halaman Konfigurasi Global hanya dapat diakses oleh peran Superadmin. Anda tidak memiliki izin untuk melihat atau mengubah pengaturan ini.
      </p>
      <Button @click="router.push('/dashboard')" size="lg" class="w-full font-semibold rounded-xl transition-all active:scale-[0.98]">
        Kembali ke Dashboard
      </Button>
    </div>
  </div>
</template>
