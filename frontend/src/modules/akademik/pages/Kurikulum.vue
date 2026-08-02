<script setup>
import { ref, onMounted, computed } from 'vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import {
  BookOpen,
  GraduationCap,
  Sparkles,
  Layers,
  Search,
  CheckCircle2,
  ListChecks,
  ChevronRight,
  School,
  BookMarked
} from 'lucide-vue-next'
import { fetchCurriculums, fetchCurriculumDetail } from '@/services/curriculumService'
import { glassSlide, glassFade } from '@/config/motion'

// --- State ---
const curriculums = ref([])
const isLoading = ref(false)
const searchQuery = ref('')
const selectedTab = ref('all') // 'all' | 'SD' | 'SMP'

// Detail Modal State
const isDetailOpen = ref(false)
const selectedCurriculum = ref(null)
const isLoadingDetail = ref(false)

async function loadData() {
  isLoading.value = true
  try {
    const res = await fetchCurriculums({ is_active: true })
    curriculums.value = res.data || []
  } catch (err) {
    console.error('Gagal memuat data kurikulum:', err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadData()
})

// --- Computed Filters & Stats ---
const filteredCurriculums = computed(() => {
  return curriculums.value.filter(item => {
    const matchesSearch =
      !searchQuery.value.trim() ||
      item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.code.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesLevel =
      selectedTab.value === 'all' ||
      item.level === selectedTab.value ||
      item.level === 'ALL'

    return matchesSearch && matchesLevel
  })
})

const totalCurriculums = computed(() => curriculums.value.length)
const totalSD = computed(() => curriculums.value.filter(c => c.level === 'SD' || c.level === 'ALL').length)
const totalSMP = computed(() => curriculums.value.filter(c => c.level === 'SMP' || c.level === 'ALL').length)
const totalMapelWajib = computed(() => {
  return curriculums.value.reduce((acc, curr) => acc + (curr.curriculum_subjects_count || 0), 0)
})

async function openDetail(curriculumId) {
  isLoadingDetail.value = true
  isDetailOpen.value = true
  try {
    const res = await fetchCurriculumDetail(curriculumId)
    selectedCurriculum.value = res.data
  } catch (err) {
    console.error('Error loading detail:', err)
  } finally {
    isLoadingDetail.value = false
  }
}
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 w-full mx-auto p-1 pb-12 text-left"
  >
    <!-- Header -->
    <PageHeader
      title="Jenis Kurikulum & Mapel Wajib"
      description="Daftar acuan kurikulum resmi Indonesia (Kurikulum Merdeka 2024 & K13) beserta mata pelajaran wajib per jenjang SD & SMP."
    />

    <!-- Stat Cards -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
      class="grid gap-4 grid-cols-1 sm:grid-cols-4"
    >
      <StatCard
        label="TOTAL KURIKULUM"
        :value="String(totalCurriculums)"
        :icon="BookOpen"
        illustration="globe"
        variant="primary"
      />
      <StatCard
        label="JENJANG SD"
        :value="String(totalSD)"
        :icon="School"
        illustration="school_bell"
        variant="emerald"
      />
      <StatCard
        label="JENJANG SMP"
        :value="String(totalSMP)"
        :icon="GraduationCap"
        illustration="abc_board"
        variant="violet"
      />
      <StatCard
        label="MAPEL WAJIB ACUAN"
        :value="String(totalMapelWajib)"
        :icon="ListChecks"
        illustration="notes"
        variant="amber"
      />
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
      <!-- Tabs Filter -->
      <div class="flex items-center gap-1.5 p-1 rounded-xl bg-accent/50 dark:bg-zinc-900 border border-border dark:border-zinc-800 w-full sm:w-auto">
        <button
          type="button"
          @click="selectedTab = 'all'"
          class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5"
          :class="selectedTab === 'all' ? 'bg-background dark:bg-zinc-800 text-foreground dark:text-zinc-100 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
        >
          <Layers class="h-3.5 w-3.5" />
          Semua Jenjang
        </button>
        <button
          type="button"
          @click="selectedTab = 'SD'"
          class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5"
          :class="selectedTab === 'SD' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/20' : 'text-muted-foreground hover:text-foreground'"
        >
          <School class="h-3.5 w-3.5" />
          Jenjang SD
        </button>
        <button
          type="button"
          @click="selectedTab = 'SMP'"
          class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5"
          :class="selectedTab === 'SMP' ? 'bg-violet-500/10 text-violet-600 dark:text-violet-400 font-bold border border-violet-500/20' : 'text-muted-foreground hover:text-foreground'"
        >
          <GraduationCap class="h-3.5 w-3.5" />
          Jenjang SMP
        </button>
      </div>

      <!-- Search Input -->
      <div class="relative w-full sm:w-72">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
        <Input
          v-model="searchQuery"
          placeholder="Cari kurikulum..."
          class="pl-9 bg-background dark:bg-zinc-900 border-input dark:border-zinc-800 text-xs"
        />
      </div>
    </div>

    <!-- Curriculum Cards Grid -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
      class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3"
    >
      <Card
        v-for="curr in filteredCurriculums"
        :key="curr.id"
        class="border border-border dark:border-zinc-800 hover:border-primary/50 dark:hover:border-primary/40 transition-all duration-300 shadow-sm hover:shadow-md group flex flex-col justify-between"
      >
        <CardHeader class="pb-3">
          <div class="flex items-start justify-between gap-2">
            <div class="space-y-1">
              <Badge
                variant="outline"
                class="text-[10px] uppercase font-bold tracking-wide px-2 py-0.5"
                :class="{
                  'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30': curr.level === 'SD',
                  'bg-violet-500/10 text-violet-600 dark:text-violet-400 border-violet-500/30': curr.level === 'SMP',
                  'bg-primary/10 text-primary border-primary/30': curr.level === 'ALL'
                }"
              >
                {{ curr.level === 'ALL' ? 'SD & SMP' : `JENJANG ${curr.level}` }}
              </Badge>
              <CardTitle class="text-base font-bold text-foreground dark:text-zinc-100 group-hover:text-primary transition-colors">
                {{ curr.name }}
              </CardTitle>
            </div>
            <div class="h-9 w-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
              <BookMarked class="h-5 w-5" />
            </div>
          </div>
          <CardDescription class="text-xs line-clamp-2 mt-1.5 text-muted-foreground dark:text-zinc-400">
            {{ curr.description || 'Kurikulum acuan pendidikan nasional terdaftar.' }}
          </CardDescription>
        </CardHeader>

        <CardContent class="pt-0 space-y-4">
          <div class="p-3 rounded-lg bg-accent/40 dark:bg-zinc-800/40 border border-border/50 dark:border-zinc-800/50 flex items-center justify-between text-xs">
            <span class="text-muted-foreground dark:text-zinc-400 flex items-center gap-1.5">
              <ListChecks class="h-4 w-4 text-primary" />
              Jumlah Mapel Wajib:
            </span>
            <span class="font-bold text-foreground dark:text-zinc-100">
              {{ curr.curriculum_subjects_count || 0 }} Mata Pelajaran
            </span>
          </div>

          <Button
            variant="outline"
            size="sm"
            @click="openDetail(curr.id)"
            class="w-full gap-1.5 border-input dark:border-zinc-800 hover:bg-primary hover:text-primary-foreground transition-all"
          >
            Lihat Mapel Wajib
            <ChevronRight class="h-4 w-4" />
          </Button>
        </CardContent>
      </Card>
    </div>

    <!-- Detail Dialog -->
    <Dialog :open="isDetailOpen" @update:open="isDetailOpen = false">
      <DialogContent class="sm:max-w-2xl max-h-[85vh] overflow-y-auto bg-card dark:bg-zinc-900 border border-border dark:border-zinc-800">
        <DialogHeader>
          <div class="flex items-center gap-2">
            <Badge variant="outline" class="text-[10px] uppercase font-bold px-2 py-0.5">
              {{ selectedCurriculum?.code }}
            </Badge>
            <Badge
              v-if="selectedCurriculum?.level"
              variant="outline"
              class="text-[10px] uppercase font-bold px-2 py-0.5"
              :class="{
                'bg-emerald-500/10 text-emerald-600 border-emerald-500/30': selectedCurriculum?.level === 'SD',
                'bg-violet-500/10 text-violet-600 border-violet-500/30': selectedCurriculum?.level === 'SMP'
              }"
            >
              {{ selectedCurriculum?.level }}
            </Badge>
          </div>
          <DialogTitle class="text-lg font-bold text-foreground dark:text-zinc-100 mt-1">
            {{ selectedCurriculum?.name }}
          </DialogTitle>
          <DialogDescription class="text-xs text-muted-foreground dark:text-zinc-400">
            {{ selectedCurriculum?.description }}
          </DialogDescription>
        </DialogHeader>

        <div v-if="isLoadingDetail" class="py-12 text-center text-xs text-muted-foreground">
          Memuat daftar mata pelajaran wajib...
        </div>

        <div v-else-if="selectedCurriculum" class="space-y-4 py-2">
          <h4 class="text-sm font-semibold text-foreground dark:text-zinc-200 flex items-center gap-1.5">
            <CheckCircle2 class="h-4 w-4 text-emerald-500" />
            Daftar Mata Pelajaran Wajib Standar Nasional
          </h4>

          <div class="border border-border dark:border-zinc-800 rounded-xl overflow-hidden divide-y divide-border dark:divide-zinc-800">
            <div
              v-for="(subject, idx) in selectedCurriculum.curriculum_subjects"
              :key="subject.id"
              class="p-3 flex items-center justify-between bg-card dark:bg-zinc-900/60 hover:bg-accent/30 dark:hover:bg-zinc-800/40 transition-colors"
            >
              <div class="flex items-center gap-3">
                <span class="h-6 w-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center shrink-0">
                  {{ idx + 1 }}
                </span>
                <div>
                  <h5 class="text-xs font-semibold text-foreground dark:text-zinc-100">
                    {{ subject.name }}
                  </h5>
                  <span class="text-[10px] text-muted-foreground dark:text-zinc-400">
                    Kode: {{ subject.code }} {{ subject.phase ? `• ${subject.phase}` : '' }}
                  </span>
                </div>
              </div>

              <Badge variant="outline" class="text-[10px] font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30">
                Wajib Nasional
              </Badge>
            </div>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" type="button" @click="isDetailOpen = false">
            Tutup
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
