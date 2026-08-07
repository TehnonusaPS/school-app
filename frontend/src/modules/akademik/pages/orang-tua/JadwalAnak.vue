<script setup>
import { computed } from 'vue'
import { 
  GraduationCap, 
  BookOpen, 
  FileText, 
  PartyPopper,
  Sparkles,
  RefreshCw,
  ClipboardList
} from 'lucide-vue-next'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import ParentAgendaCard from '@/components/jadwal/ParentAgendaCard.vue'
import ParentExamCard from '@/components/jadwal/ParentExamCard.vue'
import ParentLessonCard from '@/components/jadwal/ParentLessonCard.vue'
import JadwalEmptyState from '@/components/jadwal/JadwalEmptyState.vue'
import { useAuthStore } from '@/stores/authStore'
import { useJadwalAnak } from '@/modules/akademik/composables/useJadwalAnak'

const authStore = useAuthStore()
const isSiswa = computed(() => authStore.user?.role === 'siswa')

const headerTitle = computed(() => isSiswa.value ? 'Agenda Saya' : 'Jadwal & Agenda Anak')
const headerDescription = computed(() => isSiswa.value 
  ? 'Ringkasan jadwal pelajaran mingguan, tugas, ujian, serta kegiatan sekolah Anda.'
  : 'Ringkasan jadwal pelajaran, tugas, ujian, serta kegiatan sekolah anak Anda.'
)

const {
  loading,
  children,
  selectedChildId,
  selectedChild,
  mainSectionTab,
  daysList,
  selectedDayTab,
  activeDayLessons,
  weeklySchedule,
  upcomingExams,
  upcomingEvents,
  tugasList,
  kegiatanList,
  ujianHarianList,
  expandedExamId,
  toggleExamExpand,
  formatDateIndo
} = useJadwalAnak()

// Clean navigation tabs configuration
const navTabs = computed(() => [
  { id: 'jadwal', label: 'Jadwal Mingguan', icon: BookOpen, count: 0 },
  { id: 'ujian', label: 'Ujian', icon: FileText, iconColor: 'text-violet-500', count: upcomingExams.value.length + ujianHarianList.value.length },
  { id: 'tugas', label: 'Tugas', icon: ClipboardList, iconColor: 'text-emerald-500', count: tugasList.value.length },
  { id: 'kegiatan', label: 'Kegiatan', icon: Sparkles, iconColor: 'text-amber-500', count: kegiatanList.value.length },
  { id: 'libur', label: 'Libur & Agenda', icon: PartyPopper, iconColor: 'text-rose-500', count: upcomingEvents.value.length }
])
</script>

