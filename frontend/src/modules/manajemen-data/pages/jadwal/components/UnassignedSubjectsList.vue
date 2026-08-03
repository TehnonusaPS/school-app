<script setup>
import { ref, watch } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { BookOpen, AlertCircle } from 'lucide-vue-next'
import { getUnassignedSubjects } from '@/services/scheduleService'

const props = defineProps({
  academicYearId: [String, Number],
  classroomId: [String, Number]
})

const unassignedList = ref([])
const isLoading = ref(false)

async function fetchUnassigned() {
  if (!props.academicYearId || !props.classroomId) {
    unassignedList.value = []
    return
  }
  isLoading.value = true
  try {
    const res = await getUnassignedSubjects({
      academic_year_id: props.academicYearId,
      classroom_id: props.classroomId
    })
    unassignedList.value = res.data
  } catch (err) {
    console.error('Failed to load unassigned subjects', err)
  } finally {
    isLoading.value = false
  }
}

watch(() => [props.academicYearId, props.classroomId], () => {
  fetchUnassigned()
}, { immediate: true })

defineExpose({
  refresh: fetchUnassigned
})
</script>

<template>
  <Card class="border border-border bg-card/60 backdrop-blur-md rounded-2xl shadow-sm overflow-hidden h-full flex flex-col">
    <div class="p-4 border-b border-border bg-muted/20 flex items-center gap-2">
      <BookOpen class="size-4 text-amber-500" />
      <span class="text-xs font-bold text-foreground">MAPEL BELUM DIJADWALKAN</span>
    </div>
    
    <CardContent class="p-4 flex-1 overflow-y-auto no-scrollbar">
      <div v-if="isLoading" class="text-center text-xs py-6 text-muted-foreground font-semibold">
        Memuat data mapel...
      </div>
      
      <div v-else-if="unassignedList.length === 0" class="text-center py-8 text-muted-foreground flex flex-col items-center justify-center gap-2">
        <AlertCircle class="size-8 text-emerald-500/60" />
        <p class="text-xs font-bold text-emerald-600">Semua Mapel Terjadwal</p>
        <p class="text-[10px] text-muted-foreground max-w-[150px] leading-relaxed">Seluruh mata pelajaran aktif di sekolah sudah dimasukkan ke jadwal kelas ini.</p>
      </div>
      
      <div v-else class="space-y-2.5">
        <div
          v-for="sub in unassignedList"
          :key="sub.id"
          class="flex items-center justify-between p-3 rounded-xl bg-background border hover:border-amber-500/40 transition-colors"
        >
          <div class="space-y-0.5 text-left">
            <p class="text-xs font-bold text-foreground leading-tight">{{ sub.name }}</p>
            <p class="text-[9px] font-mono text-muted-foreground">{{ sub.code }}</p>
          </div>
          <Badge variant="outline" class="bg-amber-500/10 text-amber-600 border-none font-bold text-[9px] px-2 py-0.5 rounded-full shrink-0">
            Belum Set
          </Badge>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
