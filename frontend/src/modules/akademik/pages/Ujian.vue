<script setup>
import { useExamSchedule } from '../composables/useExamSchedule'

import PageHeader from '@/components/page-header/PageHeader.vue'
import ExamSelectorBar from '../components/ujian/ExamSelectorBar.vue'
import ExamDateTabs from '../components/ujian/ExamDateTabs.vue'
import ExamSessionMatrix from '../components/ujian/ExamSessionMatrix.vue'
import ExamConfirmSaveModal from '../components/ujian/ExamConfirmSaveModal.vue'
import ExamStickySaveBar from '../components/ujian/ExamStickySaveBar.vue'
import ExamTimelineView from '../components/ujian/ExamTimelineView.vue'

import { Card, CardContent } from '@/components/ui/card'
import { Layers, RefreshCw } from 'lucide-vue-next'

const {
  canEditExams,
  canViewExams,
  canManageExams,
  loading,
  loadingEvents,
  saving,
  showConfirmSaveModal,
  academicYears,
  selectedAcademicYearId,
  selectedAcademicYear,
  examEvents,
  selectedEventId,
  selectedEvent,
  selectedDateTab,
  availableGrades,
  availableSubjects,
  subjectOptions,
  getSubjectOptionsForGrade,
  noExamDays,
  toggleNoExamDay,
  editorSessions,
  myScheduleData,
  selectedGradeFilter,
  eventDates,
  visibleDates,
  emptyDates,
  totalAssignedSubjects,
  formatDateIndo,
  getDateStatus,
  addSessionForDate,
  removeSession,
  confirmSave,
  executeSaveAllSessions,
  printSchedule
} = useExamSchedule()
</script>

<template>
  <div class="space-y-6 pb-24">
    <!-- PRINT ONLY HEADER (Visible only on print preview) -->
    <div class="hidden print:block text-center space-y-2 mb-6 pb-4 border-b">
      <h1 class="text-xl font-bold uppercase tracking-wider text-black">JADWAL UJIAN DETAIL SEKOLAH</h1>
      <p v-if="selectedAcademicYear" class="text-sm font-semibold text-gray-700">
        Tahun Ajaran {{ selectedAcademicYear.name }}
      </p>
      <p v-if="selectedEvent" class="text-sm font-bold text-gray-900">
        {{ selectedEvent.title }} ({{ formatDateIndo(selectedEvent.start_date) }} - {{ formatDateIndo(selectedEvent.end_date) }})
      </p>
    </div>

    <!-- Standard Page Header (Screen Only) -->
    <div class="print:hidden">
      <PageHeader 
        title="Jadwal Ujian Detail (UTS / UAS)"
        description="Susun dan pantau jadwal pelaksanaan mata pelajaran per tanggal dan sesi ujian sekolah."
      />
    </div>

    <!-- MANAGER / REVIEWER VIEW: Matrix Editor (Admin & Kepala Sekolah) -->
    <div v-if="canManageExams" class="space-y-6">
      <!-- Step 1 & 2 Selector Bar (with Print button on bottom-left) -->
      <ExamSelectorBar
        v-model:academicYearId="selectedAcademicYearId"
        v-model:eventId="selectedEventId"
        :academic-years="academicYears"
        :exam-events="examEvents"
        :loading-events="loadingEvents"
        :format-date-indo="formatDateIndo"
        @print="printSchedule"
      />

      <!-- Loading State -->
      <div v-if="loading || loadingEvents" class="py-16 text-center space-y-3 bg-card border border-border/60 rounded-2xl shadow-xs">
        <RefreshCw class="size-8 animate-spin text-primary mx-auto" />
        <p class="text-sm font-medium text-muted-foreground">Memuat struktur jadwal & sesi ujian...</p>
      </div>

      <!-- Step Enforcement Empty State -->
      <Card v-else-if="!selectedEvent" class="border-border/60 shadow-xs py-12 text-center">
        <CardContent class="space-y-3">
          <div class="size-14 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mx-auto">
            <Layers class="size-7" />
          </div>
          <h3 class="text-base font-bold">Pilih Kalender Akademik & Jenis Ujian</h3>
          <p class="text-sm text-muted-foreground max-w-md mx-auto leading-relaxed">
            Silakan pilih **Tahun Ajaran** dan **Jenis Ujian** (seperti UTS atau UAS) pada dropdown di atas untuk membuka form penyusunan jadwal detail per mata pelajaran.
          </p>
        </CardContent>
      </Card>

      <!-- Step 3: Main Form Editor (Only shown when Exam Event is selected) -->
      <div v-else class="space-y-6">
        <!-- Date Navigation Tabs -->
        <ExamDateTabs
          v-model:selectedDateTab="selectedDateTab"
          :selected-event="selectedEvent"
          :event-dates="eventDates"
          :total-assigned-subjects="totalAssignedSubjects"
          :get-date-status="getDateStatus"
          :format-date-indo="formatDateIndo"
        />

        <!-- Sessions & Subject Matrix -->
        <ExamSessionMatrix
          :visible-dates="visibleDates"
          :editor-sessions="editorSessions"
          :available-grades="availableGrades"
          :available-subjects="availableSubjects"
          :subject-options="subjectOptions"
          :get-subject-options-for-grade="getSubjectOptionsForGrade"
          :no-exam-days="noExamDays"
          :can-edit-exams="canEditExams"
          :get-date-status="getDateStatus"
          :format-date-indo="formatDateIndo"
          @add-session="addSessionForDate"
          @remove-session="removeSession"
          @toggle-no-exam-day="toggleNoExamDay"
        />
      </div>

      <!-- Sticky Floating Bottom Save Bar (Only for Admin / Editor) -->
      <ExamStickySaveBar
        :can-manage-exams="canEditExams"
        :selected-event="selectedEvent"
        :total-assigned-subjects="totalAssignedSubjects"
        :saving="saving"
        @confirm-save="confirmSave"
      />
    </div>

    <!-- STUDENT / TEACHER / PUBLIC VIEW: Timeline Cards -->
    <ExamTimelineView
      v-else
      v-model:gradeFilter="selectedGradeFilter"
      :loading="loading"
      :my-schedule-data="myScheduleData"
      :format-date-indo="formatDateIndo"
    />

    <!-- Confirmation Modal Dialog -->
    <ExamConfirmSaveModal
      v-model:open="showConfirmSaveModal"
      :selected-event="selectedEvent"
      :event-dates="eventDates"
      :total-assigned-subjects="totalAssignedSubjects"
      :empty-dates="emptyDates"
      :saving="saving"
      :format-date-indo="formatDateIndo"
      @execute="executeSaveAllSessions"
    />
  </div>
</template>
