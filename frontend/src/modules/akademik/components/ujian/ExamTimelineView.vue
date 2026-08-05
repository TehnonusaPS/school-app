<script setup>
import { Calendar, BookOpen, Filter, RefreshCw } from 'lucide-vue-next'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

defineProps({
  loading: { type: Boolean, default: false },
  myScheduleData: { type: Array, required: true },
  formatDateIndo: { type: Function, required: true }
})

const selectedGradeFilter = defineModel('gradeFilter')
</script>

<template>
  <div class="space-y-6">
    <!-- Filter Bar -->
    <Card class="border-border/60 shadow-xs">
      <CardContent class="p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <Filter class="size-4 text-muted-foreground" />
          <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Filter Tingkat:</span>
          <div class="flex gap-1.5">
            <Button 
              @click="selectedGradeFilter = null"
              :variant="selectedGradeFilter === null ? 'default' : 'secondary'"
              size="sm"
              class="text-xs font-semibold"
            >
              Semua Tingkat
            </Button>
            <Button 
              v-for="g in [10, 11, 12]" 
              :key="g"
              @click="selectedGradeFilter = g"
              :variant="selectedGradeFilter === g ? 'default' : 'secondary'"
              size="sm"
              class="text-xs font-semibold"
            >
              Tingkat {{ g }}
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Schedule Loading -->
    <div v-if="loading" class="py-16 text-center space-y-3 bg-card border border-border/60 rounded-2xl">
      <RefreshCw class="size-8 animate-spin text-primary mx-auto" />
      <p class="text-sm font-medium text-muted-foreground">Memuat jadwal ujian...</p>
    </div>

    <!-- Schedule Empty -->
    <Card v-else-if="myScheduleData.length === 0" class="border-border/60 shadow-xs py-12 text-center">
      <CardContent class="space-y-3">
        <Calendar class="size-10 text-muted-foreground/60 mx-auto" />
        <h3 class="text-base font-bold">Belum Ada Jadwal Ujian Dipublikasikan</h3>
        <p class="text-sm text-muted-foreground max-w-md mx-auto">
          Jadwal ujian detail belum diterbitkan oleh panitia ujian sekolah.
        </p>
      </CardContent>
    </Card>

    <!-- Schedule Timelines -->
    <div v-else class="space-y-4">
      <Card 
        v-for="session in myScheduleData" 
        :key="session.id"
        class="border-border/60 shadow-xs hover:border-primary/40 transition-all"
      >
        <CardContent class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div class="space-y-1.5">
            <div class="flex items-center gap-2">
              <Badge variant="secondary" class="bg-indigo-50 text-indigo-700 font-extrabold text-xs">
                {{ session.event_title }}
              </Badge>
              <span class="text-xs text-muted-foreground">Sesi {{ session.session_number }}</span>
            </div>
            
            <div class="flex items-center gap-2 font-bold text-base text-foreground">
              <Calendar class="size-4 text-indigo-600" />
              <span>{{ formatDateIndo(session.exam_date) }}</span>
              <span class="text-muted-foreground font-normal text-xs">({{ session.start_time }} - {{ session.end_time }} WIB)</span>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <div 
              v-for="subj in session.subjects" 
              :key="subj.subject_id"
              class="px-3.5 py-2 rounded-xl bg-muted/40 border border-border/60 text-xs font-semibold text-foreground flex items-center gap-2"
            >
              <BookOpen class="size-3.5 text-indigo-500" />
              <span>{{ subj.subject_name }}</span>
              <Badge variant="outline" class="text-[10px] font-bold text-muted-foreground">
                Tingkat {{ subj.grade }}
              </Badge>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
