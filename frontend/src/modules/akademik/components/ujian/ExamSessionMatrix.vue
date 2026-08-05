<script setup>
import { Clock, Plus, Trash2, GraduationCap, CalendarOff, CheckCircle2 } from 'lucide-vue-next'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import FormSelect from '@/components/forms/FormSelect.vue'
import TimeInput24h from '@/components/forms/TimeInput24h.vue'

const props = defineProps({
  visibleDates: { type: Array, required: true },
  editorSessions: { type: Array, required: true },
  availableGrades: { type: Array, required: true },
  availableSubjects: { type: Array, required: true },
  subjectOptions: { type: Array, required: true },
  getSubjectOptionsForGrade: { type: Function, default: null },
  noExamDays: { type: Object, required: true },
  canEditExams: { type: Boolean, default: true },
  getDateStatus: { type: Function, required: true },
  formatDateIndo: { type: Function, required: true }
})

const emit = defineEmits(['add-session', 'remove-session', 'toggle-no-exam-day'])

const getGradeSubjectStr = (session, grade) => {
  const val = session.subjects ? session.subjects[grade] : undefined
  return (val !== undefined && val !== null) ? String(val) : ''
}

const setGradeSubjectStr = (session, grade, val) => {
  if (!session.subjects) session.subjects = {}
  session.subjects[grade] = val || undefined
}
</script>

<template>
  <div class="space-y-6">
    <Card 
      v-for="dateStr in visibleDates" 
      :key="dateStr" 
      class="border-border/60 shadow-xs overflow-hidden"
    >
      <!-- Day Header Bar -->
      <div class="bg-muted/40 px-6 py-4 border-b border-border/50 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="size-9 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm shadow-2xs">
            {{ new Date(dateStr).getDate() }}
          </div>
          <div>
            <h3 class="font-bold text-sm text-foreground flex items-center gap-2">
              {{ formatDateIndo(dateStr) }}
              <span 
                v-if="getDateStatus(dateStr) === 'complete'"
                class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 text-[10px] font-bold inline-flex items-center gap-1"
              >
                <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse" /> Terisi Lengkap
              </span>
              <span 
                v-else
                class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300 text-[10px] font-bold inline-flex items-center gap-1"
              >
                <span class="size-1.5 rounded-full bg-rose-500 animate-pulse" /> Belum Lengkap
              </span>
            </h3>
            <p class="text-xs text-muted-foreground">
              {{ editorSessions.filter(s => s.exam_date === dateStr).length }} Sesi Ujian Dikonfigurasi
            </p>
          </div>
        </div>

        <!-- Controls: Add Session Button -->
        <div class="flex items-center gap-4">
          <Button 
            v-if="canEditExams"
            @click="emit('add-session', dateStr)"
            variant="secondary"
            size="sm"
            class="text-xs font-bold text-primary rounded-xl"
          >
            <Plus class="size-4 mr-1" />
            Tambah Sesi
          </Button>
        </div>
      </div>

      <!-- Content Area -->
      <CardContent class="p-6 space-y-5">
        <!-- SESSIONS MATRIX LIST -->
        <template v-if="true">
          <div 
            v-for="session in editorSessions.filter(s => s.exam_date === dateStr)" 
            :key="session.id || (dateStr + '-' + session.session_number)"
            class="bg-background border border-border/80 rounded-xl p-4 space-y-4 hover:border-primary/40 transition-all shadow-2xs"
          >
            <!-- Session Time & Controls Header -->
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-border/40">
              <div class="flex items-center gap-3">
                <Badge variant="secondary" class="bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-extrabold text-xs">
                  Sesi {{ session.session_number }}
                </Badge>

                <div class="flex items-center gap-2 text-xs font-semibold text-muted-foreground">
                  <Clock class="size-3.5 text-muted-foreground" />
                  <TimeInput24h 
                    v-model="session.start_time" 
                    :disabled="!canEditExams" 
                  />
                  <span>s.d</span>
                  <TimeInput24h 
                    v-model="session.end_time" 
                    :disabled="!canEditExams" 
                  />
                  <span class="text-[10px] text-muted-foreground font-bold">WIB (24 Jam)</span>
                </div>
              </div>

              <Button 
                v-if="canEditExams"
                @click="emit('remove-session', editorSessions.indexOf(session))"
                variant="ghost"
                size="sm"
                class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 p-1.5 h-auto rounded-lg"
                title="Hapus Sesi Ini"
              >
                <Trash2 class="size-4" />
              </Button>
            </div>

            <!-- Session Subject Matrix per Grade using FormSelect -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div 
                v-for="grade in availableGrades" 
                :key="grade" 
                class="space-y-1.5 bg-muted/20 p-3 rounded-xl border border-border/30"
              >
                <div class="flex items-center justify-between text-xs font-bold text-foreground">
                  <span class="flex items-center gap-1.5">
                    <GraduationCap class="size-3.5 text-indigo-500" />
                    Tingkat {{ grade }}
                  </span>
                </div>

                <!-- Unified FormSelect Dropdown -->
                <FormSelect
                  :model-value="getGradeSubjectStr(session, grade)"
                  @update:model-value="setGradeSubjectStr(session, grade, $event)"
                  placeholder="-- Pilih Mata Pelajaran --"
                  :options="getSubjectOptionsForGrade ? getSubjectOptionsForGrade(session, grade) : subjectOptions"
                  :disabled="!canEditExams"
                />
              </div>
            </div>
          </div>

          <!-- Empty Date Sessions Prompt -->
          <div 
            v-if="editorSessions.filter(s => s.exam_date === dateStr).length === 0" 
            class="py-8 text-center text-xs text-muted-foreground space-y-2 bg-muted/10 rounded-xl border border-dashed border-border/60"
          >
            <Clock class="size-6 text-muted-foreground/40 mx-auto" />
            <p>Belum ada sesi ujian untuk tanggal ini.</p>
            <Button 
              v-if="canEditExams"
              @click="emit('add-session', dateStr)"
              variant="outline"
              size="sm"
              class="text-xs font-semibold rounded-xl"
            >
              <Plus class="size-3.5 mr-1" />
              Tambah Sesi Ujian
            </Button>
          </div>
        </template>
      </CardContent>
    </Card>
  </div>
</template>