<template>
  <div class="space-y-6 pb-12 w-full mx-auto">
    <!-- Header -->
    <PageHeader
      :title="headerTitle"
      :description="headerDescription"
    />

    <!-- CONTROL BAR & MAIN TABS -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 p-2 sm:p-2.5 rounded-2xl bg-card/60 border border-border/60 shadow-2xs backdrop-blur-md">
      <!-- Child Switcher -->
      <div class="flex items-center gap-2 px-2 py-1">
        <div class="size-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold shrink-0">
          <GraduationCap class="size-4.5" />
        </div>
        
        <div class="flex items-center gap-2 flex-wrap">
          <span class="text-sm font-extrabold text-foreground">
            {{ selectedChild?.name || 'Siswa' }}
          </span>

          <span v-if="selectedChild?.classroom_name" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-muted/60 text-muted-foreground text-xs font-bold">
            {{ selectedChild.classroom_name }}
            <template v-if="selectedChild?.room"> • {{ selectedChild.room }}</template>
          </span>

          <div v-if="!isSiswa && children.length > 1" class="ml-1">
            <select
              v-model="selectedChildId"
              class="h-7 px-2 text-xs rounded-lg bg-background/80 border border-border/80 font-bold text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer"
            >
              <option v-for="c in children" :key="c.id" :value="c.id">
                Ubah Anak: {{ c.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs (Loop) -->
      <div class="flex items-center gap-1 overflow-x-auto p-1 rounded-xl bg-muted/60 border border-border/40 shrink-0 no-scrollbar">
        <button
          v-for="tab in navTabs"
          :key="tab.id"
          type="button"
          @click="mainSectionTab = tab.id"
          class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
          :class="mainSectionTab === tab.id 
            ? 'bg-card/90 text-foreground shadow-2xs border border-border/50' 
            : 'text-muted-foreground hover:text-foreground'"
        >
          <component :is="tab.icon" class="size-3.5" :class="tab.iconColor" />
          <span>{{ tab.label }}</span>
          <Badge v-if="tab.count > 0" variant="secondary" class="text-[10px] px-1.5 py-0 font-extrabold h-4">
            {{ tab.count }}
          </Badge>
        </button>
      </div>
    </div>

    <!-- LOADING STATE -->
    <div v-if="loading" class="py-16 text-center space-y-3 bg-card/60 border border-border/60 rounded-2xl shadow-xs backdrop-blur-md">
      <RefreshCw class="size-7 animate-spin text-primary mx-auto" />
      <p class="text-xs font-medium text-muted-foreground">Memuat data jadwal anak...</p>
    </div>

    <div v-else>
      <!-- ========================================================================= -->
      <!-- TAB 1: JADWAL PELAJARAN MINGGUAN -->
      <!-- ========================================================================= -->
      <div v-if="mainSectionTab === 'jadwal'" class="space-y-4">
        <!-- Day Tabs (Senin - Sabtu) -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar">
          <button
            v-for="d in daysList"
            :key="d.num"
            type="button"
            @click="selectedDayTab = d.num"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 cursor-pointer border"
            :class="selectedDayTab === d.num 
              ? 'bg-primary text-primary-foreground border-primary shadow-2xs' 
              : 'bg-card/60 text-muted-foreground border-border/60 hover:border-border hover:bg-muted/40 backdrop-blur-md'"
          >
            <span>{{ d.name }}</span>
            <span 
              v-if="weeklySchedule?.[d.num]"
              class="size-4.5 rounded-full text-[10px] font-extrabold flex items-center justify-center"
              :class="selectedDayTab === d.num ? 'bg-primary-foreground/20 text-primary-foreground' : 'bg-muted text-muted-foreground'"
            >
              {{ weeklySchedule[d.num]?.filter(x => !x.is_break).length || 0 }}
            </span>
          </button>
        </div>

        <Card class="border-border/60 shadow-2xs bg-card/60 backdrop-blur-md">
          <CardContent class="p-4 sm:p-5">
            <JadwalEmptyState
              v-if="activeDayLessons.length === 0"
              :icon="BookOpen"
              title="Tidak Ada Pelajaran"
              description="Belum ada alokasi jam pelajaran resmi pada hari ini."
            />

            <div v-else class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
              <ParentLessonCard
                v-for="lesson in activeDayLessons"
                :key="lesson.id"
                :lesson="lesson"
              />
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 2: UJIAN (UJIAN HARIAN & UJIAN RESMI) -->
      <!-- ========================================================================= -->
      <div v-else-if="mainSectionTab === 'ujian'" class="space-y-5">
        <JadwalEmptyState
          v-if="upcomingExams.length === 0 && ujianHarianList.length === 0"
          :icon="FileText"
          iconColor="text-violet-500"
          title="Belum Ada Ujian"
          description="Jadwal ujian harian guru maupun ujian semester akan muncul di sini."
        />

        <div v-else class="space-y-5">
          <!-- Ujian Harian Cards (3 Columns Desktop, 1 Column Mobile) -->
          <div v-if="ujianHarianList.length > 0" class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            <ParentAgendaCard
              v-for="uh in ujianHarianList"
              :key="`uh-${uh.id}`"
              type="ujian_harian"
              :title="uh.title"
              :date="uh.date"
              :classroomName="uh.classroom_name"
              :subjectName="uh.subject_name"
              :description="uh.description"
            />
          </div>

          <!-- Ujian Resmi Sekolah (UTS / UAS) -->
          <div v-if="upcomingExams.length > 0" class="space-y-3">
            <h3 v-if="ujianHarianList.length > 0" class="text-xs font-extrabold text-foreground uppercase tracking-wider">
              UJIAN RESMI SEKOLAH (UTS / UAS)
            </h3>
            <ParentExamCard
              v-for="exam in upcomingExams"
              :key="exam.id"
              :exam="exam"
              :isExpanded="expandedExamId === exam.id"
              :formatDateIndo="formatDateIndo"
              @toggle-expand="toggleExamExpand(exam.id)"
            />
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 3: TUGAS KELAS -->
      <!-- ========================================================================= -->
      <div v-else-if="mainSectionTab === 'tugas'" class="space-y-4">
        <JadwalEmptyState
          v-if="tugasList.length === 0"
          :icon="ClipboardList"
          iconColor="text-emerald-500"
          title="Belum Ada Tugas"
          description="Tugas pengumpulan dari guru akan ditampilkan di sini."
        />

        <div v-else class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
          <ParentAgendaCard
            v-for="task in tugasList"
            :key="task.id"
            type="tugas"
            :title="task.title"
            :date="task.date"
            :classroomName="task.classroom_name"
            :subjectName="task.subject_name"
            :description="task.description"
          />
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 4: KEGIATAN KELAS -->
      <!-- ========================================================================= -->
      <div v-else-if="mainSectionTab === 'kegiatan'" class="space-y-4">
        <JadwalEmptyState
          v-if="kegiatanList.length === 0"
          :icon="Sparkles"
          iconColor="text-amber-500"
          title="Belum Ada Kegiatan Kelas"
          description="Proyek dan kegiatan khusus kelas akan tercantum di sini."
        />

        <div v-else class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
          <ParentAgendaCard
            v-for="kg in kegiatanList"
            :key="kg.id"
            type="kegiatan"
            :title="kg.title"
            :date="kg.date"
            :classroomName="kg.classroom_name"
            :subjectName="kg.subject_name"
            :description="kg.description"
          />
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 5: LIBUR & AGENDA SEKOLAH -->
      <!-- ========================================================================= -->
      <div v-else-if="mainSectionTab === 'libur'" class="space-y-4">
        <JadwalEmptyState
          v-if="upcomingEvents.length === 0"
          :icon="PartyPopper"
          iconColor="text-rose-500"
          title="Belum Ada Agenda Libur"
          description="Agenda libur nasional dan kegiatan umum sekolah akan tercantum di sini."
        />

        <div v-else class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
          <ParentAgendaCard
            v-for="ev in upcomingEvents"
            :key="ev.id"
            :type="ev.is_holiday ? 'libur' : 'agenda_sekolah'"
            :title="ev.title"
            :date="ev.start_date"
            :endDate="ev.end_date"
            :description="ev.description"
          />
        </div>
      </div>
    </div>
  </div>
</template>
